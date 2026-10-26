<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\CashMovement;
use App\Models\DesignAsset;
use App\Models\DesignJob;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Branches
        $branchA = Branch::create([
            'name' => 'Gemini Art - Cabang Solo (CBA)',
            'code' => 'CBA',
            'address' => 'Jl. Slamet Riyadi No. 142, Surakarta',
            'phone' => '0812-3456-7890',
        ]);

        $branchB = Branch::create([
            'name' => 'Gemini Art - Cabang Jogja (CBB)',
            'code' => 'CBB',
            'address' => 'Jl. Gejayan No. 88, Sleman, D.I. Yogyakarta',
            'phone' => '0819-8765-4321',
        ]);

        // 2. Create Users
        $owner = User::create([
            'branch_id' => null, // Global access
            'name' => 'Budi Pratama (Owner)',
            'email' => 'owner@geminiart.test',
            'password' => Hash::make('password'),
            'role' => 'owner',
        ]);

        $cashierA = User::create([
            'branch_id' => $branchA->id,
            'name' => 'Siti Kasir Solo',
            'email' => 'kasir.solo@geminiart.test',
            'password' => Hash::make('password'),
            'role' => 'cashier',
        ]);

        $designerA = User::create([
            'branch_id' => $branchA->id,
            'name' => 'Rian Desainer Solo',
            'email' => 'desain.solo@geminiart.test',
            'password' => Hash::make('password'),
            'role' => 'designer',
        ]);

        $cashierB = User::create([
            'branch_id' => $branchB->id,
            'name' => 'Dewi Kasir Jogja',
            'email' => 'kasir.jogja@geminiart.test',
            'password' => Hash::make('password'),
            'role' => 'cashier',
        ]);

        $designerB = User::create([
            'branch_id' => $branchB->id,
            'name' => 'Adit Desainer Jogja',
            'email' => 'desain.jogja@geminiart.test',
            'password' => Hash::make('password'),
            'role' => 'designer',
        ]);

        // 3. Initial Cash Balances
        CashMovement::create([
            'branch_id' => $branchA->id,
            'user_id' => $cashierA->id,
            'type' => 'in',
            'amount' => 500000,
            'payment_method' => 'cash',
            'category' => 'initial_balance',
            'notes' => 'Kas awal laci kasir Solo',
        ]);

        CashMovement::create([
            'branch_id' => $branchB->id,
            'user_id' => $cashierB->id,
            'type' => 'in',
            'amount' => 500000,
            'payment_method' => 'cash',
            'category' => 'initial_balance',
            'notes' => 'Kas awal laci kasir Jogja',
        ]);

        // 4. Sample Design Orders for Branch A (Solo)
        // Order 1: Ready to print (DP paid, final file uploaded)
        $order1Num = 'ORD-CBA-' . date('Ym') . '-0001';
        $order1 = Order::create([
            'branch_id' => $branchA->id,
            'user_id' => $cashierA->id,
            'order_number' => $order1Num,
            'customer_name' => 'Kopi Senja Nusantara',
            'customer_phone' => '08123987112',
            'order_type' => 'design_printing',
            'total_amount' => 350000,
            'paid_amount' => 150000,
            'payment_status' => 'partial_dp',
            'work_status' => 'ready_to_print',
        ]);

        $job1 = DesignJob::create([
            'order_id' => $order1->id,
            'designer_id' => $designerA->id,
            'title' => 'Banner Spanduk Grand Opening 3x1m',
            'dimensions' => '3x1 meter',
            'material_notes' => 'Flexi Korea 380gr + Mata Ayam tiap sudut',
            'brief_notes' => 'Warna dominan cokelat tua dan warm amber. Sertakan promo Buy 1 Get 1 Free dan logo halal.',
            'deadline_at' => Carbon::tomorrow()->setHour(17)->setMinute(0),
        ]);

        // Dummy sample print asset file in storage
        $ym = date('Y-m');
        $dummyPath = "designs/CBA/{$ym}/{$order1Num}/print_spanduk_kopi_final.jpg";
        Storage::disk('public')->put($dummyPath, 'SIMULATED_HIGH_RES_PRINT_ASSET');

        DesignAsset::create([
            'design_job_id' => $job1->id,
            'uploaded_by' => $designerA->id,
            'file_type' => 'final_print',
            'file_path' => $dummyPath,
            'file_name' => 'print_spanduk_kopi_final.jpg',
            'file_size' => 14857600, // ~14.8MB
        ]);

        CashMovement::create([
            'branch_id' => $branchA->id,
            'order_id' => $order1->id,
            'user_id' => $cashierA->id,
            'type' => 'in',
            'amount' => 150000,
            'payment_method' => 'qris',
            'category' => 'order_dp',
            'notes' => "DP Order {$order1Num} via QRIS",
        ]);

        // Order 2: In Progress (Designer actively working on it)
        $order2Num = 'ORD-CBA-' . date('Ym') . '-0002';
        $order2 = Order::create([
            'branch_id' => $branchA->id,
            'user_id' => $cashierA->id,
            'order_number' => $order2Num,
            'customer_name' => 'Apotek Sehat Barokah',
            'customer_phone' => '08781234567',
            'order_type' => 'design_printing',
            'total_amount' => 250000,
            'paid_amount' => 250000,
            'payment_status' => 'paid',
            'work_status' => 'in_progress',
        ]);

        DesignJob::create([
            'order_id' => $order2->id,
            'designer_id' => $designerA->id,
            'title' => 'Brosur Pelayanan 3 Lipat (Trifold A4)',
            'dimensions' => 'A4 Lipat 3 (21x29.7cm)',
            'material_notes' => 'Art Paper 150gr Glossy',
            'brief_notes' => 'Warna biru medis dan putih. Cantumkan jadwal praktek dokter spesialis dan jam buka 24 jam.',
            'deadline_at' => Carbon::now()->addDays(2)->setHour(14)->setMinute(0),
        ]);

        CashMovement::create([
            'branch_id' => $branchA->id,
            'order_id' => $order2->id,
            'user_id' => $cashierA->id,
            'type' => 'in',
            'amount' => 250000,
            'payment_method' => 'cash',
            'category' => 'order_settlement',
            'notes' => "Pelunasan di awal {$order2Num} (Cash)",
        ]);

        // Order 3: Completed Order
        $order3Num = 'ORD-CBA-' . date('Ym') . '-0003';
        $order3 = Order::create([
            'branch_id' => $branchA->id,
            'user_id' => $cashierA->id,
            'order_number' => $order3Num,
            'customer_name' => 'Batik Sekar Wangi',
            'customer_phone' => '08571122334',
            'order_type' => 'design_printing',
            'total_amount' => 450000,
            'paid_amount' => 450000,
            'payment_status' => 'paid',
            'work_status' => 'completed',
        ]);

        $job3 = DesignJob::create([
            'order_id' => $order3->id,
            'designer_id' => $designerA->id,
            'title' => 'Roll Up Banner 60x160cm Pameran',
            'dimensions' => '60x160 cm',
            'material_notes' => 'Luster + Rangka Aluminium Roll Up',
            'brief_notes' => 'Pameran batik nusantara. Desain elegan minimalis motif parang.',
            'deadline_at' => Carbon::yesterday()->setHour(15)->setMinute(0),
        ]);

        CashMovement::create([
            'branch_id' => $branchA->id,
            'order_id' => $order3->id,
            'user_id' => $cashierA->id,
            'type' => 'in',
            'amount' => 450000,
            'payment_method' => 'transfer',
            'category' => 'order_settlement',
            'notes' => "Lunas Transfer Order {$order3Num}",
        ]);

        // Order 4: Retail POS (Fast Cashier Transaction)
        $order4Num = 'POS-CBA-' . date('Ym') . '-0001';
        $order4 = Order::create([
            'branch_id' => $branchA->id,
            'user_id' => $cashierA->id,
            'order_number' => $order4Num,
            'customer_name' => 'Pelanggan Walk-In',
            'customer_phone' => null,
            'order_type' => 'retail_pos',
            'total_amount' => 75000,
            'paid_amount' => 75000,
            'payment_status' => 'paid',
            'work_status' => 'completed',
        ]);

        OrderItem::create([
            'order_id' => $order4->id,
            'item_name' => 'Print Warna A4 HVS 80gr',
            'qty' => 15,
            'unit_price' => 2000,
            'subtotal' => 30000,
        ]);

        OrderItem::create([
            'order_id' => $order4->id,
            'item_name' => 'Jilid Spiral Kawat + Mika',
            'qty' => 3,
            'unit_price' => 15000,
            'subtotal' => 45000,
        ]);

        CashMovement::create([
            'branch_id' => $branchA->id,
            'order_id' => $order4->id,
            'user_id' => $cashierA->id,
            'type' => 'in',
            'amount' => 75000,
            'payment_method' => 'cash',
            'category' => 'retail_pos',
            'notes' => "Transaksi POS Kasir {$order4Num}",
        ]);

        // Branch A Petty cash out
        CashMovement::create([
            'branch_id' => $branchA->id,
            'user_id' => $cashierA->id,
            'type' => 'out',
            'amount' => 35000,
            'payment_method' => 'cash',
            'category' => 'petty_cash_expense',
            'notes' => 'Beli kertas lakban & air galon cabang',
        ]);

        // Branch B Sample Order
        $orderB1Num = 'ORD-CBB-' . date('Ym') . '-0001';
        $orderB1 = Order::create([
            'branch_id' => $branchB->id,
            'user_id' => $cashierB->id,
            'order_number' => $orderB1Num,
            'customer_name' => 'Gudeg Bu Tjitro Cab. Gejayan',
            'customer_phone' => '08132223334',
            'order_type' => 'design_printing',
            'total_amount' => 600000,
            'paid_amount' => 300000,
            'payment_status' => 'partial_dp',
            'work_status' => 'in_progress',
        ]);

        DesignJob::create([
            'order_id' => $orderB1->id,
            'designer_id' => $designerB->id,
            'title' => 'Buku Menu Restoran Hardcover Laminasi Doff',
            'dimensions' => 'A4 (10 halaman bolak-balik)',
            'material_notes' => 'Art Carton 260gr + Hardcover Greyboard',
            'brief_notes' => 'Foto makanan resolusi tinggi terlampir. Nuansa kayu & Jawa modern.',
            'deadline_at' => Carbon::now()->addDays(3)->setHour(16)->setMinute(0),
        ]);

        CashMovement::create([
            'branch_id' => $branchB->id,
            'order_id' => $orderB1->id,
            'user_id' => $cashierB->id,
            'type' => 'in',
            'amount' => 300000,
            'payment_method' => 'transfer',
            'category' => 'order_dp',
            'notes' => "DP Order {$orderB1Num} (BCA Transfer)",
        ]);
    }
}
