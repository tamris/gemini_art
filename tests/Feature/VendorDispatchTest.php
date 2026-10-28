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
        $response->assertSee(route('vendor.share.download', $this->asset->share_token));
        $response->assertSee(route('vendor.share.preview', $this->asset->share_token));
    }
}
