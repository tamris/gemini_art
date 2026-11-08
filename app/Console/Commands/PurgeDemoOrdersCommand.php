<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\DesignJob;
use App\Models\DesignAsset;
use App\Models\CashMovement;

class PurgeDemoOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:purge 
                            {--number= : Nomor order spesifik yang ingin dihapus (contoh: KTR-2611-001)}
                            {--all : Hapus SEMUA pesanan demo dan mutasi kas terkait}
                            {--force : Jalankan penghapusan tanpa konfirmasi pertanyaan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus pesanan desain/ritel dan bersihkan file upload serta mutasi kas demo';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $orderNumber = $this->option('number');
        $isAll = $this->option('all');
        $force = $this->option('force');

        // Mode Interaktif jika tidak ada opsi yang diberikan
        if (!$orderNumber && !$isAll) {
            $choice = $this->choice(
                'Pilih opsi pembersihan order demo:',
                [
                    'all' => 'Hapus SEMUA pesanan & transaksi kas demo (Reset Bersih)',
                    'single' => 'Hapus 1 pesanan berdasarkan Nomor Order',
                    'cancel' => 'Batal',
                ],
                'cancel'
            );

            if ($choice === 'cancel') {
                $this->info('Operasi dibatalkan.');
                return Command::SUCCESS;
            }

            if ($choice === 'all') {
                $isAll = true;
            } else {
                $orderNumber = $this->ask('Masukkan Nomor Order yang ingin dihapus (misal: KTR-2611-001)');
            }
        }

        // Hapus single order
        if ($orderNumber) {
            return $this->purgeSingleOrder(trim($orderNumber), $force);
        }

        // Hapus semua order
        if ($isAll) {
            return $this->purgeAllOrders($force);
        }

        return Command::SUCCESS;
    }

    /**
     * Hapus satu pesanan spesifik beserta relasi & berkasnya
     */
    protected function purgeSingleOrder(string $orderNumber, bool $force): int
    {
        $order = Order::with(['designJob.assets', 'items', 'cashMovements'])->where('order_number', $orderNumber)->first();

        if (!$order) {
            $this->error("Pesanan dengan nomor [{$orderNumber}] tidak ditemukan di database.");
            return Command::FAILURE;
        }

        if (!$force && !$this->confirm("Yakin ingin menghapus pesanan [{$orderNumber}] ({$order->customer_name}) beserta berkas dan mutasi kasnya?", true)) {
            $this->comment('Dibatalkan oleh pengguna.');
            return Command::SUCCESS;
        }

        $this->info("Menghapus pesanan [{$orderNumber}]...");

        $deletedFiles = 0;
        $disk = config('filesystems.archive_disk', 'public');

        // 1. Hapus berkas fisik di storage
        if ($order->designJob) {
            foreach ($order->designJob->assets as $asset) {
                if ($asset->file_path && Storage::disk($disk)->exists($asset->file_path)) {
                    Storage::disk($disk)->delete($asset->file_path);
                    $deletedFiles++;
                }
                $asset->delete();
            }
            $order->designJob->delete();
        }

        // 2. Hapus mutasi kas yang terkait order ini
        $deletedCash = CashMovement::where('order_id', $order->id)->delete();

        // 3. Hapus item order & order itu sendiri
        $order->items()->delete();
        $order->delete();

        $this->info("✓ Pesanan [{$orderNumber}] berhasil dihapus!");
        $this->line("- Berkas fisik dihapus: {$deletedFiles} file");
        $this->line("- Mutasi kas dihapus: {$deletedCash} data");

        $this->clearCache();

        return Command::SUCCESS;
    }

    /**
     * Hapus semua pesanan demo secara menyeluruh
     */
    protected function purgeAllOrders(bool $force): int
    {
        $totalOrders = Order::count();

        if ($totalOrders === 0) {
            $this->info('Tidak ada data pesanan di database. Database sudah bersih.');
            return Command::SUCCESS;
        }

        if (!$force && !$this->confirm("PERINGATAN: Ini akan menghapus seluruh {$totalOrders} pesanan, berkas desain, dan transaksi kas terkait. Lanjutkan?", false)) {
            $this->comment('Dibatalkan oleh pengguna.');
            return Command::SUCCESS;
        }

        $this->info("Membersihkan seluruh {$totalOrders} pesanan demo...");

        $disk = config('filesystems.archive_disk', 'public');
        $deletedFiles = 0;

        // 1. Bersihkan seluruh file fisik di storage desain
        $assets = DesignAsset::all();
        foreach ($assets as $asset) {
            if ($asset->file_path && Storage::disk($disk)->exists($asset->file_path)) {
                Storage::disk($disk)->delete($asset->file_path);
                $deletedFiles++;
            }
        }

        // Bersihkan folder designs jika masih ada file sisa
        try {
            $orphanFiles = Storage::disk($disk)->allFiles('designs');
            foreach ($orphanFiles as $file) {
                Storage::disk($disk)->delete($file);
                $deletedFiles++;
            }
        } catch (\Throwable $e) {
            // Abaikan jika folder belum ada
        }

        // 2. Hapus data tabel transaksi (anak ke induk)
        DesignAsset::query()->delete();
        DesignJob::query()->delete();
        OrderItem::query()->delete();
        $deletedCash = CashMovement::whereNotNull('order_id')
            ->orWhereIn('category', ['order_dp', 'order_settlement', 'order_refund', 'retail_pos'])
            ->delete();
        Order::query()->delete();

        $this->info("✓ Seluruh {$totalOrders} pesanan demo berhasil dibersihkan total!");
        $this->line("- Berkas desain dihapus dari disk: {$deletedFiles} file");
        $this->line("- Mutasi kas dihapus: {$deletedCash} data");
        $this->line("- Akun staf, data cabang, dan pengaturan toko TETAP AMAN.");

        $this->clearCache();

        return Command::SUCCESS;
    }

    /**
     * Bersihkan cache Laravel agar tampilan web langsung ter-refresh
     */
    protected function clearCache(): void
    {
        $this->callSilent('optimize:clear');
        $this->info('✓ Cache Laravel berhasil dibersihkan.');
    }
}
