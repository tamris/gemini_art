# Blueprint Sistem Manajemen Percetakan & Studio Desain (Multi-Cabang)

Dokumen spesifikasi arsitektur teknis dan alur operasional sistem manajemen percetakan, order desain grafis, kasir ritel, dan arsip file terpusat.

---

## 1. Latar Belakang & Identifikasi Masalah

* **Pencatatan Keuangan Tercecer:** Kasir cabang sering lupa mencatat omset harian karena alur kasir manual saat toko ramai.
* **Arsip Desain Manual & Tidak Efisien:** Pengiriman hasil desain dan brief melalui email dari karyawan cabang ke owner menimbulkan redudansi, risiko file terlewat, dan menyulitkan pelacakan historis per order.
* **Kebutuhan Sistem:** Platform terpusat untuk memisahkan pencatatan pengerjaan desain kustom (core bisnis) dengan kasir ritel cepat (fotokopi, ATK, print lembaran), sekaligus mengotomatisasi pengarsipan file ke cloud storage.

---

## 2. Struktur Role & Akses Pengguna

| Role | Ruang Lingkup Akses | Tanggung Jawab Utama |
| :--- | :--- | :--- |
| **Owner** | Multi-cabang (Global) | Monitor omset gabungan real-time, audit kas keluar/masuk, akses seluruh arsip file desain. |
| **Kasir Cabang** | Cabang masing-masing | Input order desain baru, terima DP/pelunasan, transaksi POS ritel (FC/ATK), tutup shift kasir. |
| **Desainer Cabang** | Cabang masing-masing | Mengerjakan antrean tiket desain, update status pengerjaan, upload file cetak final ke sistem. |

---

## 3. Alur Kerja Operasional (SOP Sistem)

1. **Penerimaan Order & Komitmen Biaya (Front Office):**
   * Customer datang membawa brief desain.
   * Kasir/Desainer input tiket order baru: data customer, spesifikasi pengerjaan, deadline, dan nominal biaya.
   * Pembayaran wajib dicatat di awal (Minimal DP atau Lunas). Nominal langsung masuk ke buku kas cabang hari itu.

2. **Antrean Pengerjaan Desainer (Workdesk):**
   * Tiket otomatis masuk ke antrean pengerjaan cabang terkait dengan status `in_progress`.
   * Desainer mengerjakan aset sesuai catatan brief yang tertera pada sistem.

3. **Upload File & Sinkronisasi Arsip:**
   * Desainer mengunggah file cetak siap produksi (PDF/TIFF/JPG high-res) langsung ke tiket terkait.
   * Sistem otomatis memindahkan file ke cloud storage terstruktur dan mengubah status tiket menjadi `ready_to_print`.
   * Owner dapat langsung memantau file tersebut dari dashboard pusat tanpa rekap email.

4. **Pelunasan & Serah Terima:**
   * Customer mengambil pesanan.
   * Kasir membuka tiket, mencatat pelunasan sisa tagihan (jika sebelumnya berstatus DP), dan mengubah status tiket menjadi `completed`.

---

## 4. UI/UX Design System: Soft Neo-Brutalism

Sistem mengadopsi gaya visual **Soft Neo-Brutalism** dengan implementasi Tailwind CSS:

* **Borders & Shadows:** 
  * Border tegas: `border-2 border-black`
  * Hard shadow offset tanpa blur: `shadow-[4px_4px_0px_0px_#000]`
  * Efek klik tombol: `active:translate-x-[2px] active:translate-y-[2px] active:shadow-none`
* **Sudut & Ruang:** `rounded-xl` hingga `rounded-2xl` untuk menjaga kesan antarmuka perangkat lunak modern.
* **Palet Warna Operasional:**
  * Background utama netral: `#F8FAFC` atau `#FFFFFF` (mencegah kelelahan mata operasional kasir/desainer).
  * Aksen status tegas:
    * Antrean / Draft: Kuning cerah (`#FDE047`)
    * Proses Pengerjaan: Biru terang (`#60A5FA`)
    * Siap Cetak / Lunas: Hijau mint (`#4ADE80`)
    * Dibatalkan / Masalah: Merah pastel (`#F87171`)

