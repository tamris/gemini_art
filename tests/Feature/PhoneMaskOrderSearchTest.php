<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhoneMaskOrderSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_creation_stores_formatted_phone_and_supports_flexible_search(): void
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

        $order = Order::create([
            'branch_id' => $branch->id,
            'user_id' => $owner->id,
            'order_number' => 'ORD-KTR-202610-0099',
            'customer_name' => 'Bpk. Hendra',
            'customer_phone' => '0823-2323-2323',
            'order_type' => 'design_printing',
            'total_amount' => 100000,
            'paid_amount' => 50000,
            'payment_status' => 'partial_dp',
            'work_status' => 'draft',
        ]);

        // Search with exact dashed format
        $response1 = $this->actingAs($owner)->get(route('orders.index', ['search' => '0823-2323-2323']));
        $response1->assertStatus(200);
        $response1->assertSee('ORD-KTR-202610-0099');

        // Search with raw un-dashed format
        $response2 = $this->actingAs($owner)->get(route('orders.index', ['search' => '082323232323']));
        $response2->assertStatus(200);
        $response2->assertSee('ORD-KTR-202610-0099');
    }
}
