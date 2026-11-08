<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CashMovement;
use App\Models\DesignAsset;
use App\Models\DesignJob;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PurgeDemoOrdersCommandTest extends TestCase
{
    use RefreshDatabase;

    protected User $cashier;
    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->branch = Branch::create([
            'name' => 'Cabang Kramat',
            'code' => 'KRT',
            'address' => 'Jl. Kramat Raya',
            'phone' => '08123456789',
        ]);

        $this->cashier = User::create([
            'name' => 'Kasir Demo',
            'email' => 'kasir@demo.test',
            'role' => 'cashier',
            'branch_id' => $this->branch->id,
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
    }

    public function test_purge_single_order_by_number(): void
    {
        $order = Order::create([
            'branch_id' => $this->branch->id,
            'user_id' => $this->cashier->id,
            'order_number' => 'KRT-DEMO-001',
            'customer_name' => 'Klien Demo',
            'order_type' => 'design_printing',
            'total_amount' => 50000,
            'paid_amount' => 50000,
            'payment_status' => 'paid',
            'work_status' => 'in_progress',
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'item_name' => 'Banner Demo 1x1m',
            'qty' => 1,
            'unit_price' => 50000,
            'subtotal' => 50000,
        ]);

        $job = DesignJob::create([
            'order_id' => $order->id,
            'title' => 'Banner Demo',
        ]);

        $filePath = 'designs/KRT/2026-11/KRT-DEMO-001/print_sample.pdf';
        Storage::disk('public')->put($filePath, 'sample-print-content');

        DesignAsset::create([
            'design_job_id' => $job->id,
            'uploaded_by' => $this->cashier->id,
            'file_type' => 'final_print',
            'file_path' => $filePath,
            'disk' => 'public',
            'file_name' => 'print_sample.pdf',
            'file_size' => 1024,
        ]);

        CashMovement::create([
            'branch_id' => $this->branch->id,
            'user_id' => $this->cashier->id,
            'order_id' => $order->id,
            'type' => 'in',
            'category' => 'order_settlement',
            'amount' => 50000,
            'payment_method' => 'cash',
            'description' => 'Pembayaran demo',
        ]);

        // Verifikasi sebelum dihapus
        $this->assertDatabaseHas('orders', ['order_number' => 'KRT-DEMO-001']);
        Storage::disk('public')->assertExists($filePath);

        // Jalankan perintah hapus single order dengan flag --force
        $this->artisan('orders:purge', [
            '--number' => 'KRT-DEMO-001',
            '--force' => true,
        ])->assertSuccessful();

        // Verifikasi setelah dihapus
        $this->assertDatabaseMissing('orders', ['order_number' => 'KRT-DEMO-001']);
        $this->assertDatabaseMissing('order_items', ['id' => $item->id]);
        $this->assertDatabaseMissing('design_jobs', ['id' => $job->id]);
        $this->assertDatabaseMissing('cash_movements', ['order_id' => $order->id]);
        Storage::disk('public')->assertMissing($filePath);
    }

    public function test_purge_all_demo_orders(): void
    {
        // Buat 2 order demo
        for ($i = 1; $i <= 2; $i++) {
            $order = Order::create([
                'branch_id' => $this->branch->id,
                'user_id' => $this->cashier->id,
                'order_number' => "KRT-DEMO-00{$i}",
                'customer_name' => "Klien Demo {$i}",
                'order_type' => 'design_printing',
                'total_amount' => 100000,
                'paid_amount' => 100000,
                'payment_status' => 'paid',
                'work_status' => 'completed',
            ]);

            CashMovement::create([
                'branch_id' => $this->branch->id,
                'user_id' => $this->cashier->id,
                'order_id' => $order->id,
                'type' => 'in',
                'category' => 'order_settlement',
                'amount' => 100000,
                'payment_method' => 'cash',
                'description' => "Pembayaran demo {$i}",
            ]);
        }

        $this->assertEquals(2, Order::count());
        $this->assertEquals(2, CashMovement::count());

        // Jalankan perintah purge all
        $this->artisan('orders:purge', [
            '--all' => true,
            '--force' => true,
        ])->assertSuccessful();

        // Verifikasi database order dan kas bersih
        $this->assertEquals(0, Order::count());
        $this->assertEquals(0, CashMovement::count());

        // User & Branch tetap utuh!
        $this->assertDatabaseHas('branches', ['code' => 'KRT']);
        $this->assertDatabaseHas('users', ['email' => 'kasir@demo.test']);
    }
}
