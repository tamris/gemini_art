<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Http\MaintenanceModeBypassCookie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MaintenanceModeTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;
    protected User $cashier;
    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ensureSystemUp();

        $this->branch = Branch::create([
            'name' => 'Cabang Pusat',
            'code' => 'KTR',
            'address' => 'Jl. Pusat No. 1',
            'phone' => '08123456789',
        ]);

        $this->owner = User::create([
            'name' => 'Owner Gemini Art',
            'email' => 'owner.maint@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'owner',
            'branch_id' => null,
        ]);

        $this->cashier = User::create([
            'name' => 'Kasir Gemini Art',
            'email' => 'kasir.maint@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'cashier',
            'branch_id' => $this->branch->id,
        ]);
    }

    protected function tearDown(): void
    {
        $this->ensureSystemUp();

        parent::tearDown();
    }

    private function ensureSystemUp(): void
    {
        if (app()->maintenanceMode()->active()) {
            Artisan::call('up');
        }

        $metaPath = storage_path('app/maintenance-meta.json');
        if (is_file($metaPath)) {
            @unlink($metaPath);
        }
    }

    public function test_only_owner_can_access_maintenance_control_and_preview(): void
    {
        $this->actingAs($this->cashier)->get(route('maintenance.index'))->assertForbidden();
        $this->actingAs($this->cashier)->get(route('maintenance.preview'))->assertForbidden();
        $this->actingAs($this->cashier)->post(route('maintenance.down'), [])->assertForbidden();
        $this->actingAs($this->cashier)->post(route('maintenance.up'))->assertForbidden();

        $indexRes = $this->actingAs($this->owner)->get(route('maintenance.index'));
        $indexRes->assertOk();
        $indexRes->assertSee('Mode Maintenance Aplikasi');
        $indexRes->assertSee('Sistem Online');

        $previewRes = $this->actingAs($this->owner)->get(route('maintenance.preview'));
        $previewRes->assertOk();
        $previewRes->assertSee('Sistem Sedang Dalam Pemeliharaan');
        $previewRes->assertSee('PRATINJAU HALAMAN MAINTENANCE (503)');
    }

    public function test_owner_can_activate_maintenance_mode_and_receives_valid_bypass_cookie(): void
    {
        $response = $this->actingAs($this->owner)->post(route('maintenance.down'), [
            'message' => 'Sedang upgrade PHP dan database server cabang.',
            'eta_minutes' => 20,
            'secret' => 'gemini-secret-2026',
        ]);

        $response->assertRedirect(route('maintenance.index'));
        $response->assertSessionHas('success');
        $this->assertTrue(app()->maintenanceMode()->active());

        // Bypass cookie must NOT be encrypted so PreventRequestsDuringMaintenance can validate it
        $cookie = $response->getCookie('laravel_maintenance', false);
        $this->assertNotNull($cookie);
        $this->assertTrue(
            MaintenanceModeBypassCookie::isValid($cookie->getValue(), 'gemini-secret-2026')
        );

        // Owner with unencrypted bypass cookie continues accessing the panel cleanly
        $bypassed = $this->actingAs($this->owner)
            ->withUnencryptedCookie('laravel_maintenance', $cookie->getValue())
            ->get(route('maintenance.index'));

        $bypassed->assertOk();
        $bypassed->assertSee('MODE MAINTENANCE SEDANG AKTIF');
        $bypassed->assertSee(url('/gemini-secret-2026'));
    }

    public function test_visitors_receive_503_maintenance_page_with_prerendered_message_and_secret_bypass_works(): void
    {
        $this->actingAs($this->owner)->post(route('maintenance.down'), [
            'message' => 'Sedang merapikan storage arsip cloud.',
            'eta_minutes' => 15,
            'secret' => 'owner-unlock-99',
        ])->assertRedirect();

        // Guest request without bypass cookie gets HTTP 503 with pre-rendered Indonesian template
        $guestRes = $this->get(route('login'));
        $guestRes->assertStatus(503);
        $guestRes->assertSee('GEMINI ART');
        $guestRes->assertSee('Sistem Sedang Dalam Pemeliharaan');
        $guestRes->assertSee('Sedang merapikan storage arsip cloud.');
        $guestRes->assertSee('15 menit');
        $guestRes->assertDontSee('PRATINJAU HALAMAN MAINTENANCE');

        // JSON request without bypass cookie receives HTTP 503 JSON
        $this->getJson('/dashboard')->assertStatus(503);

        // Secret bypass route grants maintenance bypass cookie
        $secretRes = $this->get('/owner-unlock-99');
        $secretRes->assertRedirect('/');
        $secretCookie = $secretRes->getCookie('laravel_maintenance', false);
        $this->assertNotNull($secretCookie);
        $this->assertTrue(
            MaintenanceModeBypassCookie::isValid($secretCookie->getValue(), 'owner-unlock-99')
        );
    }

    public function test_plain_artisan_down_renders_custom_indonesian_503_view(): void
    {
        Artisan::call('down');
        $this->assertTrue(app()->maintenanceMode()->active());

        $res = $this->get(route('login'));
        $res->assertStatus(503);
        $res->assertSee('Sistem Sedang Dalam Pemeliharaan');
        $res->assertSee('Order, Kas &amp; Arsip Aman', false);
        $res->assertDontSee('Service Unavailable');
    }

    public function test_validates_secret_against_reserved_routes_and_invalid_characters(): void
    {
        $reservedRes = $this->actingAs($this->owner)->post(route('maintenance.down'), [
            'secret' => 'dashboard',
        ]);
        $reservedRes->assertSessionHasErrors('secret');
        $this->assertFalse(app()->maintenanceMode()->active());

        $invalidCharsRes = $this->actingAs($this->owner)->post(route('maintenance.down'), [
            'secret' => 'ada spasi',
        ]);
        $invalidCharsRes->assertSessionHasErrors('secret');
        $this->assertFalse(app()->maintenanceMode()->active());
    }

    public function test_owner_can_deactivate_maintenance_mode_and_restore_public_access(): void
    {
        $downRes = $this->actingAs($this->owner)->post(route('maintenance.down'), [
            'secret' => 'unlock-gemini-123',
        ]);
        $cookie = $downRes->getCookie('laravel_maintenance', false);
        $this->assertTrue(app()->maintenanceMode()->active());

        $upRes = $this->actingAs($this->owner)
            ->withUnencryptedCookie('laravel_maintenance', $cookie->getValue())
            ->from(route('maintenance.index'))
            ->post(route('maintenance.up'));

        $upRes->assertRedirect(route('maintenance.index'));
        $upRes->assertSessionHas('success');
        $this->assertFalse(app()->maintenanceMode()->active());

        // Owner can still load dashboard normally
        $this->actingAs($this->owner)->get(route('dashboard'))->assertOk();

        // Public visitor (logged out) can load login normally again
        auth()->logout();
        $this->get(route('login'))->assertOk();
    }
}
