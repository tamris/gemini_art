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
    }

    public function test_owner_sidebar_does_not_contain_maintenance_menu(): void
    {
        $response = $this->actingAs($this->owner)->get(route('dashboard'));

        $response->assertOk();
        $response->assertDontSee('Mode Maintenance');
        $response->assertDontSee('/maintenance');
    }

    public function test_plain_artisan_down_renders_custom_indonesian_503_view_and_up_restores_access(): void
    {
        Artisan::call('down');
        $this->assertTrue(app()->maintenanceMode()->active());

        $guestRes = $this->get(route('login'));
        $guestRes->assertStatus(503);
        $guestRes->assertSee('GEMINI ART');
        $guestRes->assertSee('Sistem Sedang Dalam Pemeliharaan');
        $guestRes->assertSee('Order, Kas &amp; Arsip Aman', false);
        $guestRes->assertDontSee('Service Unavailable');

        $this->getJson('/dashboard')->assertStatus(503);

        Artisan::call('up');
        $this->assertFalse(app()->maintenanceMode()->active());

        $this->get(route('login'))->assertOk();
    }

    public function test_artisan_down_prerender_with_secret_and_retry_supports_developer_bypass(): void
    {
        Artisan::call('down', [
            '--render' => 'errors::503',
            '--secret' => 'dev-maint-2026',
            '--retry' => 900,
            '--refresh' => 60,
        ]);

        $this->assertTrue(app()->maintenanceMode()->active());

        // Public visitor gets pre-rendered 503 template with Retry-After and ETA
        $res = $this->get(route('login'));
        $res->assertStatus(503);
        $res->assertHeader('Retry-After', 900);
        $res->assertHeader('Refresh', 60);
        $res->assertSee('Sistem Sedang Dalam Pemeliharaan');
        $res->assertSee('15 menit');

        // Developer visits bypass URL to receive valid bypass cookie
        $secretRes = $this->get('/dev-maint-2026');
        $secretRes->assertRedirect('/');
        $cookie = $secretRes->getCookie('laravel_maintenance', false);
        $this->assertNotNull($cookie);
        $this->assertTrue(
            MaintenanceModeBypassCookie::isValid($cookie->getValue(), 'dev-maint-2026')
        );

        // Developer with bypass cookie can access app normally during maintenance
        $this->actingAs($this->owner)
            ->withUnencryptedCookie('laravel_maintenance', $cookie->getValue())
            ->get(route('dashboard'))
            ->assertOk();
    }
}
