<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\DesignAsset;
use App\Models\DesignJob;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WorkdeskController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = DesignJob::with(['order.branch', 'order.creator', 'order.items', 'designer', 'assets.uploader']);

        if (!$user->isOwner()) {
            $query->whereHas('order', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        } elseif ($request->filled('branch_id')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->where('work_status', $request->status);
            });
        } else {
            // Default: active work (in_progress, revision, draft)
            $query->whereHas('order', function ($q) {
                $q->whereIn('work_status', ['draft', 'in_progress', 'revision', 'ready_to_print']);
            });
        }

        // Assigned to me filter
        if ($request->boolean('my_jobs')) {
            $query->where('designer_id', $user->id);
        }

        $jobs = $query->join('orders', 'design_jobs.order_id', '=', 'orders.id')
            ->select('design_jobs.*')
            ->orderByRaw("CASE WHEN orders.work_status = 'revision' THEN 0 ELSE 1 END")
            ->orderBy('design_jobs.deadline_at', 'asc')
            ->paginate(12)
            ->withQueryString();
        $branches = Branch::all();

        return view('workdesk.index', compact('jobs', 'branches'));
    }

    public function uploadAsset(Request $request, DesignJob $job)
    {
        $user = Auth::user();
        $order = $job->order;

        if (!$user->isOwner() && $order->branch_id !== $user->branch_id) {
            abort(403, 'Akses ditolak.');
        }

        if ($request->boolean('chunk_upload')) {
            return $this->handleChunkedUpload($request, $job);
        }

        $request->validate([
            'files' => 'nullable|array',
            'files.*' => 'file|max:512000', // max 500MB per file
            'file' => 'nullable|file|max:512000',
            'file_type' => 'required|in:final_print,preview_proof,brief_ref',
        ]);

        $uploadedFiles = [];
        if ($request->hasFile('files')) {
            $uploadedFiles = $request->file('files');
        } elseif ($request->hasFile('file')) {
            $uploadedFiles = [$request->file('file')];
        }

        if (empty($uploadedFiles)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan pilih minimal satu berkas desain untuk diunggah.',
                ], 422);
            }
            return back()->with('error', 'Silakan pilih minimal satu berkas desain untuk diunggah.');
        }

        $folder = $this->resolveAssetFolder($order);
        $disk = config('filesystems.archive_disk', 'public');
        $createdAssets = [];
        $uploadedNames = [];

        foreach ($uploadedFiles as $index => $file) {
            $fileName = $this->resolveUniqueAssetFilename(
                $disk,
                $folder,
                $request->file_type,
                $file->getClientOriginalName(),
                $index
            );

            $path = $file->storeAs($folder, $fileName, $disk);

            $asset = $this->recordDesignAsset(
                $job,
                $user->id,
                $request->file_type,
                $path,
                $disk,
                $fileName,
                (int) $file->getSize()
            );

            $createdAssets[] = $asset;
            $uploadedNames[] = $fileName;
        }

        $count = count($createdAssets);
        $message = $count === 1
            ? "File {$uploadedNames[0]} berhasil diunggah! Status tiket otomatis diperbarui."
            : "{$count} file desain berhasil diunggah! Status tiket otomatis diperbarui.";

        if ($request->expectsJson() || $request->ajax()) {
            session()->flash('success', $message);
            return response()->json([
                'success' => true,
                'message' => $message,
                'file_name' => $uploadedNames[0] ?? '',
                'count' => $count,
                'asset_id' => $createdAssets[0]->id ?? null,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Handle chunked file upload so large print files bypass server post_max_size limits.
     */
    protected function handleChunkedUpload(Request $request, DesignJob $job)
    {
        $user = Auth::user();
        $order = $job->order;

        $request->validate([
            'upload_id' => ['required', 'string', 'max:64', 'regex:/^[a-zA-Z0-9_-]+$/'],
            'chunk_index' => 'required|integer|min:0',
            'total_chunks' => 'required|integer|min:1|max:1024',
            'original_name' => 'required|string|max:255',
            'file_size' => 'required|integer|min:1|max:524288000',
            'file_type' => 'required|in:final_print,preview_proof,brief_ref',
            'chunk' => 'required|file|max:5120',
            'is_last_file_in_batch' => 'nullable|boolean',
            'uploaded_files_count' => 'nullable|integer|min:1',
        ]);

        $uploadId = $request->input('upload_id');
        $chunkIndex = (int) $request->input('chunk_index');
        $totalChunks = (int) $request->input('total_chunks');

        if ($chunkIndex >= $totalChunks) {
            return response()->json([
                'success' => false,
                'message' => 'Indeks potongan berkas tidak valid.',
            ], 422);
        }

        $chunkDirRelative = "chunks/{$job->id}_{$user->id}_{$uploadId}";

        if ($chunkIndex === 0) {
            $this->purgeStaleChunkDirectories();
        }

        Storage::disk('local')->makeDirectory($chunkDirRelative);
        $request->file('chunk')->storeAs($chunkDirRelative, "part_{$chunkIndex}", 'local');

        if ($chunkIndex < $totalChunks - 1) {
            return response()->json([
                'success' => true,
                'completed' => false,
                'chunk_received' => $chunkIndex,
            ]);
        }

        for ($i = 0; $i < $totalChunks; $i++) {
            if (!Storage::disk('local')->exists("{$chunkDirRelative}/part_{$i}")) {
                Storage::disk('local')->deleteDirectory($chunkDirRelative);
                return response()->json([
                    'success' => false,
                    'message' => 'Potongan berkas tidak lengkap. Silakan coba unggah ulang.',
                ], 422);
            }
        }

        if (function_exists('set_time_limit')) {
            @set_time_limit(180);
        }

        $disk = config('filesystems.archive_disk', 'public');
        $folder = $this->resolveAssetFolder($order);
        $fileName = $this->resolveUniqueAssetFilename(
            $disk,
            $folder,
            $request->input('file_type'),
            $request->input('original_name')
        );
        $targetPath = "{$folder}/{$fileName}";

        try {
            $mergedFullPath = Storage::disk('local')->path("{$chunkDirRelative}/merged.tmp");
            $outHandle = fopen($mergedFullPath, 'wb');
            if ($outHandle === false) {
                throw new \RuntimeException('Gagal membuat berkas sementara di server.');
            }

            try {
                for ($i = 0; $i < $totalChunks; $i++) {
                    $partFullPath = Storage::disk('local')->path("{$chunkDirRelative}/part_{$i}");
                    $inHandle = fopen($partFullPath, 'rb');
                    if ($inHandle === false) {
                        throw new \RuntimeException("Gagal membaca potongan berkas #{$i}.");
                    }
                    try {
                        while (!feof($inHandle)) {
                            $buffer = fread($inHandle, 65536);
                            if ($buffer !== false && $buffer !== '') {
                                fwrite($outHandle, $buffer);
                            }
                        }
                    } finally {
                        fclose($inHandle);
                    }
                }
            } finally {
                fclose($outHandle);
            }

            $actualSize = (int) filesize($mergedFullPath);
            if ($actualSize > 524288000) {
                throw new \RuntimeException('Ukuran berkas melebihi batas maksimal 500MB.');
            }

            $readStream = fopen($mergedFullPath, 'rb');
            if ($readStream === false) {
                throw new \RuntimeException('Gagal membuka berkas hasil penggabungan.');
            }
            try {
                Storage::disk($disk)->put($targetPath, $readStream);
            } finally {
                if (is_resource($readStream)) {
                    fclose($readStream);
                }
            }
        } finally {
            Storage::disk('local')->deleteDirectory($chunkDirRelative);
        }

        $asset = $this->recordDesignAsset(
            $job,
            $user->id,
            $request->input('file_type'),
            $targetPath,
            $disk,
            $fileName,
            $actualSize
        );

        $batchCount = max(1, (int) $request->input('uploaded_files_count', 1));
        $message = $batchCount === 1
            ? "File {$fileName} berhasil diunggah! Status tiket otomatis diperbarui."
            : "{$batchCount} file desain berhasil diunggah! Status tiket otomatis diperbarui.";

        if ($request->boolean('is_last_file_in_batch', true)) {
            session()->flash('success', $message);
        }

        return response()->json([
            'success' => true,
            'completed' => true,
            'message' => $message,
            'file_name' => $fileName,
            'count' => $batchCount,
            'asset_id' => $asset->id,
        ]);
    }

    protected function resolveAssetFolder(Order $order): string
    {
        $branch = $order->branch;
        $ym = date('Y-m');
        return "designs/{$branch->code}/{$ym}/{$order->order_number}";
    }

    protected function resolveUniqueAssetFilename(
        string $disk,
        string $folder,
        string $fileType,
        string $originalName,
        int $batchIndex = 0
    ): string {
        $prefix = match ($fileType) {
            'final_print' => 'print',
            'preview_proof' => 'proof',
            'brief_ref' => 'ref',
        };

        $cleanOriginalName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($originalName));
        if ($cleanOriginalName === '' || $cleanOriginalName === '.' || $cleanOriginalName === '..') {
            $cleanOriginalName = 'design_file';
        }

        $fileName = "{$prefix}_{$cleanOriginalName}";
        if (Storage::disk($disk)->exists("{$folder}/{$fileName}")) {
            $ext = pathinfo($cleanOriginalName, PATHINFO_EXTENSION);
            $nameOnly = pathinfo($cleanOriginalName, PATHINFO_FILENAME);
            $uniqueSuffix = '_' . time() . '_' . ($batchIndex + 1);
            $fileName = "{$prefix}_{$nameOnly}{$uniqueSuffix}" . ($ext ? ".{$ext}" : '');
        }

        return $fileName;
    }

    protected function recordDesignAsset(
        DesignJob $job,
        int $userId,
        string $fileType,
        string $path,
        string $disk,
        string $fileName,
        int $fileSize
    ): DesignAsset {
        $asset = DesignAsset::create([
            'design_job_id' => $job->id,
            'uploaded_by' => $userId,
            'file_type' => $fileType,
            'file_path' => $path,
            'disk' => $disk,
            'file_name' => $fileName,
            'file_size' => $fileSize,
        ]);

        if ($fileType === 'final_print') {
            $job->order->work_status = 'ready_to_print';
            $job->order->save();
        }

        return $asset;
    }

    protected function purgeStaleChunkDirectories(): void
    {
        try {
            $cutoff = now()->subHours(24)->timestamp;
            foreach (Storage::disk('local')->directories('chunks') as $dir) {
                $fullPath = Storage::disk('local')->path($dir);
                if (is_dir($fullPath)) {
                    $mtime = @filemtime($fullPath);
                    if ($mtime !== false && $mtime < $cutoff) {
                        Storage::disk('local')->deleteDirectory($dir);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Do not block active uploads if cleanup fails
        }
    }

    public function claimJob(DesignJob $job)
    {
        $user = Auth::user();
        if (!$user->isOwner() && $job->order->branch_id !== $user->branch_id) {
            abort(403, 'Akses ditolak.');
        }

        $job->designer_id = $user->id;
        $job->save();

        if ($job->order->work_status === 'draft') {
            $job->order->work_status = 'in_progress';
            $job->order->save();
        }

        return back()->with('success', "Tiket berhasil diambil alih oleh Anda.");
    }
}
