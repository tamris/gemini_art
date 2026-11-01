<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\CashMovement;
use App\Models\DesignJob;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = Branch::all();
        if ($branches->isEmpty()) {
            $this->command->error('Cabang belum ada.');
            return;
        }

        $branchKtr = $branches->firstWhere('code', 'KTR') ?? $branches->first();
        $branchKrt = $branches->firstWhere('code', 'KRT') ?? $branches->last();

        $owner = User::where('role', 'owner')->first();
        $designerKtr = User::where('role', 'designer')->where('branch_id', $branchKtr->id)->first() ?? $owner;
        $designerKrt = User::where('role', 'designer')->where('branch_id', $branchKrt->id)->first() ?? $designerKtr;

        $samples = [
            [
                'customer' => 'Kopi Kenangan Tegal',
                'phone' => '081223344551',
                'branch' => $branchKtr,
                'designer' => $designerKtr,
                'title' => 'Spanduk Promo Grand Opening 3x1',
                'dimensions' => '3 x 1 Meter',
                'material' => 'Flexi Korea 380gr',
                'brief' => 'Font tegas modern, warna dominan hitam kuning khas cafe.',
                'total' => 105000,
                'type' => 'paid',
                'work_status' => 'completed',
                'days_ago' => 8,
            ],
            [
                'customer' => 'Ayam Geprek Juara',
                'phone' => '081398765432',
                'branch' => $branchKtr,
                'designer' => $designerKtr,
                'title' => 'Buku Menu & Daftar Harga A3',
                'dimensions' => 'A3 (29.7 x 42 cm)',
                'material' => 'Art Paper 260gr Laminasi Doff',
                'brief' => 'Foto makanan resolusi tinggi, tata letak 3 kolom rapi.',
                'total' => 150000,
                'type' => 'paid',
                'work_status' => 'completed',
                'days_ago' => 7,
            ],
            [
                'customer' => 'Toko Berkah Abadi',
                'phone' => '087711223344',
                'branch' => $branchKrt,
                'designer' => $designerKrt,
                'title' => 'Stiker Label Kemasan Madu Hutan',
                'dimensions' => '5 x 5 cm (Cutting Bulat)',
                'material' => 'Vinyl Glossy Camel',
                'brief' => 'Nuansa emas dan coklat lebah, tulisan halal & PIRT jelas.',
                'total' => 120000,
                'type' => 'partial_dp',
                'work_status' => 'ready_to_print',
                'days_ago' => 6,
            ],
            [
                'customer' => 'CV Maju Jaya Abadi',
                'phone' => '082155667788',
                'branch' => $branchKtr,
                'designer' => $designerKtr,
                'title' => 'Nota 3 Rangkap & Kop Surat',
                'dimensions' => 'F4 (Buku Nota)',
                'material' => 'Kertas NCR 3 Ply (Putih, Merah, Kuning)',
                'brief' => 'Logo di kiri atas, nomor seri numerator 001-500.',
                'total' => 350000,
                'type' => 'paid',
                'work_status' => 'completed',
                'days_ago' => 6,
            ],
            [
                'customer' => 'Warung Makan Bu Siti',
                'phone' => '085677889900',
                'branch' => $branchKtr,
                'designer' => $designerKtr,
                'title' => 'Spanduk Soto Ayam Kampung',
                'dimensions' => '4 x 1 Meter',
                'material' => 'Flexi Standar 280gr',
                'brief' => 'Background kuning cerah, ada gambar mangkok soto lezat.',
                'total' => 80000,
                'type' => 'paid',
                'work_status' => 'completed',
                'days_ago' => 5,
            ],
            [
                'customer' => 'Bengkel Surya Motor',
                'phone' => '081900112233',
                'branch' => $branchKrt,
                'designer' => $designerKrt,
                'title' => 'Kartu Servis & X-Banner Promo Oli',
                'dimensions' => '60 x 160 cm',
                'material' => 'Albatros + Rangka X-Banner Fiber',
                'brief' => 'Tampilkan promo ganti oli gratis cuci motor.',
                'total' => 110000,
                'type' => 'partial_dp',
                'work_status' => 'in_progress',
                'days_ago' => 5,
            ],
            [
                'customer' => 'Klinik Sehat Pratama',
                'phone' => '081233445566',
                'branch' => $branchKtr,
                'designer' => $designerKtr,
                'title' => 'Brosur Layanan Medis Lipat 3',
                'dimensions' => 'A4 Lipat 3',
                'material' => 'Art Paper 150gr Full Color',
                'brief' => 'Gaya formal kesehatan, warna biru tosca dan putih.',
                'total' => 225000,
                'type' => 'paid',
                'work_status' => 'ready_to_print',
                'days_ago' => 4,
            ],
            [
                'customer' => 'Optik Melawai Tegal',
                'phone' => '082244556677',
                'branch' => $branchKrt,
                'designer' => $designerKrt,
                'title' => 'Poster Promo Frame Kacamata Korea',
                'dimensions' => 'A2 (42 x 59.4 cm)',
                'material' => 'Luster Paper Doff',
                'brief' => 'Foto model kacamata estetik, diskon 50% di-highlight.',
                'total' => 95000,
                'type' => 'paid',
                'work_status' => 'completed',
                'days_ago' => 4,
            ],
            [
                'customer' => 'Laundry Express Clean',
                'phone' => '085711223388',
                'branch' => $branchKtr,
                'designer' => $designerKtr,
                'title' => 'Stiker Fragile & Kupon Pelanggan',
                'dimensions' => 'Stiker 4x4 cm & Kartu Nama',
                'material' => 'Chromo Glossy & Karton Tebal 260',
                'brief' => 'Ada checklist parfum: Lavender, Sakura, Ocean Fresh.',
                'total' => 85000,
                'type' => 'unpaid',
                'work_status' => 'in_progress',
                'days_ago' => 3,
            ],
            [
                'customer' => 'Kedai Es Teh Manis Nusantara',
                'phone' => '081377889911',
                'branch' => $branchKrt,
                'designer' => $designerKrt,
                'title' => 'Booth Banner Stand Portabel 2x1',
                'dimensions' => '2 x 1 Meter',
                'material' => 'Flexi Korea 380gr',
                'brief' => 'Warna hijau segar es teh, ada logo daun teh.',
                'total' => 70000,
                'type' => 'paid',
                'work_status' => 'completed',
                'days_ago' => 3,
            ],
            [
                'customer' => 'Distro Sablon T-Shirt',
                'phone' => '087855443322',
                'branch' => $branchKtr,
                'designer' => $designerKtr,
                'title' => 'Hang Tag Label Baju & Stiker Die Cut',
                'dimensions' => 'Hangtag 4 x 8 cm',
                'material' => 'Art Carton 310gr Doff + Tali',
                'brief' => 'Konsep streetwear monochrome hitam putih.',
                'total' => 190000,
                'type' => 'partial_dp',
                'work_status' => 'revision',
                'days_ago' => 3,
            ],
            [
                'customer' => 'Salon Cantika Dewi',
                'phone' => '081299887766',
                'branch' => $branchKtr,
                'designer' => $designerKtr,
                'title' => 'Daftar Treatment & Kartu Member VIP',
                'dimensions' => 'A4 Standing Akrilik',
                'material' => 'Art Paper 260gr Doff',
                'brief' => 'Nuansa elegan rose gold dan pastel pink.',
                'total' => 135000,
                'type' => 'paid',
                'work_status' => 'ready_to_print',
                'days_ago' => 2,
            ],
            [
                'customer' => 'Toko Roti Sedap Wangi',
                'phone' => '082311998844',
                'branch' => $branchKrt,
                'designer' => $designerKrt,
                'title' => 'Stiker Segel Box Roti & Donat',
                'dimensions' => '2 x 10 cm (Stiker Pita Segel)',
                'material' => 'Vinyl Transparan Waterproof',
                'brief' => 'Teks Best Quality Fresh Baked Daily.',
                'total' => 90000,
                'type' => 'paid',
                'work_status' => 'completed',
                'days_ago' => 2,
            ],
            [
                'customer' => 'Rental Mobil Trans Tegal',
                'phone' => '085266778899',
                'branch' => $branchKtr,
                'designer' => $designerKtr,
                'title' => 'Kartu Nama Box & Brosur Wisata Guci',
                'dimensions' => 'Kartu Nama Standard 9x5.5 cm',
                'material' => 'Art Carton 260gr + Box Mika',
                'brief' => 'Daftar armada: Innova Zenix, Hiace Premio, Avanza.',
                'total' => 125000,
                'type' => 'partial_dp',
                'work_status' => 'in_progress',
                'days_ago' => 2,
            ],
            [
                'customer' => 'Apotek Berkah Sehat',
                'phone' => '081344556699',
                'branch' => $branchKrt,
                'designer' => $designerKrt,
                'title' => 'Plang Petunjuk Praktek Dokter',
                'dimensions' => '1.5 x 0.8 Meter',
                'material' => 'Akrilik Putih Susu 3mm + Stiker Oracal',
                'brief' => 'Jadwal praktek pagi & sore, SIP dokter dicantumkan.',
                'total' => 280000,
                'type' => 'paid',
                'work_status' => 'ready_to_print',
                'days_ago' => 1,
            ],
            [
                'customer' => 'Bimbel Bintang Prestasi',
                'phone' => '087799001122',
                'branch' => $branchKtr,
                'designer' => $designerKtr,
                'title' => 'Spanduk PPDB Siswa Baru 5x1 Meter',
                'dimensions' => '5 x 1 Meter',
                'material' => 'Flexi Standar 280gr',
                'brief' => 'Ada foto alumni lolos PTN favorit dan diskon pendaftaran.',
                'total' => 125000,
                'type' => 'paid',
                'work_status' => 'completed',
                'days_ago' => 1,
            ],
            [
                'customer' => 'Pet Shop Sahabat Satwa',
                'phone' => '085600112233',
                'branch' => $branchKrt,
                'designer' => $designerKrt,
                'title' => 'Spanduk Grooming & Pet Hotel',
                'dimensions' => '3 x 0.8 Meter',
                'material' => 'Flexi Korea 380gr',
                'brief' => 'Karakter anjing dan kucing lucu, warna pastel biru.',
                'total' => 84000,
                'type' => 'partial_dp',
                'work_status' => 'ready_to_print',
                'days_ago' => 1,
            ],
            [
                'customer' => 'Catering Selera Rasa',
                'phone' => '081277665544',
                'branch' => $branchKtr,
                'designer' => $designerKtr,
                'title' => 'Katalog Paket Prasmanan Pernikahan',
                'dimensions' => 'A4 Booklet 8 Halaman',
                'material' => 'Art Paper 150gr Cover Art Carton 260gr',
                'brief' => 'Nuansa mewah Jawa modern, daftar menu komplit A-D.',
                'total' => 320000,
                'type' => 'paid',
                'work_status' => 'completed',
                'days_ago' => 1,
            ],
            [
                'customer' => 'Fotocopy & ATK Keturen',
                'phone' => '089911223344',
                'branch' => $branchKtr,
                'designer' => $designerKtr,
                'title' => 'Spanduk Cetak Foto Kilat & Jilid Skripsi',
                'dimensions' => '2 x 1 Meter',
                'material' => 'Flexi Standar 280gr',
                'brief' => 'Tulisan Buka 24 Jam besar di kanan spanduk.',
                'total' => 50000,
                'type' => 'paid',
                'work_status' => 'completed',
                'days_ago' => 1,
            ],
            [
                'customer' => 'Barbershop Classic Cut',
                'phone' => '082188990011',
                'branch' => $branchKrt,
                'designer' => $designerKrt,
                'title' => 'Poster Hair Model 2026 & Kartu Cuci',
                'dimensions' => 'A2 Poster & Kartu Kupon',
                'material' => 'Luster Paper & Karton Brief Card',
                'brief' => 'Konsep vintage retro barber pole merah biru.',
                'total' => 95000,
                'type' => 'partial_dp',
                'work_status' => 'in_progress',
                'days_ago' => 0,
            ],
            [
                'customer' => 'Kedai Kopi Janji Rasa',
                'phone' => '081355443322',
                'branch' => $branchKtr,
                'designer' => $designerKtr,
                'title' => 'Stiker Cup Minuman & Standee Meja QRIS',
                'dimensions' => 'Stiker 5x5 cm & Akrilik A6',
                'material' => 'Vinyl Doff Waterproof',
                'brief' => 'Scan QRIS untuk pembayaran, logo kopi minimalis.',
                'total' => 85000,
                'type' => 'paid',
                'work_status' => 'ready_to_print',
                'days_ago' => 0,
            ],
            [
                'customer' => 'Toko Bangunan Sumber Rejeki',
                'phone' => '085722334455',
                'branch' => $branchKrt,
                'designer' => $designerKrt,
                'title' => 'Nota Penjualan Semen & Besi 2 Play',
                'dimensions' => 'Buku Nota 1/2 Folio',
                'material' => 'NCR 2 Ply 50 Set',
                'brief' => 'Kolom nomor, nama barang, qty, harga satuan, total.',
                'total' => 175000,
                'type' => 'paid',
                'work_status' => 'completed',
                'days_ago' => 0,
            ],
            [
                'customer' => 'Gym & Fitness Club Tegal',
                'phone' => '081266554433',
                'branch' => $branchKtr,
                'designer' => $designerKtr,
                'title' => 'Banner Motivasi Dinding 3x1.5 Meter',
                'dimensions' => '3 x 1.5 Meter',
                'material' => 'Flexi Korea High-Res',
                'brief' => 'Quotes NO PAIN NO GAIN dengan ilustrasi barbell atletis.',
                'total' => 157500,
                'type' => 'unpaid',
                'work_status' => 'draft',
                'days_ago' => 0,
            ],
            [
                'customer' => 'Florist Bunga Indah',
                'phone' => '087811998877',
                'branch' => $branchKrt,
                'designer' => $designerKrt,
                'title' => 'Kartu Ucapan Karangan Bunga Pernikahan',
                'dimensions' => 'A5 Lipat Kartu',
                'material' => 'Concorde Putih Bertekstur',
                'brief' => 'Watermark bunga mawar lembut, font kaligrafi indah.',
                'total' => 65000,
                'type' => 'paid',
                'work_status' => 'completed',
                'days_ago' => 0,
            ],
            [
                'customer' => 'Studio Foto Mahkota',
                'phone' => '085233445566',
                'branch' => $branchKtr,
                'designer' => $designerKtr,
                'title' => 'Cetak Kanvas Foto Keluarga 60x90cm',
                'dimensions' => '60 x 90 cm',
                'material' => 'Kanvas Katun Premium + Spanram Kayu',
                'brief' => 'Koreksi warna kulit natural, cetak kanvas tajam.',
                'total' => 275000,
                'type' => 'partial_dp',
                'work_status' => 'ready_to_print',
                'days_ago' => 0,
            ],
        ];

        $paymentMethods = ['cash', 'qris', 'transfer'];

        foreach ($samples as $i => $item) {
            $branch = $item['branch'];
            $createdAt = Carbon::now()->subDays($item['days_ago'])->subHours(rand(1, 8))->subMinutes(rand(5, 50));

            $orderNumber = Order::generateOrderNumber($branch);

            $paidAmount = 0;
            $paymentStatus = 'unpaid';

            if ($item['type'] === 'paid') {
                $paidAmount = $item['total'];
                $paymentStatus = 'paid';
            } elseif ($item['type'] === 'partial_dp') {
                $paidAmount = (float) round($item['total'] * 0.5);
                $paymentStatus = 'partial_dp';
            }

            $order = Order::create([
                'branch_id' => $branch->id,
                'user_id' => $owner->id,
                'order_number' => $orderNumber,
                'customer_name' => $item['customer'],
                'customer_phone' => $item['phone'],
                'order_type' => 'design_printing',
                'total_amount' => $item['total'],
                'paid_amount' => $paidAmount,
                'payment_status' => $paymentStatus,
                'work_status' => $item['work_status'],
                'completed_at' => $item['work_status'] === 'completed' ? $createdAt->copy()->addHours(6) : null,
            ]);

            $order->created_at = $createdAt;
            $order->updated_at = $createdAt;
            $order->save();

            // Create DesignJob
            $job = DesignJob::create([
                'order_id' => $order->id,
                'designer_id' => $item['designer']->id,
                'title' => $item['title'],
                'dimensions' => $item['dimensions'],
                'material_notes' => $item['material'],
                'brief_notes' => $item['brief'],
                'deadline_at' => $createdAt->copy()->addDays(2),
            ]);
            $job->created_at = $createdAt;
            $job->updated_at = $createdAt;
            $job->save();

            // Create Cash Movements
            $method = $paymentMethods[$i % count($paymentMethods)];

            if ($paymentStatus === 'paid') {
                if ($i % 3 === 0) {
                    // Paid with DP first then settlement
                    $dp = round($item['total'] * 0.4);
                    $settle = $item['total'] - $dp;

                    $cm1 = CashMovement::create([
                        'branch_id' => $branch->id,
                        'order_id' => $order->id,
                        'user_id' => $owner->id,
                        'type' => 'in',
                        'amount' => $dp,
                        'payment_method' => $method,
                        'category' => 'order_dp',
                        'notes' => "DP Order {$order->order_number}",
                    ]);
                    $cm1->created_at = $createdAt;
                    $cm1->updated_at = $createdAt;
                    $cm1->save();

                    $cm2 = CashMovement::create([
                        'branch_id' => $branch->id,
                        'order_id' => $order->id,
                        'user_id' => $owner->id,
                        'type' => 'in',
                        'amount' => $settle,
                        'payment_method' => ($method === 'cash') ? 'qris' : 'cash',
                        'category' => 'order_settlement',
                        'notes' => "Pelunasan sisa order {$order->order_number}",
                    ]);
                    $cm2->created_at = $createdAt->copy()->addHours(3);
                    $cm2->updated_at = $createdAt->copy()->addHours(3);
                    $cm2->save();
                } else {
                    // Full direct payment
                    $cm = CashMovement::create([
                        'branch_id' => $branch->id,
                        'order_id' => $order->id,
                        'user_id' => $owner->id,
                        'type' => 'in',
                        'amount' => $item['total'],
                        'payment_method' => $method,
                        'category' => 'order_settlement',
                        'notes' => "Pembayaran lunas order {$order->order_number}",
                    ]);
                    $cm->created_at = $createdAt;
                    $cm->updated_at = $createdAt;
                    $cm->save();
                }
            } elseif ($paymentStatus === 'partial_dp') {
                $cm = CashMovement::create([
                    'branch_id' => $branch->id,
                    'order_id' => $order->id,
                    'user_id' => $owner->id,
                    'type' => 'in',
                    'amount' => $paidAmount,
                    'payment_method' => $method,
                    'category' => 'order_dp',
                    'notes' => "DP Order {$order->order_number}",
                ]);
                $cm->created_at = $createdAt;
                $cm->updated_at = $createdAt;
                $cm->save();
            }
        }

        // Additional Cash In: Kasir Ritel POS (ATK, Cetak Dokumen, Print Foto kilat)
        $retailItems = [
            ['title' => 'Penjualan Kertas HVS A4 & Map Business', 'amount' => 45000, 'branch' => $branchKtr, 'days' => 5],
            ['title' => 'Cetak Dokumen PDF & Scan KTP Kilat', 'amount' => 25000, 'branch' => $branchKrt, 'days' => 4],
            ['title' => 'Jilid Spiral Kawat & Mika Bening 5 Buku', 'amount' => 60000, 'branch' => $branchKtr, 'days' => 3],
            ['title' => 'Laminating Sertifikat & Piagam 10 Lembar', 'amount' => 50000, 'branch' => $branchKrt, 'days' => 2],
            ['title' => 'Cetak Pas Foto 4x6 & 3x4 Warna Kilat', 'amount' => 35000, 'branch' => $branchKtr, 'days' => 1],
            ['title' => 'Penjualan Lakban Coklat & Amplop Coklat Tebal', 'amount' => 30000, 'branch' => $branchKrt, 'days' => 0],
        ];

        foreach ($retailItems as $rit) {
            $created = Carbon::now()->subDays($rit['days'])->subHours(rand(2, 6));
            $cm = CashMovement::create([
                'branch_id' => $rit['branch']->id,
                'order_id' => null,
                'user_id' => $owner->id,
                'type' => 'in',
                'amount' => $rit['amount'],
                'payment_method' => 'cash',
                'category' => 'retail_pos',
                'notes' => $rit['title'],
            ]);
            $cm->created_at = $created;
            $cm->updated_at = $created;
            $cm->save();
        }

        // Additional Cash Out: Petty Cash Expenses (Operasional Toko / Cabang)
        $expenseItems = [
            ['title' => 'Beli Lakban Bening 6 Roll & Pisau Cutter Cabang', 'amount' => 55000, 'branch' => $branchKtr, 'days' => 6],
            ['title' => 'Beli Air Galon Aqua & Kopi Gula Operator', 'amount' => 40000, 'branch' => $branchKrt, 'days' => 4],
            ['title' => 'Kertas Thermal Kasir POS 10 Roll', 'amount' => 45000, 'branch' => $branchKtr, 'days' => 3],
            ['title' => 'Makan Siang Lembur Operator Percetakan', 'amount' => 75000, 'branch' => $branchKrt, 'days' => 2],
            ['title' => 'Beli Lampu LED Meja Desain Keturen', 'amount' => 35000, 'branch' => $branchKtr, 'days' => 1],
        ];

        foreach ($expenseItems as $exp) {
            $created = Carbon::now()->subDays($exp['days'])->subHours(rand(1, 4));
            $cm = CashMovement::create([
                'branch_id' => $exp['branch']->id,
                'order_id' => null,
                'user_id' => $owner->id,
                'type' => 'out',
                'amount' => $exp['amount'],
                'payment_method' => 'cash',
                'category' => 'petty_cash_expense',
                'notes' => $exp['title'],
            ]);
            $cm->created_at = $created;
            $cm->updated_at = $created;
            $cm->save();
        }
    }
}

