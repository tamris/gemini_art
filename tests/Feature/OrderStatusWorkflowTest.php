<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CashMovement;
use App\Models\DesignJob;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OrderStatusWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $branch;
    protected User $owner;
    protected User $cashier;
    protected User $designer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::create([
            'name' => 'Gemini Art - Cabang Keturen',
            'code' => 'KTR',
            'address' => 'Jl. Pahlawan No. 45',
            'phone' => '081391497365',
        ]);

        $this->owner = User::create([
            'branch_id' => null,
            'name' => 'Budi Pratama (Owner)',
            'email' => 'owner@geminiart.test',
            'password' => Hash::make('password'),
            'role' => 'owner',
        ]);

        $this->cashier = User::create([
            'branch_id' => $this->branch->id,
            'name' => 'Siti Kasir',
            'email' => 'kasir@geminiart.test',
            'password' => Hash::make('password'),
            'role' => 'cashier',
        ]);

        $this->designer = User::create([
            'branch_id' => $this->branch->id,
            'name' => 'Amanda Desainer',
            'email' => 'amanda@geminiart.test',
            'password' => Hash::make('password'),
            'role' => 'designer',
        ]);
    }

    public function test_completed_order_is_locked_for_cashier(): void
    {
        $order = Order::create([
            'branch_id' => $this->branch->id,
            'user_id' => $this->cashier->id,
            'order_number' => 'ORD-KTR-202610-0001',
            'customer_name' => 'Customer A',
            'total_amount' => 50000,
            'paid_amount' => 50000,
            'payment_status' => 'paid',
            'work_status' => 'completed',
        ]);

        $response = $this->actingAs($this->cashier)
            ->post(route('orders.status', $order->id), [
                'work_status' => 'in_progress',
            ]);

        $response->assertSessionHas('error');
        $this->assertEquals('completed', $order->fresh()->work_status);
    }

    public function test_owner_can_reopen_completed_order(): void
    {
        $order = Order::create([
            'branch_id' => $this->branch->id,
            'user_id' => $this->cashier->id,
            'order_number' => 'ORD-KTR-202610-0002',
            'customer_name' => 'Customer B',
            'total_amount' => 50000,
            'paid_amount' => 50000,
            'payment_status' => 'paid',
            'work_status' => 'completed',
        ]);

        $response = $this->actingAs($this->owner)
            ->post(route('orders.status', $order->id), [
                'work_status' => 'reopen',
                'target_status' => 'revision',
                'reopen_reason' => 'Customer meminta perbaikan teks setelah mengambil barang.',
            ]);

        $response->assertSessionHas('success');
        $this->assertEquals('revision', $order->fresh()->work_status);
    }

    public function test_cannot_mark_as_completed_if_unpaid_balance_remains(): void
    {
        $order = Order::create([
            'branch_id' => $this->branch->id,
            'user_id' => $this->cashier->id,
            'order_number' => 'ORD-KTR-202610-0003',
            'customer_name' => 'Customer C',
            'total_amount' => 75000,
            'paid_amount' => 25000,
            'payment_status' => 'partial_dp',
            'work_status' => 'ready_to_print',
        ]);

        $response = $this->actingAs($this->cashier)
            ->post(route('orders.status', $order->id), [
                'work_status' => 'completed',
            ]);

        $response->assertSessionHas('error');
        $this->assertEquals('ready_to_print', $order->fresh()->work_status);
    }

    public function test_can_mark_as_completed_when_fully_paid(): void
    {
        $order = Order::create([
            'branch_id' => $this->branch->id,
            'user_id' => $this->cashier->id,
            'order_number' => 'ORD-KTR-202610-0004',
            'customer_name' => 'Customer D',
            'total_amount' => 100000,
            'paid_amount' => 100000,
            'payment_status' => 'paid',
            'work_status' => 'ready_to_print',
        ]);

        $response = $this->actingAs($this->cashier)
            ->post(route('orders.status', $order->id), [
                'work_status' => 'completed',
            ]);

        $response->assertSessionHas('success');
        $this->assertEquals('completed', $order->fresh()->work_status);
        $this->assertNotNull($order->fresh()->completed_at);
    }

    public function test_revision_status_requires_notes_and_increments_count(): void
    {
        $order = Order::create([
            'branch_id' => $this->branch->id,
            'user_id' => $this->cashier->id,
            'order_number' => 'ORD-KTR-202610-0005',
            'customer_name' => 'Customer E',
            'total_amount' => 50000,
            'paid_amount' => 25000,
            'payment_status' => 'partial_dp',
            'work_status' => 'in_progress',
        ]);

        $job = DesignJob::create([
            'order_id' => $order->id,
            'designer_id' => $this->designer->id,
            'title' => 'Spanduk Kedai Kopi',
            'revision_count' => 0,
        ]);

        // Missing notes should fail validation
        $failResponse = $this->actingAs($this->cashier)
            ->post(route('orders.status', $order->id), [
                'work_status' => 'revision',
                'revision_notes' => '',
            ]);
        $failResponse->assertSessionHasErrors('revision_notes');

        // Providing notes should succeed
        $successResponse = $this->actingAs($this->cashier)
            ->post(route('orders.status', $order->id), [
                'work_status' => 'revision',
                'revision_notes' => 'Ganti background jadi warna maroon dan font judul digedein.',
            ]);

        $successResponse->assertSessionHas('success');
        $this->assertEquals('revision', $order->fresh()->work_status);
        $this->assertEquals(1, $job->fresh()->revision_count);
        $this->assertStringContainsString('maroon', $job->fresh()->revision_notes);
    }

    public function test_cancellation_with_refund_creates_cash_movement_out(): void
    {
        $order = Order::create([
            'branch_id' => $this->branch->id,
            'user_id' => $this->cashier->id,
            'order_number' => 'ORD-KTR-202610-0006',
            'customer_name' => 'Customer F',
            'total_amount' => 80000,
            'paid_amount' => 30000,
            'payment_status' => 'partial_dp',
            'work_status' => 'in_progress',
        ]);

        $response = $this->actingAs($this->cashier)
            ->post(route('orders.status', $order->id), [
                'work_status' => 'canceled',
                'cancellation_reason' => 'Customer batal bikin banner karena acara ditiadakan.',
                'cancellation_type' => 'refund',
                'refund_method' => 'cash',
            ]);

        $response->assertSessionHas('success');
        $freshOrder = $order->fresh();
        $this->assertEquals('canceled', $freshOrder->work_status);
        $this->assertEquals('refund', $freshOrder->cancellation_type);

        $this->assertDatabaseHas('cash_movements', [
            'branch_id' => $this->branch->id,
            'order_id' => $order->id,
            'type' => 'out',
            'amount' => 30000,
            'category' => 'order_refund',
        ]);
    }

    public function test_cancellation_with_forfeit_does_not_create_cash_movement_out(): void
    {
        $order = Order::create([
            'branch_id' => $this->branch->id,
            'user_id' => $this->cashier->id,
            'order_number' => 'ORD-KTR-202610-0007',
            'customer_name' => 'Customer G',
            'total_amount' => 80000,
            'paid_amount' => 30000,
            'payment_status' => 'partial_dp',
            'work_status' => 'in_progress',
        ]);

        $response = $this->actingAs($this->cashier)
            ->post(route('orders.status', $order->id), [
                'work_status' => 'canceled',
                'cancellation_reason' => 'Bahan sudah terlanjur dipotong, DP hangus.',
                'cancellation_type' => 'forfeit',
            ]);

        $response->assertSessionHas('success');
        $freshOrder = $order->fresh();
        $this->assertEquals('canceled', $freshOrder->work_status);
        $this->assertEquals('forfeit', $freshOrder->cancellation_type);

        $this->assertDatabaseMissing('cash_movements', [
            'order_id' => $order->id,
            'type' => 'out',
        ]);
    }

    public function test_workdesk_prioritizes_revision_status(): void
    {
        $orderNormal = Order::create([
            'branch_id' => $this->branch->id,
            'user_id' => $this->cashier->id,
            'order_number' => 'ORD-KTR-202610-0008',
            'customer_name' => 'Customer H',
            'total_amount' => 50000,
            'paid_amount' => 50000,
            'payment_status' => 'paid',
            'work_status' => 'in_progress',
        ]);

        $jobNormal = DesignJob::create([
            'order_id' => $orderNormal->id,
            'designer_id' => $this->designer->id,
            'title' => 'Job Normal',
            'deadline_at' => now()->addHours(5),
        ]);

        $orderRevision = Order::create([
            'branch_id' => $this->branch->id,
            'user_id' => $this->cashier->id,
            'order_number' => 'ORD-KTR-202610-0009',
            'customer_name' => 'Customer I',
            'total_amount' => 50000,
            'paid_amount' => 50000,
            'payment_status' => 'paid',
            'work_status' => 'revision',
        ]);

        $jobRevision = DesignJob::create([
            'order_id' => $orderRevision->id,
            'designer_id' => $this->designer->id,
            'title' => 'Job Revisi Prioritas',
            'deadline_at' => now()->addHours(10), // Later deadline, but revision!
        ]);

        $response = $this->actingAs($this->designer)->get(route('workdesk.index'));
        $response->assertOk();

        $jobs = $response->viewData('jobs');
        // The first job in the paginated collection should be the revision job!
        $this->assertEquals($jobRevision->id, $jobs->first()->id);
    }

    public function test_can_create_order_with_datepicker_format(): void
    {
        $response = $this->actingAs($this->cashier)
            ->post(route('orders.store-design'), [
                'branch_id' => $this->branch->id,
                'customer_name' => 'Pak Joko',
                'customer_phone' => '081234567890',
                'title' => 'Spanduk Pecel Lele',
                'dimensions' => '3x1 meter',
                'material_notes' => 'Flexi Korea',
                'deadline_at' => '2026-10-30 17:00',
                'total_amount' => 60000,
                'paid_amount' => 30000,
                'payment_method' => 'cash',
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Pak Joko',
            'work_status' => 'in_progress',
        ]);
        $this->assertDatabaseHas('design_jobs', [
            'title' => 'Spanduk Pecel Lele',
            'deadline_at' => '2026-10-30 17:00:00',
        ]);
    }

    public function test_create_design_form_has_zero_default_for_paid_amount_and_no_zero_preset_button(): void
    {
        $response = $this->actingAs($this->cashier)
            ->get(route('orders.create-design'));

        $response->assertOk();
        // Paid amount display has default value="0" and hidden input value="0"
        $response->assertSee('id="paid_amount_display"', false);
        $response->assertSee('value="0"', false);
        // Does NOT show "Rp 0 (Belum Bayar)" preset button
        $response->assertDontSee('Rp 0 (Belum Bayar)');
        // Still has remaining presets
        $response->assertSee('Lunas 100%');
        $response->assertSee('DP 50%');
        $response->assertSee('DP 50rb');
        $response->assertSee('DP 100rb');
    }
}

