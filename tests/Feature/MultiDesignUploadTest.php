<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\DesignAsset;
use App\Models\DesignJob;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MultiDesignUploadTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;
    protected User $designer;
    protected Branch $branch;
    protected Order $order;
    protected DesignJob $job;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->branch = Branch::create([
            'name' => 'Cabang Pusat',
            'code' => 'KTR',
            'address' => 'Jl. Pusat No. 1',
            'phone' => '08123456789',
        ]);

        $this->owner = User::create([
            'name' => 'Owner Antigravity',
            'email' => 'owner.test@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'owner',
            'branch_id' => null,
        ]);

        $this->designer = User::create([
            'name' => 'Designer Antigravity',
            'email' => 'designer.test@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'designer',
            'branch_id' => $this->branch->id,
        ]);

        $this->order = Order::create([
            'branch_id' => $this->branch->id,
            'user_id' => $this->designer->id,
            'order_number' => 'ORD-KTR-202611-0001',
            'customer_name' => 'Kopi Sejahtera',
            'customer_phone' => '08111222333',
            'order_type' => 'design_printing',
            'work_status' => 'in_progress',
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'total_amount' => 100000,
            'paid_amount' => 100000,
        ]);

        $this->job = DesignJob::create([
            'order_id' => $this->order->id,
            'designer_id' => $this->designer->id,
            'title' => 'Desain Spanduk 3x1 Kopi Sejahtera',
            'dimensions' => '300x100 cm',
        ]);
    }

    public function test_can_upload_single_file_legacy_compat(): void
    {
        $file = UploadedFile::fake()->create('spanduk_depan.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($this->designer)->post(route('workdesk.upload', $this->job->id), [
            'file' => $file,
            'file_type' => 'final_print',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('design_assets', [
            'design_job_id' => $this->job->id,
            'file_type' => 'final_print',
        ]);

        $this->order->refresh();
        $this->assertEquals('ready_to_print', $this->order->work_status);
    }

    public function test_can_upload_multiple_files_simultaneously(): void
    {
        $file1 = UploadedFile::fake()->create('spanduk_sisi_depan.pdf', 2048, 'application/pdf');
        $file2 = UploadedFile::fake()->create('spanduk_sisi_belakang.pdf', 2048, 'application/pdf');
        $file3 = UploadedFile::fake()->create('mockup_preview.jpg', 512, 'image/jpeg');

        $response = $this->actingAs($this->designer)->post(route('workdesk.upload', $this->job->id), [
            'files' => [$file1, $file2, $file3],
            'file_type' => 'final_print',
        ]);

        $response->assertSessionHas('success', '3 file desain berhasil diunggah! Status tiket otomatis diperbarui.');
        
        $this->assertEquals(3, DesignAsset::where('design_job_id', $this->job->id)->count());

        $this->order->refresh();
        $this->assertEquals('ready_to_print', $this->order->work_status);
    }

    public function test_ajax_multiple_upload_returns_json_summary(): void
    {
        $file1 = UploadedFile::fake()->create('banner_a.pdf', 1024, 'application/pdf');
        $file2 = UploadedFile::fake()->create('banner_b.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($this->designer)->postJson(route('workdesk.upload', $this->job->id), [
            'files' => [$file1, $file2],
            'file_type' => 'preview_proof',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'count' => 2,
        ]);

        $this->assertEquals(2, DesignAsset::where('design_job_id', $this->job->id)->where('file_type', 'preview_proof')->count());
    }

    public function test_order_show_view_contains_multiple_upload_support(): void
    {
        $response = $this->actingAs($this->owner)->get(route('orders.show', $this->order->id));

        $response->assertOk();
        $response->assertSee('name="files[]"', false);
        $response->assertSee('multiple', false);
    }

    public function test_order_show_view_renders_dispatch_all_button_only_when_final_print_assets_exist(): void
    {
        // 1. If only preview_proof exists, button should NOT show
        $proof = DesignAsset::create([
            'design_job_id' => $this->job->id,
            'uploaded_by' => $this->designer->id,
            'file_type' => 'preview_proof',
            'file_path' => 'designs/proof.jpg',
            'file_name' => 'proof.jpg',
            'file_size' => 1024,
        ]);

        $response = $this->actingAs($this->owner)->get(route('orders.show', $this->order->id));
        $response->assertOk();
        $response->assertDontSee('Kirim Berkas (All)');

        // 2. When final_print is added, button should appear
        DesignAsset::create([
            'design_job_id' => $this->job->id,
            'uploaded_by' => $this->designer->id,
            'file_type' => 'final_print',
            'file_path' => 'designs/test.pdf',
            'file_name' => 'test.pdf',
            'file_size' => 1024,
        ]);

        $response2 = $this->actingAs($this->owner)->get(route('orders.show', $this->order->id));
        $response2->assertOk();
        $response2->assertSee('Kirim Berkas (All)');
        $response2->assertSee('dispatchAllAssets()');
    }

    public function test_dispatch_all_assets_via_email_filters_only_final_print(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        // final_print file 1
        DesignAsset::create([
            'design_job_id' => $this->job->id,
            'uploaded_by' => $this->designer->id,
            'file_type' => 'final_print',
            'file_path' => 'designs/depan.pdf',
            'file_name' => 'depan.pdf',
            'file_size' => 1024,
        ]);

        // final_print file 2
        DesignAsset::create([
            'design_job_id' => $this->job->id,
            'uploaded_by' => $this->designer->id,
            'file_type' => 'final_print',
            'file_path' => 'designs/belakang.pdf',
            'file_name' => 'belakang.pdf',
            'file_size' => 1024,
        ]);

        // non-final_print file (should be ignored from print dispatch)
        DesignAsset::create([
            'design_job_id' => $this->job->id,
            'uploaded_by' => $this->designer->id,
            'file_type' => 'preview_proof',
            'file_path' => 'designs/mockup_preview.jpg',
            'file_name' => 'mockup_preview.jpg',
            'file_size' => 1024,
        ]);

        $response = $this->actingAs($this->owner)->postJson(route('vendor.dispatch.job.email', $this->job->id), [
            'vendor_email' => 'percetakan.rekanan@example.com',
            'notes' => 'Tolong cetak 2 sisi ya mas',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);

        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\VendorPrintDispatchMail::class, function ($mail) {
            return $mail->hasTo('percetakan.rekanan@example.com') 
                && count($mail->allAssets) === 2
                && collect($mail->allAssets)->every(fn($a) => $a->file_type === 'final_print');
        });
    }

    public function test_preview_job_email_renders_html_with_all_final_print_assets(): void
    {
        $asset1 = DesignAsset::create([
            'design_job_id' => $this->job->id,
            'uploaded_by' => $this->designer->id,
            'file_type' => 'final_print',
            'file_path' => 'designs/sisi_depan.pdf',
            'file_name' => 'sisi_depan.pdf',
            'file_size' => 1024,
        ]);

        $asset2 = DesignAsset::create([
            'design_job_id' => $this->job->id,
            'uploaded_by' => $this->designer->id,
            'file_type' => 'final_print',
            'file_path' => 'designs/sisi_belakang.pdf',
            'file_name' => 'sisi_belakang.pdf',
            'file_size' => 2048,
        ]);

        $response = $this->actingAs($this->owner)->get(route('vendor.dispatch.job.preview-email', $this->job->id));

        $response->assertOk();
        $response->assertSee('sisi_depan.pdf');
        $response->assertSee('sisi_belakang.pdf');
        $response->assertSee('Daftar Berkas Siap Cetak (Total 2 File)');
        $response->assertSee($asset1->share_url);
        $response->assertSee($asset2->share_url);
    }

    public function test_public_vendor_share_portal_renders_all_final_print_assets(): void
    {
        $asset1 = DesignAsset::create([
            'design_job_id' => $this->job->id,
            'uploaded_by' => $this->designer->id,
            'file_type' => 'final_print',
            'file_path' => 'designs/spanduk_depan.pdf',
            'file_name' => 'spanduk_depan.pdf',
            'file_size' => 1024,
        ]);

        $asset2 = DesignAsset::create([
            'design_job_id' => $this->job->id,
            'uploaded_by' => $this->designer->id,
            'file_type' => 'final_print',
            'file_path' => 'designs/spanduk_belakang.pdf',
            'file_name' => 'spanduk_belakang.pdf',
            'file_size' => 2048,
        ]);

        // Public visitor (unauthenticated) visits the share link
        $response = $this->get(route('vendor.share', $asset1->share_token));

        $response->assertOk();
        $response->assertSee('Spesifikasi Produksi');
        $response->assertSee('spanduk_depan.pdf');
        $response->assertSee('spanduk_belakang.pdf');
        $response->assertSee('Download Berkas');
    }
}
