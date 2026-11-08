<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\DesignAsset;

class CleanupR2OrphansCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:cleanup-orphans {--force : Perform the actual deletion without confirmation} {--dry-run : Only list orphan files without deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan storage disk and clean up orphan files that no longer exist in the database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $disk = config('filesystems.archive_disk', 'public');
        $this->info("Scanning storage disk [{$disk}] for orphan design files...");

        try {
            $files = Storage::disk($disk)->allFiles('designs');
        } catch (\Throwable $e) {
            $this->error("Failed to connect to storage disk [{$disk}]: " . $e->getMessage());
            return Command::FAILURE;
        }

        $this->line("Found " . count($files) . " total files in [designs/] on disk.");

        $orphanFiles = [];
        $totalOrphanBytes = 0;

        foreach ($files as $filePath) {
            $existsInDb = DesignAsset::where('file_path', $filePath)->exists();
            if (!$existsInDb) {
                $size = 0;
                try {
                    $size = Storage::disk($disk)->size($filePath);
                } catch (\Throwable $e) {
                    // Size could not be determined
                }
                $orphanFiles[] = [
                    'path' => $filePath,
                    'size' => $size,
                ];
                $totalOrphanBytes += $size;
            }
        }

        if (empty($orphanFiles)) {
            $this->info("✓ No orphan files found. Storage disk [{$disk}] is 100% in sync with database!");
            return Command::SUCCESS;
        }

        $this->warn("Found " . count($orphanFiles) . " orphan file(s) occupying " . round($totalOrphanBytes / (1024 * 1024), 2) . " MB (" . number_format($totalOrphanBytes, 0, ',', '.') . " bytes):");
        $tableData = array_map(function ($item) {
            return [
                'File Path' => $item['path'],
                'Size' => round($item['size'] / 1024, 2) . ' KB',
            ];
        }, $orphanFiles);
        $this->table(['File Path', 'Size'], $tableData);

        if ($this->option('dry-run')) {
            $this->info("Dry-run mode active. No files were deleted.");
            return Command::SUCCESS;
        }

        if (!$this->option('force') && !$this->confirm("Do you want to permanently delete these " . count($orphanFiles) . " orphan file(s) from [{$disk}]?", false)) {
            $this->comment("Operation aborted by user.");
            return Command::SUCCESS;
        }

        $this->info("Deleting orphan files from [{$disk}]...");
        $deletedCount = 0;
        foreach ($orphanFiles as $item) {
            try {
                Storage::disk($disk)->delete($item['path']);
                $deletedCount++;
                $this->line("Deleted: {$item['path']}");
            } catch (\Throwable $e) {
                $this->error("Failed to delete {$item['path']}: " . $e->getMessage());
            }
        }

        $this->info("✓ Successfully deleted {$deletedCount} orphan file(s). Freed " . round($totalOrphanBytes / (1024 * 1024), 2) . " MB from disk [{$disk}].");
        return Command::SUCCESS;
    }
}