---

## 5. Skema Database (Laravel Migrations Blueprint)

### `branches`
* `id`: bigint, unsigned, primary key
* `name`: string(100)
* `code`: string(10), unique (contoh: `CBA`, `CBB`)
* `address`: text, nullable
* `phone`: string(20), nullable
* `created_at`, `updated_at`: timestamps

### `users`
* `id`: bigint, unsigned, primary key
* `branch_id`: foreignId, nullable (null = owner multi-cabang), constrained ke `branches`
* `name`: string
* `email`: string, unique
* `password`: string
* `role`: enum (`owner`, `cashier`, `designer`)
* `created_at`, `updated_at`: timestamps

### `orders`
* `id`: bigint, unsigned, primary key
* `branch_id`: foreignId, constrained ke `branches`
* `user_id`: foreignId, constrained ke `users` (pembuat order)
* `order_number`: string(50), unique (contoh: `ORD-CBA-202610-0001`)
* `customer_name`: string(100)
* `customer_phone`: string(20), nullable
* `order_type`: enum (`design_printing`, `retail_pos`)
* `total_amount`: decimal(12, 2)
* `paid_amount`: decimal(12, 2), default: 0
* `payment_status`: enum (`unpaid`, `partial_dp`, `paid`)
* `work_status`: enum (`draft`, `in_progress`, `revision`, `ready_to_print`, `completed`, `canceled`)
* `created_at`, `updated_at`: timestamps

### `order_items` (Khusus transaksi POS / ATK / Cetak lembaran)
* `id`: bigint, unsigned, primary key
* `order_id`: foreignId, constrained ke `orders`, cascade on delete
* `item_name`: string(150)
* `qty`: integer
* `unit_price`: decimal(12, 2)
* `subtotal`: decimal(12, 2)
* `created_at`, `updated_at`: timestamps

### `design_jobs`
* `id`: bigint, unsigned, primary key
* `order_id`: foreignId, constrained ke `orders`, cascade on delete
* `designer_id`: foreignId, nullable, constrained ke `users`
* `title`: string(150)
* `dimensions`: string(50), nullable (contoh: `3x1 meter`)
* `material_notes`: string(100), nullable (contoh: `Flexi 280gr`)
* `brief_notes`: text, nullable
* `deadline_at`: timestamp, nullable
* `created_at`, `updated_at`: timestamps

### `design_assets`
* `id`: bigint, unsigned, primary key
* `design_job_id`: foreignId, constrained ke `design_jobs`, cascade on delete
* `uploaded_by`: foreignId, constrained ke `users`
* `file_type`: enum (`brief_ref`, `preview_proof`, `final_print`)
* `file_path`: string
* `file_name`: string
* `file_size`: unsignedBigInteger (dalam bytes)
* `created_at`, `updated_at`: timestamps

### `cash_movements`
* `id`: bigint, unsigned, primary key
* `branch_id`: foreignId, constrained ke `branches`
* `order_id`: foreignId, nullable, constrained ke `orders`
* `user_id`: foreignId, constrained ke `users`
* `type`: enum (`in`, `out`)
* `amount`: decimal(12, 2)
* `payment_method`: enum (`cash`, `qris`, `transfer`)
* `category`: enum (`order_dp`, `order_settlement`, `retail_pos`, `petty_cash_expense`, `initial_balance`)
* `notes`: text, nullable
* `created_at`, `updated_at`: timestamps

---

## 6. Pola Penyimpanan Aset Cloud

Struktur path penyimpanan file pada cloud storage (S3 API / R2):

```text
storage/
└── designs/
    └── {branch_code}/
        └── {YYYY-MM}/
            └── {order_number}/
                ├── ref_{file_name}
                └── print_{file_name}