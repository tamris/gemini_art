<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CashMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashMovementDateFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_cash_index_renders_with_neo_datepicker_and_filters_by_date(): void
    {
        $branch = Branch::create([
            'name' => 'Cabang Keturen',
            'code' => 'KTR',
            'address' => 'Jl. Veteran No. 10',
            'is_active' => true,
        ]);

        $owner = User::create([
            'name' => 'Budi Pratama',
            'email' => 'owner@geminiart.test',
            'password' => bcrypt('password'),
            'role' => 'owner',
            'branch_id' => $branch->id,
            'is_active' => true,
        ]);

        // Create movements on different dates
        $movementToday = CashMovement::create([
            'branch_id' => $branch->id,
            'user_id' => $owner->id,
            'type' => 'in',
            'category' => 'order_settlement',
            'payment_method' => 'cash',
            'amount' => 75000,
            'notes' => 'Pelunasan hari ini',
            'created_at' => '2026-10-28 10:00:00',
        ]);

        $movementYesterday = CashMovement::create([
            'branch_id' => $branch->id,
            'user_id' => $owner->id,
            'type' => 'in',
            'category' => 'order_dp',
            'payment_method' => 'cash',
            'amount' => 50000,
            'notes' => 'DP kemarin',
        ]);
        $movementYesterday->created_at = '2026-10-27 15:00:00';
        $movementYesterday->save();

        // 1. Visit without date filter -> both appear
        $response = $this->actingAs($owner)->get(route('cash.index'));
        $response->assertStatus(200);
        $response->assertSee('data-neo-datepicker-container', false);
        $response->assertSee('Pelunasan hari ini');
        $response->assertSee('DP kemarin');

        // 2. Filter by 2026-10-28 -> only today appears
        $responseFiltered = $this->actingAs($owner)->get(route('cash.index', ['date' => '2026-10-28']));
        $responseFiltered->assertStatus(200);
        $responseFiltered->assertSee('Pelunasan hari ini');
        $responseFiltered->assertDontSee('DP kemarin');
        $responseFiltered->assertSee('28/10/2026');
    }
}
