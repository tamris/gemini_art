<?php

namespace Tests\Feature;

use App\Mail\VendorPrintDispatchMail;
use App\Models\Branch;
use App\Models\DesignAsset;
use App\Models\DesignJob;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VendorDispatchTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;
    protected DesignAsset $asset;
    protected string $shareToken;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $branch = Branch::create([
            'name' => 'Gemini Art - Cabang Kramat',
            'code' => 'KRT',
            'address' => 'Jl. Garuda No. 12',
            'phone' => '08123456789',
        ]);

        $this->owner = User::create([
            'branch_id' => null,
            'name' => 'Budi Pratama (Owner)',
            'email' => 'owner@geminiart.test',
            'password' => Hash::make('password'),
            'role' => 'owner',
        ]);

        $order = Order::create([
            'branch_id' => $branch->id,
            'user_id' => $this->owner->id,
            'order_number' => 'ORD-KRT-202610-0001',
            'customer_name' => 'Bpk Kusan',
            'customer_phone' => '08954002613',
            'order_type' => 'design_printing',
            'total_amount' => 50000,
            'paid_amount' => 50000,
            'payment_status' => 'paid',
            'work_status' => 'ready_to_print',
        ]);

        $job = DesignJob::create([
            'order_id' => $order->id,
            'title' => 'Spanduk Banner Bansos',
            'dimensions' => '3 x 1.5 Meter',
            'material_notes' => 'Flexi Korea 440gr',
            'brief_notes' => 'Mata ayam 4 sudut, keliling lipat rapi.',
        ]);

        $this->asset = DesignAsset::create([
            'design_job_id' => $job->id,
            'uploaded_by' => $this->owner->id,
            'file_type' => 'final_print',
            'file_path' => 'designs/KRT/2026-10/ORD-KRT-202610-0001/print_banner_bansos.pdf',
            'disk' => 'public',
            'file_name' => 'print_banner_bansos.pdf',
            'file_size' => 102400,
            'share_token' => 'valid-vendor-token-1234567890abcdef',
        ]);

        $this->shareToken = $this->asset->share_token;
    }

    public function test_public_vendor_can_view_share_page_without_auth(): void
    {
        // Guest accessing share portal
        $response = $this->get(route('vendor.share', $this->shareToken));
        $response->assertOk();
        $response->assertSee($this->asset->designJob->order->order_number);
        $response->assertSee($this->asset->file_name);
        $response->assertSee(route('vendor.share.download', $this->shareToken));
        $response->assertSee('3 x 1.5 Meter');
        $response->assertSee('Flexi Korea 440gr');
        $response->assertSee('Jumlah Cetak (Qty)');
    }

    public function test_public_vendor_can_download_file_without_auth(): void
    {
        // Create fake file in storage
        Storage::disk($this->asset->disk ?: 'public')->put($this->asset->file_path, 'dummy print ready content');

        // Guest downloading file
        $response = $this->get(route('vendor.share.download', $this->shareToken));
        $response->assertOk();
    }

    public function test_invalid_token_returns_404(): void
    {
        $response = $this->get(route('vendor.share', 'non-existent-token-12345'));
        $response->assertNotFound();
    }

    public function test_owner_can_dispatch_email_to_vendor(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->owner)->postJson(route('vendor.dispatch.email', $this->asset->id), [
            'vendor_email' => 'rekanan.percetakan@example.com',
            'notes' => 'Tolong diprioritaskan cetak sore ini.',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);

        Mail::assertSent(VendorPrintDispatchMail::class, function ($mail) {
            return $mail->hasTo('rekanan.percetakan@example.com') &&
                   $mail->asset->id === $this->asset->id;
        });
    }

    public function test_owner_can_preview_dispatch_email(): void
    {
        $response = $this->actingAs($this->owner)->get(route('vendor.dispatch.preview-email', $this->asset->id));
        $response->assertOk();
        $response->assertSee($this->asset->designJob->order->order_number);
        $response->assertSee('ORDER PRODUKSI PERCETAKAN');
        $response->assertSee('Jumlah Cetak (Qty)');
        $response->assertSee(route('vendor.share.download', $this->asset->share_token));
        $response->assertSee(route('vendor.share.preview', $this->asset->share_token));
    }

    public function test_vendor_email_and_share_portal_hide_min_area_billing_note_and_show_actual_size(): void
    {
        $this->asset->designJob->update([
            'dimensions' => '0.56 x 0.56 m (0.31 m², Min. 1 m²)',
        ]);

        $emailPreview = $this->actingAs($this->owner)->get(route('vendor.dispatch.preview-email', $this->asset->id));
        $emailPreview->assertOk();
        $emailPreview->assertSee('0.56 x 0.56 m (0.31 m²)');
        $emailPreview->assertDontSee('Min. 1 m²');

        $sharePage = $this->get(route('vendor.share', $this->shareToken));
        $sharePage->assertOk();
        $sharePage->assertSee('0.56 x 0.56 m (0.31 m²)');
        $sharePage->assertDontSee('Min. 1 m²');
    }

    public function test_order_badges_show_pending_only_when_final_print_exists_and_hide_for_retail_or_no_final_print(): void
    {
        $orderWithFinal = $this->asset->designJob->order;

        // Retail POS order -> no email badge
        $retailOrder = Order::create([
            'branch_id' => $orderWithFinal->branch_id,
            'user_id' => $this->owner->id,
            'order_number' => 'ORD-KRT-202610-0091',
            'customer_name' => 'Walk-in Retail',
            'order_type' => 'retail_pos',
            'total_amount' => 15000,
            'paid_amount' => 15000,
            'payment_status' => 'paid',
            'work_status' => 'completed',
        ]);

        // Design printing order with no final_print asset -> no email badge
        $draftOrder = Order::create([
            'branch_id' => $orderWithFinal->branch_id,
            'user_id' => $this->owner->id,
            'order_number' => 'ORD-KRT-202610-0092',
            'customer_name' => 'Draft Order',
            'order_type' => 'design_printing',
            'total_amount' => 25000,
            'paid_amount' => 25000,
            'payment_status' => 'paid',
            'work_status' => 'in_progress',
        ]);
        DesignJob::create([
            'order_id' => $draftOrder->id,
            'title' => 'Draft Only',
        ]);

        $this->assertEquals('pending', $orderWithFinal->fresh()->email_dispatch_state['status']);
        $this->assertNull($retailOrder->fresh()->email_dispatch_state);
        $this->assertEquals('none', $draftOrder->fresh()->email_dispatch_state['status']);

        $indexResponse = $this->actingAs($this->owner)->get(route('orders.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('Belum Email');

        $draftShow = $this->actingAs($this->owner)->get(route('orders.show', $draftOrder));
        $draftShow->assertOk();
        $draftShow->assertDontSee('Belum Kirim Email');
        $draftShow->assertDontSee('Email Terkirim');
    }

    public function test_sending_job_email_records_dispatch_metadata_and_shows_email_terkirim_badge(): void
    {
        Mail::fake();

        $job = $this->asset->designJob;
        $order = $job->order;

        $response = $this->actingAs($this->owner)->postJson(route('vendor.dispatch.job.email', $job->id), [
            'vendor_email' => 'cetak@percetakan.test',
            'notes' => 'Cetak 2x finishing mata ayam.',
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        Mail::assertSent(VendorPrintDispatchMail::class, 1);

        $job->refresh();
        $this->assertNotNull($job->vendor_email_sent_at);
        $this->assertSame('cetak@percetakan.test', $job->vendor_email_sent_to);
        $this->assertSame($this->owner->id, $job->vendor_email_sent_by);
        $this->assertSame(1, $job->vendor_email_sent_count);
        $this->assertSame([$this->asset->id], $job->vendor_email_sent_asset_ids);

        $indexResponse = $this->actingAs($this->owner)->get(route('orders.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('Terkirim');
        $indexResponse->assertDontSee('Belum Email');

        $showResponse = $this->actingAs($this->owner)->get(route('orders.show', $order));
        $showResponse->assertOk();
        $showResponse->assertSee('Email Terkirim');
        $showResponse->assertSee('cetak@percetakan.test');
    }

    public function test_failed_vendor_email_send_does_not_mark_order_as_sent(): void
    {
        Mail::shouldReceive('to')
            ->once()
            ->with('gagal@percetakan.test')
            ->andThrow(new \RuntimeException('SMTP connection refused'));

        $job = $this->asset->designJob;
        $order = $job->order;

        $response = $this->actingAs($this->owner)->postJson(route('vendor.dispatch.job.email', $job->id), [
            'vendor_email' => 'gagal@percetakan.test',
        ]);

        $response->assertStatus(500)->assertJson(['success' => false]);

        $job->refresh();
        $this->assertNull($job->vendor_email_sent_at);
        $this->assertNull($job->vendor_email_sent_to);
        $this->assertSame(0, $job->vendor_email_sent_count);
        $this->assertSame('pending', $order->fresh()->email_dispatch_state['status']);
    }

    public function test_option_a_triggers_needs_resend_on_new_file_revision_or_file_deletion_until_resent(): void
    {
        Mail::fake();

        $job = $this->asset->designJob;
        $order = $job->order;

        // 1. Initial send -> sent
        $this->actingAs($this->owner)->postJson(route('vendor.dispatch.job.email', $job->id), [
            'vendor_email' => 'cetak@percetakan.test',
        ])->assertOk();
        $this->assertSame('sent', $order->fresh()->email_dispatch_state['status']);

        // 2. Order switches to revision -> Revisi · Kirim Ulang
        $order->update(['work_status' => 'revision']);
        $revState = $order->fresh()->email_dispatch_state;
        $this->assertSame('needs_resend', $revState['status']);
        $this->assertSame('revision', $revState['reason']);
        $this->assertSame('Revisi · Kirim Ulang', $revState['label']);

        $showRevision = $this->actingAs($this->owner)->get(route('orders.show', $order));
        $showRevision->assertOk()->assertSee('Revisi · Kirim Ulang');

        // 3. New final_print uploaded (and status becomes ready_to_print) -> File Baru · Kirim Ulang
        $order->update(['work_status' => 'ready_to_print']);
        Storage::disk('public')->put('designs/spanduk_v2.jpg', 'fake-image-v2');
        $newAsset = DesignAsset::create([
            'design_job_id' => $job->id,
            'uploaded_by' => $this->owner->id,
            'file_name' => 'spanduk_v2.jpg',
            'file_path' => 'designs/spanduk_v2.jpg',
            'file_size' => 2048,
            'file_type' => 'final_print',
            'share_token' => 'test-token-v2-' . uniqid(),
            'share_expires_at' => now()->addDays(7),
        ]);

        $newFileState = $order->fresh()->email_dispatch_state;
        $this->assertSame('needs_resend', $newFileState['status']);
        $this->assertSame('new_file', $newFileState['reason']);
        $this->assertSame('File Baru · Kirim Ulang', $newFileState['label']);

        // 4. Resend all files -> Email Terkirim again & count increments to 2
        $this->actingAs($this->owner)->postJson(route('vendor.dispatch.job.email', $job->id), [
            'vendor_email' => 'cetak@percetakan.test',
        ])->assertOk();

        $job->refresh();
        $this->assertSame(2, $job->vendor_email_sent_count);
        $this->assertSame('sent', $order->fresh()->email_dispatch_state['status']);

        // 5. Delete one sent final_print file -> File Dihapus · Kirim Ulang
        $newAsset->delete();
        $deletedState = $order->fresh()->email_dispatch_state;
        $this->assertSame('needs_resend', $deletedState['status']);
        $this->assertSame('file_deleted', $deletedState['reason']);
        $this->assertSame('File Dihapus · Kirim Ulang', $deletedState['label']);
    }
}
