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
        $response->assertDontSee('Kirim Berkas');
        $response->assertDontSee('onclick="dispatchAllAssets()"', false);

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
        $response2->assertSee('Kirim Berkas');
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

    public function test_orders_show_replaces_per_file_send_with_delete_and_hides_when_locked(): void
    {
        $asset = DesignAsset::create([
            'design_job_id' => $this->job->id,
            'uploaded_by' => $this->designer->id,
            'file_type' => 'final_print',
            'file_path' => 'designs/banner_toko.pdf',
            'file_name' => 'banner_toko.pdf',
            'file_size' => 2048,
        ]);

        // Unlocked order: shows Kirim Berkas + delete button trigger, no per-row assetId dispatch
        $response = $this->actingAs($this->designer)->get(route('orders.show', $this->order->id));
        $response->assertOk();
        $response->assertSee('Kirim Berkas');
        $response->assertSee('dispatchAllAssets()');
        $response->assertSee('Hapus Berkas (Jika Salah Unggah)');
        $response->assertSee("action: '" . route('archive.destroy', $asset->id) . "'", false);
        $response->assertDontSee("assetId: {$asset->id}");

        // Locked order: hides delete trigger (preview/download stay available)
        $this->order->update(['work_status' => 'completed']);
        $lockedResponse = $this->actingAs($this->owner)->get(route('orders.show', $this->order->id));
        $lockedResponse->assertOk();
        $lockedResponse->assertDontSee('Hapus Berkas (Jika Salah Unggah)');
        $lockedResponse->assertDontSee("action: '" . route('archive.destroy', $asset->id) . "'", false);
    }

    public function test_authorized_staff_can_delete_asset_and_removes_physical_file(): void
    {
        Storage::disk('public')->put('designs/salah_upload.pdf', 'dummy-pdf-content');

        $asset = DesignAsset::create([
            'design_job_id' => $this->job->id,
            'uploaded_by' => $this->designer->id,
            'disk' => 'public',
            'file_type' => 'preview_proof',
            'file_path' => 'designs/salah_upload.pdf',
            'file_name' => 'salah_upload.pdf',
            'file_size' => 1024,
        ]);

        Storage::disk('public')->assertExists('designs/salah_upload.pdf');

        $response = $this->actingAs($this->designer)
            ->deleteJson(route('archive.destroy', $asset->id));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'status_reverted' => false,
            ]);

        $this->assertDatabaseMissing('design_assets', ['id' => $asset->id]);
        Storage::disk('public')->assertMissing('designs/salah_upload.pdf');
    }

    public function test_deleting_last_final_print_asset_reverts_ready_to_print_order_to_in_progress(): void
    {
        $this->order->update(['work_status' => 'ready_to_print']);
        Storage::disk('public')->put('designs/sole_final.pdf', 'final-content');

        $asset = DesignAsset::create([
            'design_job_id' => $this->job->id,
            'uploaded_by' => $this->designer->id,
            'disk' => 'public',
            'file_type' => 'final_print',
            'file_path' => 'designs/sole_final.pdf',
            'file_name' => 'sole_final.pdf',
            'file_size' => 4096,
        ]);

        $response = $this->actingAs($this->designer)
            ->deleteJson(route('archive.destroy', $asset->id));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'status_reverted' => true,
            ]);

        $this->assertDatabaseMissing('design_assets', ['id' => $asset->id]);
        Storage::disk('public')->assertMissing('designs/sole_final.pdf');
        $this->assertSame('in_progress', $this->order->fresh()->work_status);
    }

    public function test_deleting_one_of_multiple_final_print_or_preview_keeps_ready_to_print_status(): void
    {
        $this->order->update(['work_status' => 'ready_to_print']);
        Storage::disk('public')->put('designs/final_1.pdf', 'one');
        Storage::disk('public')->put('designs/final_2.pdf', 'two');
        Storage::disk('public')->put('designs/preview.jpg', 'prev');

        $final1 = DesignAsset::create([
            'design_job_id' => $this->job->id,
            'uploaded_by' => $this->designer->id,
            'disk' => 'public',
            'file_type' => 'final_print',
            'file_path' => 'designs/final_1.pdf',
            'file_name' => 'final_1.pdf',
            'file_size' => 1024,
        ]);

        DesignAsset::create([
            'design_job_id' => $this->job->id,
            'uploaded_by' => $this->designer->id,
            'disk' => 'public',
            'file_type' => 'final_print',
            'file_path' => 'designs/final_2.pdf',
            'file_name' => 'final_2.pdf',
            'file_size' => 1024,
        ]);

        $preview = DesignAsset::create([
            'design_job_id' => $this->job->id,
            'uploaded_by' => $this->designer->id,
            'disk' => 'public',
            'file_type' => 'preview_proof',
            'file_path' => 'designs/preview.jpg',
            'file_name' => 'preview.jpg',
            'file_size' => 512,
        ]);

        // Deleting 1 of 2 final_print assets keeps ready_to_print
        $res1 = $this->actingAs($this->designer)
            ->deleteJson(route('archive.destroy', $final1->id));
        $res1->assertOk()->assertJson(['success' => true, 'status_reverted' => false]);
        $this->assertSame('ready_to_print', $this->order->fresh()->work_status);

        // Deleting preview_proof keeps ready_to_print
        $res2 = $this->actingAs($this->designer)
            ->deleteJson(route('archive.destroy', $preview->id));
        $res2->assertOk()->assertJson(['success' => true, 'status_reverted' => false]);
        $this->assertSame('ready_to_print', $this->order->fresh()->work_status);
    }

    public function test_cross_branch_staff_cannot_delete_asset(): void
    {
        $otherBranch = Branch::create([
            'name' => 'Cabang Lain',
            'code' => 'KMT',
            'address' => 'Jl. Lain No. 2',
            'phone' => '08129999999',
        ]);

        $otherDesigner = User::create([
            'name' => 'Other Designer',
            'email' => 'other.designer@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'designer',
            'branch_id' => $otherBranch->id,
        ]);

        Storage::disk('public')->put('designs/branch_protected.pdf', 'protected');

        $asset = DesignAsset::create([
            'design_job_id' => $this->job->id,
            'uploaded_by' => $this->designer->id,
            'disk' => 'public',
            'file_type' => 'final_print',
            'file_path' => 'designs/branch_protected.pdf',
            'file_name' => 'branch_protected.pdf',
            'file_size' => 1024,
        ]);

        $response = $this->actingAs($otherDesigner)
            ->deleteJson(route('archive.destroy', $asset->id));

        $response->assertForbidden();
        $this->assertDatabaseHas('design_assets', ['id' => $asset->id]);
        Storage::disk('public')->assertExists('designs/branch_protected.pdf');
    }

    public function test_cannot_delete_asset_when_order_is_locked(): void
    {
        Storage::disk('public')->put('designs/locked_final.pdf', 'locked-content');

        $asset = DesignAsset::create([
            'design_job_id' => $this->job->id,
            'uploaded_by' => $this->designer->id,
            'disk' => 'public',
            'file_type' => 'final_print',
            'file_path' => 'designs/locked_final.pdf',
            'file_name' => 'locked_final.pdf',
            'file_size' => 2048,
        ]);

        // Completed order blocks JSON delete (422)
        $this->order->update(['work_status' => 'completed']);
        $jsonRes = $this->actingAs($this->owner)
            ->deleteJson(route('archive.destroy', $asset->id));
        $jsonRes->assertStatus(422)
            ->assertJson(['success' => false]);
        $this->assertDatabaseHas('design_assets', ['id' => $asset->id]);
        Storage::disk('public')->assertExists('designs/locked_final.pdf');

        // Canceled order blocks standard HTTP delete (session error)
        $this->order->update(['work_status' => 'canceled']);
        $httpRes = $this->actingAs($this->owner)
            ->delete(route('archive.destroy', $asset->id));
        $httpRes->assertSessionHas('error');
        $this->assertDatabaseHas('design_assets', ['id' => $asset->id]);
        Storage::disk('public')->assertExists('designs/locked_final.pdf');
    }
}
