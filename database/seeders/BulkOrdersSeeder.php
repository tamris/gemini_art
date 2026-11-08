<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\CashMovement;
use App\Models\DesignJob;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BulkOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds for 50 rich orders.
     */
    public function run(): void
    {
        $branches = Branch::all();
        if ($branches->isEmpty()) {
            $branches = collect([
                Branch::create([
                    'name' => 'Cabang Keturen (Pusat)',
                    'code' => 'KTR',
                    'address' => 'Jl. Merdeka No. 10, Tegal',
                    'phone' => '081234567890',
                ]),
                Branch::create([
                    'name' => 'Cabang Kraton',
                    'code' => 'KRT',
                    'address' => 'Jl. Sultan Agung No. 45, Tegal',
                    'phone' => '081234567891',
                ]),
            ]);
        }

        $owner = User::where('role', 'owner')->first();
        if (!$owner) {
            $owner = User::create([
                'name' => 'Budi Pratama (Owner)',
                'email' => 'owner@geminiart.test',
                'password' => bcrypt('password'),
                'role' => 'owner',
                'is_active' => true,
            ]);
        }

        $designers = User::where('role', 'designer')->get();
        if ($designers->isEmpty()) {
            $designers = collect([
                User::create([
                    'name' => 'Fajar Desainer',
                    'email' => 'designer.ktr@geminiart.test',
                    'password' => bcrypt('password'),
                    'role' => 'designer',
                    'branch_id' => $branches->first()->id,
                    'is_active' => true,
                ]),
                User::create([
                    'name' => 'Rian Kreatif',
                    'email' => 'designer.krt@geminiart.test',
                    'password' => bcrypt('password'),
                    'role' => 'designer',
                    'branch_id' => $branches->last()->id,
                    'is_active' => true,
                ]),
            ]);
        }

        $cashiers = User::where('role', 'cashier')->get();
        if ($cashiers->isEmpty()) {
            $cashiers = collect([$owner]);
        }

        $orderTemplates = [
            // 1
            [
                'customer' => 'Kopi Janji Manis Tegal',
                'phone' => '081223344111',
                'title' => 'Spanduk Outdoor Grand Opening Kopi 4x1m',
                'unit' => 'Meter',
                'qty' => 4,
                'unit_price' => 30000,
                'unit_cost' => 16000, // HPP Lengkap
                'dimensions' => '4 x 1 Meter',
                'material' => 'Flexi Korea 380gr Premium',
                'brief' => 'Font tegas estetik, paduan warna hitam-emas, cantumkan promo Buy 1 Get 1.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'qris',
                'days_ago' => 26,
            ],
            // 2
            [
                'customer' => 'SMK Bina Nusantara',
                'phone' => '081398765002',
                'title' => 'Brosur Penerimaan Siswa Baru (PPDB) A4 Lipat 3',
                'unit' => 'Rim',
                'qty' => 2,
                'unit_price' => 280000,
                'unit_cost' => null, // HPP Belum Lengkap (Nota belum masuk)
                'dimensions' => 'A4 Lipat Tiga',
                'material' => 'Art Paper 150gr Full Color',
                'brief' => 'Tampilkan foto fasilitas laboratorium komputer, kejuruan otomotif, dan diskon pendaftar pertama.',
                'work_status' => 'ready_to_print',
                'payment' => 'partial_dp',
                'method' => 'transfer',
                'days_ago' => 25,
            ],
            // 3
            [
                'customer' => 'Dapur Bunda Zahra',
                'phone' => '087711223003',
                'title' => 'Stiker Label Kemasan Sambal Cumi Roa',
                'unit' => 'Lembar A3',
                'qty' => 15,
                'unit_price' => 12000,
                'unit_cost' => 6500,
                'dimensions' => 'Diameter 5 cm (Kiss Cut Bulat)',
                'material' => 'Vinyl Glossy Camel Water-resistant',
                'brief' => 'Warna merah cabai menyala, cantumkan logo halal MUI dan nomor P-IRT.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'cash',
                'days_ago' => 24,
            ],
            // 4
            [
                'customer' => 'Klinik Cantik Ayu Estetika',
                'phone' => '082155667004',
                'title' => 'Roll-Up Banner Promosi Treatment Laser Glow',
                'unit' => 'Set',
                'qty' => 2,
                'unit_price' => 185000,
                'unit_cost' => 110000,
                'dimensions' => '80 x 200 cm',
                'material' => 'Luster Paper Doff + Stand Roll-up Aluminium',
                'brief' => 'Nuansa pastel pink dan putih, foto model sebelum & sesudah perawatan.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'transfer',
                'days_ago' => 24,
            ],
            // 5
            [
                'customer' => 'Bengkel Mobil Sentosa Jaya',
                'phone' => '085677889005',
                'title' => 'Buku Nota Servis 3 Rangkap & Formulir Checkup',
                'unit' => 'Buku',
                'qty' => 10,
                'unit_price' => 38000,
                'unit_cost' => 22000,
                'dimensions' => 'F4 Setengah (Folio / 2)',
                'material' => 'Kertas NCR 3 Ply (Putih, Merah, Kuning)',
                'brief' => 'Header logo bengkel di kiri atas, nomorator cetak merah dari 001-500.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'cash',
                'days_ago' => 23,
            ],
            // 6
            [
                'customer' => 'Drg. Felicia Anggraini',
                'phone' => '081900112006',
                'title' => 'Plang Papan Nama Praktek Dokter Gigi',
                'unit' => 'Pcs',
                'qty' => 1,
                'unit_price' => 350000,
                'unit_cost' => null, // HPP Belum Ada
                'dimensions' => '120 x 80 cm',
                'material' => 'Akrilik Putih Susu 3mm + Huruf Timbul Stiker Oracal',
                'brief' => 'Sesuai regulasi IDGI: Nama dokter, gelar, SIP/STR, dan jam praktek pagi sore.',
                'work_status' => 'in_progress',
                'payment' => 'partial_dp',
                'method' => 'transfer',
                'days_ago' => 22,
            ],
            // 7
            [
                'customer' => 'CV Multi Artha Notaris',
                'phone' => '081233445007',
                'title' => 'Kartu Nama Eksekutif Emboss Gold Foil',
                'unit' => 'Box',
                'qty' => 5,
                'unit_price' => 55000,
                'unit_cost' => 28000,
                'dimensions' => '9 x 5.5 cm',
                'material' => 'Art Carton 310gr Laminasi Doff + Foil Emas',
                'brief' => 'Desain minimalis berwibawa, logo timbul emas pada lambang timbangan.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'qris',
                'days_ago' => 21,
            ],
            // 8
            [
                'customer' => 'Toko Kue Mama Mia',
                'phone' => '082244556008',
                'title' => 'Stiker Segel Toples Nastar & Kastengel',
                'unit' => 'Lembar A3',
                'qty' => 20,
                'unit_price' => 11000,
                'unit_cost' => 6000,
                'dimensions' => 'Panjang 15 x 2.5 cm (Bentuk Lidah Segel)',
                'material' => 'Bontax Camel Glossy Die Cut',
                'brief' => 'Warna hijau tosca pastel dengan ilustrasi kue kering estetik.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'cash',
                'days_ago' => 21,
            ],
            // 9
            [
                'customer' => 'Barbershop The Gentlemen',
                'phone' => '085711223009',
                'title' => 'Spanduk Banner Depan Kios 3x1m',
                'unit' => 'Meter',
                'qty' => 3,
                'unit_price' => 28000,
                'unit_cost' => 15000,
                'dimensions' => '3 x 1 Meter',
                'material' => 'Flexi Standar 280gr',
                'brief' => 'Kombinasi corak garis barber pole vintage, cantumkan harga potong rambut mulai Rp20.000.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'cash',
                'days_ago' => 20,
            ],
            // 10
            [
                'customer' => 'Komunitas Scooterist Pantura',
                'phone' => '081377889010',
                'title' => 'Kaos Gathering Anniversary Sablon DTF',
                'unit' => 'Pcs',
                'qty' => 35,
                'unit_price' => 68000,
                'unit_cost' => null, // Belum masuk nota vendor konveksi
                'dimensions' => 'Ukuran M - XXL Campur',
                'material' => 'Cotton Combed 30s Reaktif + DTF High Definition',
                'brief' => 'Gambar siluet vespa klasik menyusuri jalur pantura dengan tulisan Tegal Punya Cerita.',
                'work_status' => 'in_progress',
                'payment' => 'partial_dp',
                'method' => 'transfer',
                'days_ago' => 20,
            ],
            // 11
            [
                'customer' => 'Resto Seafood Muara Indah',
                'phone' => '087855443011',
                'title' => 'Buku Menu Hardcover Spiral Kawat A4',
                'unit' => 'Buku',
                'qty' => 6,
                'unit_price' => 85000,
                'unit_cost' => 45000,
                'dimensions' => 'A4 (8 Lembar Bolak-balik)',
                'material' => 'Art Paper 260gr Laminasi Doff Anti Air + Ring Spiral Besi',
                'brief' => 'Foto kepiting saus tiram & udang bakar ukuran jumbo, menu paket rombongan.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'transfer',
                'days_ago' => 19,
            ],
            // 12
            [
                'customer' => 'BEM Politeknik Harapan Bersama',
                'phone' => '081299887012',
                'title' => 'Sertifikat Seminar Karir & Magang BUMN',
                'unit' => 'Lembar',
                'qty' => 150,
                'unit_price' => 3500,
                'unit_cost' => 1800,
                'dimensions' => 'A4 (21 x 29.7 cm)',
                'material' => 'Concorde Tebal 230gr Tekstur Serat',
                'brief' => 'Border formal universitas, space tanda tangan dekan & ketua panitia rapi.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'qris',
                'days_ago' => 18,
            ],
            // 13
            [
                'customer' => 'Boutique Hijab Syari Humaira',
                'phone' => '085211223013',
                'title' => 'Stiker Cutting Sandblast Kaca Toko',
                'unit' => 'Meter',
                'qty' => 5,
                'unit_price' => 65000,
                'unit_cost' => null, // Belum diinput
                'dimensions' => 'Tinggi 1.2m x Panjang 4m',
                'material' => 'Stiker Sandblast Takki Joss Es Buram',
                'brief' => 'Motif ornamen islami elegan di bagian tengah kaca etalase toko.',
                'work_status' => 'ready_to_print',
                'payment' => 'paid',
                'method' => 'transfer',
                'days_ago' => 18,
            ],
            // 14
            [
                'customer' => 'Panitia Pemilihan Desa Mejasem',
                'phone' => '081344556014',
                'title' => 'ID Card Panitia & Tali Lanyard Printing 2cm',
                'unit' => 'Set',
                'qty' => 40,
                'unit_price' => 17500,
                'unit_cost' => 9500,
                'dimensions' => 'ID Card PVC tebal kartu ATM + Tali Fullprint Kait Besi',
                'material' => 'PVC Card Instant + Tali Tissue Lembut',
                'brief' => 'Warna biru dongker kemendagri, foto panitia dan barcode pengenal.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'cash',
                'days_ago' => 17,
            ],
            // 15
            [
                'customer' => 'Cuci Motor Salju Barokah',
                'phone' => '087799001015',
                'title' => 'Spanduk Outdoor Cuci Salju & Semir Ban 3x1m',
                'unit' => 'Meter',
                'qty' => 3,
                'unit_price' => 28000,
                'unit_cost' => 15000,
                'dimensions' => '3 x 1 Meter',
                'material' => 'Flexi 280gr Finishing Mata Ayam Tiap Sudut',
                'brief' => 'Warna biru muda berbusa salju, ada gambar motor bersih mengkilap.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'cash',
                'days_ago' => 16,
            ],
            // 16
            [
                'customer' => 'Puskesmas Tegal Timur',
                'phone' => '085600112016',
                'title' => 'Poster Edukasi Pencegahan DBD & Stunting A2',
                'unit' => 'Lembar',
                'qty' => 25,
                'unit_price' => 18000,
                'unit_cost' => null, // Belum HPP
                'dimensions' => 'A2 (42 x 59.4 cm)',
                'material' => 'Art Carton 210gr Laminasi Glossy',
                'brief' => 'Infografis 3M Plus, karakter kartun keluarga sehat ceria, logo Kemenkes RI.',
                'work_status' => 'in_progress',
                'payment' => 'unpaid',
                'method' => 'transfer',
                'days_ago' => 16,
            ],
            // 17
            [
                'customer' => 'Koperasi Pegawai Republik Indonesia (KPRI)',
                'phone' => '081277665017',
                'title' => 'Kalender Dinding 2027 Klem Seng 3 Lembar',
                'unit' => 'Pcs',
                'qty' => 200,
                'unit_price' => 14000,
                'unit_cost' => 8200,
                'dimensions' => '38 x 53 cm (Kalender Triwulan)',
                'material' => 'Kertas AP 120gr + Jepit Kaleng Seng Sengit',
                'brief' => 'Ada jadwal cuti bersama, pasaran Jawa (Pon, Wage, Kliwon, Legi, Pahing).',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'transfer',
                'days_ago' => 15,
            ],
            // 18
            [
                'customer' => 'Toko Bangunan Sumber Logam',
                'phone' => '089911223018',
                'title' => 'Branding Stiker Mobil Pick-up GranMax 2 Sisi',
                'unit' => 'Set',
                'qty' => 1,
                'unit_price' => 850000,
                'unit_cost' => 450000,
                'dimensions' => 'Pintu Kanan, Kiri & Bak Belakang',
                'material' => 'Stiker Vinyl Orajet Germany + Laminasi Dingin Glossy',
                'brief' => 'Logo toko mencolok, daftar bahan bangunan semen, besi, baja ringan, pasir.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'transfer',
                'days_ago' => 15,
            ],
            // 19
            [
                'customer' => 'Batik Tegal Cempaka Mulya',
                'phone' => '082188990019',
                'title' => 'Katalog Motif Batik Tradisional Tegalan',
                'unit' => 'Buku',
                'qty' => 20,
                'unit_price' => 42000,
                'unit_cost' => null, // Belum HPP
                'dimensions' => 'A5 Lanskap 16 Halaman Staples Tengah',
                'material' => 'Matte Paper 150gr Cover Ivory 260gr',
                'brief' => 'Foto kain batik motif beras mawur dan grinsing dengan deskripsi filosofi.',
                'work_status' => 'revision',
                'payment' => 'partial_dp',
                'method' => 'qris',
                'days_ago' => 14,
            ],
            // 20
            [
                'customer' => 'Distro Clothing Urban Street',
                'phone' => '081355443020',
                'title' => 'Hang Tag Baju Ivory 350gr + Tali Peluru',
                'unit' => 'Pcs',
                'qty' => 500,
                'unit_price' => 450,
                'unit_cost' => 220,
                'dimensions' => '4.5 x 9 cm (Pojok Lengkung Die Cut)',
                'material' => 'Ivory 350gr Tebal Doff + Lubang 3mm',
                'brief' => 'Konsep gelap gothic, ada instruksi pencucian (Washing Instruction).',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'cash',
                'days_ago' => 13,
            ],
            // 21
            [
                'customer' => 'Konter Berkah Seluler',
                'phone' => '087811998021',
                'title' => 'Spanduk Banner Pulsa, Paket Data & Token Listrik',
                'unit' => 'Meter',
                'qty' => 2,
                'unit_price' => 28000,
                'unit_cost' => 15000,
                'dimensions' => '2 x 1 Meter',
                'material' => 'Flexi Standar 280gr',
                'brief' => 'Tampilkan logo Telkomsel, Indosat, XL, Tri, Smartfren, PLN dan Dana OVO Gopay.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'cash',
                'days_ago' => 13,
            ],
            // 22
            [
                'customer' => 'Catering Selera Ibu Hj. Endang',
                'phone' => '085233445022',
                'title' => 'Kotak Nasi Dus Catering Custom Sablon Emas',
                'unit' => 'Pcs',
                'qty' => 300,
                'unit_price' => 2800,
                'unit_cost' => null, // Belum HPP
                'dimensions' => 'Ukuran R10B (20 x 20 x 7 cm)',
                'material' => 'Karton Kraft Coklat Eco-Friendly Tebal 350gr',
                'brief' => 'Tutup box sablon kaligrafi syukuran doa makan dan kontak catering.',
                'work_status' => 'in_progress',
                'payment' => 'partial_dp',
                'method' => 'cash',
                'days_ago' => 12,
            ],
            // 23
            [
                'customer' => 'Rizky & Amanda Wedding',
                'phone' => '081234567023',
                'title' => 'Undangan Pernikahan Amplop Jasmine Board Tebal',
                'unit' => 'Pcs',
                'qty' => 250,
                'unit_price' => 7500,
                'unit_cost' => 4200,
                'dimensions' => '15 x 20 cm Model Amplop Dompet',
                'material' => 'Kertas Jasmine Gliter Putih Susu + Foil Rose Gold',
                'brief' => 'Denah lokasi scan QR Code Google Maps, kaligrafi nama mempelai timbul.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'transfer',
                'days_ago' => 11,
            ],
            // 24
            [
                'customer' => 'Panitia HUT RI RW 04 Dampyak',
                'phone' => '081399887024',
                'title' => 'Kupon Jalan Sehat Berhadiah Sepeda Motor',
                'unit' => 'Buku',
                'qty' => 15,
                'unit_price' => 18000,
                'unit_cost' => 9000,
                'dimensions' => 'Ukuran 15 x 6 cm (Ada Porporasi Sobek & Numerator)',
                'material' => 'HVS Warna Merah Muda 70gr',
                'brief' => 'Nomorator kembar pada sobekan kupon dan arsip panitia 0001 - 1500.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'cash',
                'days_ago' => 11,
            ],
            // 25
            [
                'customer' => 'Agen Telur Ayam Mas Joko',
                'phone' => '085711334025',
                'title' => 'Spanduk Grosir Telur Negeri & Telur Asin Brebes',
                'unit' => 'Meter',
                'qty' => 4,
                'unit_price' => 28000,
                'unit_cost' => 15000,
                'dimensions' => '4 x 1 Meter',
                'material' => 'Flexi 280gr',
                'brief' => 'Tulisan BISA BELI ECER DAN PARTAI BESAR huruf kapital besar terbaca dari jauh.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'cash',
                'days_ago' => 10,
            ],
            // 26
            [
                'customer' => 'Akademi Futsal Tegal Mandiri',
                'phone' => '087822334026',
                'title' => 'Plakat Akrilik Trophy Turnamen U-15',
                'unit' => 'Pcs',
                'qty' => 4,
                'unit_price' => 110000,
                'unit_cost' => null, // HPP Belum Ada
                'dimensions' => 'Tinggi 25 cm x Tebal 8 mm',
                'material' => 'Akrilik Bening Potong Laser + Tatakan Resin Hitam',
                'brief' => 'Grafir siluet pemain menendang bola, cantumkan Juara 1, 2, 3, dan Best Player.',
                'work_status' => 'ready_to_print',
                'payment' => 'paid',
                'method' => 'transfer',
                'days_ago' => 10,
            ],
            // 27
            [
                'customer' => 'Service Laptop & PC Master Computer',
                'phone' => '082133445027',
                'title' => 'Stiker Hologram Segel Garansi Rusak Bila Terbuka',
                'unit' => 'Lembar A4',
                'qty' => 10,
                'unit_price' => 35000,
                'unit_cost' => 19000,
                'dimensions' => '1 x 2 cm Kotak Kecil (Per Lembar isi 180 pcs)',
                'material' => 'Stiker Hologram Silver Pecah Telur (Eggshell)',
                'brief' => 'Ada kotak bulan 1-12 dan tahun 2026-2027 untuk dicentang spidol teknisi.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'cash',
                'days_ago' => 9,
            ],
            // 28
            [
                'customer' => 'Keluarga Besar Alm. H. Sudirman',
                'phone' => '081299001028',
                'title' => 'Buku Surat Yasin & Majmu Syarif 100 Hari',
                'unit' => 'Buku',
                'qty' => 50,
                'unit_price' => 18000,
                'unit_cost' => 10500,
                'dimensions' => 'Ukuran Buku Saku 12 x 15 cm 128 Halaman',
                'material' => 'Cover Beludru Hijau Tua + Poli Emboss Kaligrafi Emas + Rumbai Pembatas',
                'brief' => 'Halaman tambahan 4 lembar foto almarhum, keluarga yang ditinggalkan, dan doa ahli kubur.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'transfer',
                'days_ago' => 9,
            ],
            // 29
            [
                'customer' => 'Klinik Bersalin Kasih Ibu',
                'phone' => '085611223029',
                'title' => 'Map Rekam Medis Pasien Berkantong F4',
                'unit' => 'Pcs',
                'qty' => 200,
                'unit_price' => 4500,
                'unit_cost' => null, // Belum HPP
                'dimensions' => 'F4 Folio Tebal dengan Kantong Selipan Kartu',
                'material' => 'Art Carton 260gr Cetak Luar Dalam Laminasi Doff',
                'brief' => 'Format tabel riwayat pemeriksaan ANC, tabel imunisasi bayi, dan catatan bidan.',
                'work_status' => 'in_progress',
                'payment' => 'partial_dp',
                'method' => 'cash',
                'days_ago' => 8,
            ],
            // 30
            [
                'customer' => 'TK & PAUD Aisyiyah Bustanul Athfal',
                'phone' => '087755667030',
                'title' => 'Backdrop Panggung Acara Pelepasan Siswa 5x3m',
                'unit' => 'Meter',
                'qty' => 15,
                'unit_price' => 28000,
                'unit_cost' => 15000,
                'dimensions' => '5 x 3 Meter',
                'material' => 'Flexi 280gr Lebar Tanpa Sambungan',
                'brief' => 'Tema keceriaan anak dengan balon warna-warni, pelangi, dan toga wisuda kecil.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'transfer',
                'days_ago' => 8,
            ],
            // 31
            [
                'customer' => 'Butik Baju Muslim Annisa',
                'phone' => '081288990031',
                'title' => 'X-Banner Standing Promo Diskon Ramadhan',
                'unit' => 'Set',
                'qty' => 2,
                'unit_price' => 75000,
                'unit_cost' => 42000,
                'dimensions' => '60 x 160 cm',
                'material' => 'Albatros Semi Glossy + Rangka Fiber Hitam Kokoh',
                'brief' => 'Highlight diskon up to 70% gamis dan abaya premium.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'qris',
                'days_ago' => 7,
            ],
            // 32
            [
                'customer' => 'Martabak Lebat Bangka 88',
                'phone' => '085299887032',
                'title' => 'Standee Akrilik Meja Kasir Pembayaran QRIS',
                'unit' => 'Pcs',
                'qty' => 3,
                'unit_price' => 35000,
                'unit_cost' => 18000,
                'dimensions' => 'A6 (10.5 x 15 cm) Model T-Stand',
                'material' => 'Akrilik Bening 2mm + Print Stiker Vinyl High-Res',
                'brief' => 'QR Code QRIS statis Bank Mandiri jelas terbaca kamera HP pelanggan.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'cash',
                'days_ago' => 7,
            ],
            // 33
            [
                'customer' => 'Bengkel Tambal Ban Tubeless Pak Eko',
                'phone' => '081311223033',
                'title' => 'Spanduk Layanan Tambal Ban Nitrogen Buka 24 Jam',
                'unit' => 'Meter',
                'qty' => 2,
                'unit_price' => 28000,
                'unit_cost' => 15000,
                'dimensions' => '2 x 1 Meter',
                'material' => 'Flexi Standar 280gr Finishing Kolong Kayu Kanan Kiri',
                'brief' => 'Tulisan BUKA 24 JAM warna merah kuning mencolok di malam hari.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'cash',
                'days_ago' => 6,
            ],
            // 34
            [
                'customer' => 'Biro Perjalanan Haji & Umroh Safar Tour',
                'phone' => '087833445034',
                'title' => 'Brosur Paket Umroh Awal Musim 2026 A4 Bolak Balik',
                'unit' => 'Rim',
                'qty' => 1,
                'unit_price' => 320000,
                'unit_cost' => null, // Belum diinput
                'dimensions' => 'A4 Lembaran Bolak Balik',
                'material' => 'Art Paper 120gr Full Color High Quality',
                'brief' => 'Foto hotel berbintang depan Masjidil Haram dan rincian fasilitas maskapai Saudia Airlines.',
                'work_status' => 'ready_to_print',
                'payment' => 'paid',
                'method' => 'transfer',
                'days_ago' => 6,
            ],
            // 35
            [
                'customer' => 'Klinik Terapi Mata Sehat',
                'phone' => '082144556035',
                'title' => 'Stiker One Way Vision Kaca Depan Ruko',
                'unit' => 'Meter',
                'qty' => 6,
                'unit_price' => 75000,
                'unit_cost' => 42000,
                'dimensions' => 'Tinggi 2m x Lebar 3m (2 Pintu Kaca)',
                'material' => 'Stiker One Way Berlubang Tembus Pandang Dari Dalam',
                'brief' => 'Dari luar tampak gambar ilustrasi anatomi mata dan konsultasi dokter, dari dalam tetap terang.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'transfer',
                'days_ago' => 5,
            ],
            // 36
            [
                'customer' => 'Pecinta Alam SMA Negeri 1 Tegal',
                'phone' => '081266778036',
                'title' => 'Gantungan Kunci Pin Souvenir Kemah Bakti',
                'unit' => 'Pcs',
                'qty' => 100,
                'unit_price' => 4000,
                'unit_cost' => null, // Belum HPP
                'dimensions' => 'Diameter 4.4 cm Gantungan Kunci Buka Botol',
                'material' => 'Seng Aluminium + Laminasi Doff Kasar',
                'brief' => 'Logo siluet gunung Slamet dengan tulisan Lestarikan Alam Negeriku.',
                'work_status' => 'draft',
                'payment' => 'unpaid',
                'method' => 'cash',
                'days_ago' => 5,
            ],
            // 37
            [
                'customer' => 'Firma Hukum Sudrajat & Partners',
                'phone' => '085622334037',
                'title' => 'Kop Surat Resmi & Amplop Coklat Tali Jaya',
                'unit' => 'Rim',
                'qty' => 2,
                'unit_price' => 175000,
                'unit_cost' => 95000,
                'dimensions' => 'A4 HVS 80gr & Amplop F4 Cetak 2 Warna',
                'material' => 'HVS 80gr PaperOne & Amplop Samson Tebal Coklat',
                'brief' => 'Tinta cetak biru dongker dan emas gelap, logo timbangan keadilan.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'transfer',
                'days_ago' => 4,
            ],
            // 38
            [
                'customer' => 'Depo Pasir & Semen Berkah Alam',
                'phone' => '089922334038',
                'title' => 'Nota Timbangan Truk Pasir 2 Ply Rangkap',
                'unit' => 'Buku',
                'qty' => 20,
                'unit_price' => 22000,
                'unit_cost' => 12000,
                'dimensions' => 'Ukuran 1/4 Folio (10 x 16.5 cm)',
                'material' => 'Kertas NCR 2 Ply (Putih & Merah Muda)',
                'brief' => 'Kolom berat kotor (bruto), berat kosong (tara), berat bersih (netto) dan tanda tangan supir.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'cash',
                'days_ago' => 4,
            ],
            // 39
            [
                'customer' => 'Kedai Es Jus Buah Segar Bang Ali',
                'phone' => '081388990039',
                'title' => 'Banner Spanduk Meja Booth Alpukat Kocok',
                'unit' => 'Meter',
                'qty' => 2,
                'unit_price' => 28000,
                'unit_cost' => 15000,
                'dimensions' => '2 x 0.8 Meter',
                'material' => 'Flexi Korea 380gr',
                'brief' => 'Foto alpukat matang kerok saus coklat keju melimpah, harga Rp10.000.',
                'work_status' => 'ready_to_print',
                'payment' => 'paid',
                'method' => 'cash',
                'days_ago' => 3,
            ],
            // 40
            [
                'customer' => 'Kios Ayam Potong Segar Berkah',
                'phone' => '087811223040',
                'title' => 'Spanduk Daging Ayam Negeri Segar & Halal',
                'unit' => 'Meter',
                'qty' => 3,
                'unit_price' => 28000,
                'unit_cost' => null, // Belum HPP
                'dimensions' => '3 x 1 Meter',
                'material' => 'Flexi Standar 280gr',
                'brief' => 'Pesanan dibatalkan pelanggan karena toko pindah lokasi sewa.',
                'work_status' => 'canceled',
                'payment' => 'unpaid',
                'method' => 'cash',
                'days_ago' => 3,
            ],

            // 41 - 50: Transaksi Kasir POS Ritel (Walk-in ATK, Print Kilat, Jilid, Fotokopi)
            // 41
            [
                'customer' => 'Pelanggan Ritel (Walk-In)',
                'phone' => null,
                'title' => 'Kertas HVS PaperOne A4 75gsm (Dus 5 Rim)',
                'order_type' => 'retail_pos',
                'unit' => 'Dus',
                'qty' => 2,
                'unit_price' => 225000,
                'unit_cost' => 185000,
                'dimensions' => 'A4 75gr',
                'material' => 'Kertas HVS Original',
                'brief' => 'Pembelian langsung kasir POS.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'cash',
                'days_ago' => 3,
            ],
            // 42
            [
                'customer' => 'Ibu Ratna Guru SDN 02',
                'phone' => '081234567842',
                'title' => 'Map Plastik Business File & Clear Holder 40 Pocket',
                'order_type' => 'retail_pos',
                'unit' => 'Pcs',
                'qty' => 6,
                'unit_price' => 18000,
                'unit_cost' => 11500,
                'dimensions' => 'F4 Folio',
                'material' => 'Plastik PP Tebal',
                'brief' => 'Pembelian map portofolio tugas siswa.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'cash',
                'days_ago' => 2,
            ],
            // 43
            [
                'customer' => 'Dimas Mahasiswa UPS Tegal',
                'phone' => '085711223843',
                'title' => 'Print Skripsi Full Color 120 Hal & Jilid Soft Cover',
                'order_type' => 'retail_pos',
                'unit' => 'Paket',
                'qty' => 3,
                'unit_price' => 65000,
                'unit_cost' => 32000,
                'dimensions' => 'A4 HVS 80gr',
                'material' => 'Print Laserjet Fuji Xerox + Mika Biru',
                'brief' => 'Cetak dokumen laporan sidang akhir.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'qris',
                'days_ago' => 2,
            ],
            // 44
            [
                'customer' => 'Bapak Hendra Fotografer',
                'phone' => '082133445844',
                'title' => 'Kertas Foto Glossy Silky A4 & Laminating Panas',
                'order_type' => 'retail_pos',
                'unit' => 'Pack',
                'qty' => 4,
                'unit_price' => 45000,
                'unit_cost' => 28000,
                'dimensions' => 'A4 (20 Lembar / pack)',
                'material' => 'Glossy Photo Paper 230gsm',
                'brief' => 'Kebutuhan cetak foto studio kilat.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'transfer',
                'days_ago' => 2,
            ],
            // 45
            [
                'customer' => 'Pelanggan Walk-In',
                'phone' => null,
                'title' => 'Materai Elektronik & Amplop Coklat Tali Airmail',
                'order_type' => 'retail_pos',
                'unit' => 'Lembar',
                'qty' => 5,
                'unit_price' => 12500,
                'unit_cost' => 10500,
                'dimensions' => 'Materai 10000 Resmi',
                'material' => 'Materai Tempel Pos',
                'brief' => 'Kebutuhan berkas lamaran kerja BUMN.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'cash',
                'days_ago' => 1,
            ],
            // 46
            [
                'customer' => 'Sdr. Bagus Triyono',
                'phone' => '087811998846',
                'title' => 'Cetak Pas Foto 4x6 & 3x4 Background Merah CPNS Kilat',
                'order_type' => 'retail_pos',
                'unit' => 'Paket',
                'qty' => 4,
                'unit_price' => 15000,
                'unit_cost' => 5000,
                'dimensions' => '4x6 (8 lembar) & 3x4 (8 lembar)',
                'material' => 'Kertas Foto Doff Anti Gores',
                'brief' => 'Edit ganti background jas formal & cetak langsung jadi 5 menit.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'qris',
                'days_ago' => 1,
            ],
            // 47
            [
                'customer' => 'Toko Kelontong Berkah 2',
                'phone' => '081399001847',
                'title' => 'Lakban Coklat 2 Inch Daimaru & Double Tape Foam 3M',
                'order_type' => 'retail_pos',
                'unit' => 'Roll',
                'qty' => 6,
                'unit_price' => 16000,
                'unit_cost' => 10500,
                'dimensions' => 'Lebar 48 mm x 90 Yard',
                'material' => 'Lakban Perekat Ekstra Kuat',
                'brief' => 'Perlengkapan packing ekspedisi.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'cash',
                'days_ago' => 1,
            ],
            // 48
            [
                'customer' => 'Sdr. Fajar Hidayat Mahasiswa UPS',
                'phone' => '085211334848',
                'title' => 'Jilid Skripsi Hardcover Lux Tinta Emas Timbul',
                'order_type' => 'retail_pos',
                'unit' => 'Buku',
                'qty' => 4,
                'unit_price' => 50000,
                'unit_cost' => 24000,
                'dimensions' => 'A4 Hardcover Karton No. 40',
                'material' => 'Linen Hitam + Hotprint Emas + Pita Pembatas',
                'brief' => 'Jilid tugas akhir wisuda fakultas teknik.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'cash',
                'days_ago' => 0,
            ],
            // 49
            [
                'customer' => 'Komunitas UMKM Mandiri',
                'phone' => '089944556849',
                'title' => 'Print Brosur A4 Kilat 50 Lembar Art Paper 150g',
                'order_type' => 'retail_pos',
                'unit' => 'Lembar',
                'qty' => 50,
                'unit_price' => 2500,
                'unit_cost' => 1200,
                'dimensions' => 'A4 1 Sisi',
                'material' => 'Art Paper 150gr Fuji Xerox',
                'brief' => 'Print darurat untuk pameran bazar UMKM mall.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'qris',
                'days_ago' => 0,
            ],
            // 50
            [
                'customer' => 'Kantor Notaris Setiabudi SH',
                'phone' => '081299887850',
                'title' => 'Tinta Printer EPSON 003 Original 4 Warna (CMYK)',
                'order_type' => 'retail_pos',
                'unit' => 'Set',
                'qty' => 1,
                'unit_price' => 340000,
                'unit_cost' => 275000,
                'dimensions' => 'Botol 65ml EPSON L3110 / L3210',
                'material' => 'Tinta Dye Original Epson Indonesia',
                'brief' => 'Pembelian perlengkapan cetak akta kantor notaris.',
                'work_status' => 'completed',
                'payment' => 'paid',
                'method' => 'transfer',
                'days_ago' => 0,
            ],
        ];

        DB::beginTransaction();

        try {
            foreach ($orderTemplates as $idx => $tpl) {
                // Alternasi cabang (Keturen vs Kraton)
                $branch = $branches[$idx % $branches->count()];
                $designer = $designers[$idx % $designers->count()];
                $cashier = $cashiers[$idx % $cashiers->count()];

                $orderType = $tpl['order_type'] ?? 'design_printing';
                $daysAgo = $tpl['days_ago'] ?? 0;
                $createdAt = Carbon::now()->subDays($daysAgo)->subHours(rand(1, 10))->subMinutes(rand(5, 55));

                $orderNumber = Order::generateOrderNumber($branch, $orderType);

                $qty = (int) $tpl['qty'];
                $unitPrice = (float) $tpl['unit_price'];
                $subtotal = $qty * $unitPrice;
                $totalAmount = $subtotal;

                $unitCost = isset($tpl['unit_cost']) && $tpl['unit_cost'] !== null ? (float) $tpl['unit_cost'] : null;
                $totalCost = $unitCost !== null ? ($qty * $unitCost) : null;

                $workStatus = $tpl['work_status'];
                $paymentType = $tpl['payment'];

                $paidAmount = 0;
                $paymentStatus = 'unpaid';

                if ($workStatus === 'canceled') {
                    $paidAmount = 0;
                    $paymentStatus = 'unpaid';
                } elseif ($paymentType === 'paid') {
                    $paidAmount = $totalAmount;
                    $paymentStatus = 'paid';
                } elseif ($paymentType === 'partial_dp') {
                    $paidAmount = round($totalAmount * 0.5);
                    $paymentStatus = 'partial_dp';
                }

                $completedAt = ($workStatus === 'completed') ? $createdAt->copy()->addHours(rand(3, 12)) : null;

                $order = Order::create([
                    'branch_id' => $branch->id,
                    'user_id' => $cashier->id,
                    'order_number' => $orderNumber,
                    'customer_name' => $tpl['customer'],
                    'customer_phone' => $tpl['phone'] ?? null,
                    'order_type' => $orderType,
                    'total_amount' => $totalAmount,
                    'total_cost' => $totalCost,
                    'paid_amount' => $paidAmount,
                    'payment_status' => $paymentStatus,
                    'work_status' => $workStatus,
                    'cancellation_reason' => $workStatus === 'canceled' ? 'Dibatalkan oleh pelanggan karena pindah lokasi sewa kios.' : null,
                    'cancellation_type' => $workStatus === 'canceled' ? 'none' : null,
                    'completed_at' => $completedAt,
                ]);

                // Set timestamps
                $order->created_at = $createdAt;
                $order->updated_at = $completedAt ?? $createdAt;
                $order->save();

                // Create OrderItem
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'item_name' => $tpl['title'],
                    'qty' => $qty,
                    'unit' => $tpl['unit'] ?? 'Pcs',
                    'unit_price' => $unitPrice,
                    'cost_price' => $unitCost,
                    'subtotal' => $subtotal,
                ]);
                $orderItem->created_at = $createdAt;
                $orderItem->updated_at = $createdAt;
                $orderItem->save();

                // Create DesignJob if design_printing
                if ($orderType === 'design_printing') {
                    $designJob = DesignJob::create([
                        'order_id' => $order->id,
                        'designer_id' => $designer->id,
                        'title' => mb_substr($tpl['title'], 0, 140),
                        'dimensions' => isset($tpl['dimensions']) ? mb_substr($tpl['dimensions'], 0, 48) : null,
                        'material_notes' => isset($tpl['material']) ? mb_substr($tpl['material'], 0, 95) : null,
                        'brief_notes' => $tpl['brief'] ?? null,
                        'revision_count' => $workStatus === 'revision' ? 1 : 0,
                        'revision_notes' => $workStatus === 'revision' ? 'Tolong perbesar tulisan judul utama dan ganti latar belakang menjadi lebih terang.' : null,
                        'deadline_at' => $createdAt->copy()->addDays(2),
                    ]);
                    $designJob->created_at = $createdAt;
                    $designJob->updated_at = $createdAt;
                    $designJob->save();
                }

                // Create CashMovement if payment made
                if ($paidAmount > 0) {
                    $method = $tpl['method'] ?? 'cash';

                    $category = 'order_settlement';
                    if ($orderType === 'retail_pos') {
                        $category = 'retail_pos';
                    } elseif ($paymentStatus === 'partial_dp') {
                        $category = 'order_dp';
                    }

                    $note = match ($category) {
                        'order_dp' => "DP 50% Order {$orderNumber}",
                        'retail_pos' => "Transaksi Kasir POS Ritel {$orderNumber}",
                        default => "Pelunasan Pembayaran Order {$orderNumber}",
                    };

                    $cm = CashMovement::create([
                        'branch_id' => $branch->id,
                        'order_id' => $order->id,
                        'user_id' => $cashier->id,
                        'type' => 'in',
                        'amount' => $paidAmount,
                        'payment_method' => $method,
                        'category' => $category,
                        'notes' => $note,
                    ]);
                    $cm->created_at = $createdAt;
                    $cm->updated_at = $createdAt;
                    $cm->save();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
