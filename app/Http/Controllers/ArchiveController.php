<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\DesignAsset;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ArchiveController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = DesignAsset::with(['designJob.order.branch', 'designJob.assets:id,design_job_id,file_type', 'uploader'])->latest();

        if (!$user->isOwner()) {
            $query->whereHas('designJob.order', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        } elseif ($request->filled('branch_id')) {
            $query->whereHas('designJob.order', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        if ($request->filled('file_type')) {
            $query->where('file_type', $request->file_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('file_name', 'like', "%{$search}%")
                    ->orWhereHas('designJob.order', function ($sub) use ($search) {
                        $sub->where('order_number', 'like', "%{$search}%")
                            ->orWhere('customer_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('designJob', function ($sub) use ($search) {
                        $sub->where('title', 'like', "%{$search}%");
                    });
            });
        }

        $assets = $query->paginate(18)->withQueryString();
        $branches = Branch::all();

        // Calculate total storage used for the authorized scope
        $statsQuery = DesignAsset::query();
        if (!$user->isOwner()) {
            $statsQuery->whereHas('designJob.order', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        } elseif ($request->filled('branch_id')) {
            $statsQuery->whereHas('designJob.order', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        $totalBytes = (clone $statsQuery)->sum('file_size');
        $totalCount = (clone $statsQuery)->count();

        return view('archive.index', compact('assets', 'branches', 'totalBytes', 'totalCount'));
    }

    public function download(DesignAsset $asset)
    {
        $user = Auth::user();
        $order = $asset->designJob->order;

        if (!$user->isOwner() && $order->branch_id !== $user->branch_id) {
            abort(403, 'Akses file ditolak.');
        }

        $disk = $asset->disk ?: config('filesystems.archive_disk', 'public');

        if (!Storage::disk($disk)->exists($asset->file_path)) {
            // Fallback check to public local disk if file was stored locally
            if ($disk !== 'public' && Storage::disk('public')->exists($asset->file_path)) {
                $disk = 'public';
            } else {
                abort(404, 'File tidak ditemukan di disk penyimpanan.');
            }
        }

        return Storage::disk($disk)->download($asset->file_path, $asset->file_name);
    }

    public function preview(DesignAsset $asset)
    {
        $user = Auth::user();
        $order = $asset->designJob->order;

        if (!$user->isOwner() && $order->branch_id !== $user->branch_id) {
            abort(403, 'Akses file ditolak.');
        }

        $disk = $asset->disk ?: config('filesystems.archive_disk', 'public');

        if (!Storage::disk($disk)->exists($asset->file_path)) {
            // Fallback check to public local disk if file was stored locally
            if ($disk !== 'public' && Storage::disk('public')->exists($asset->file_path)) {
                $disk = 'public';
            } else {
                abort(404, 'File tidak ditemukan di disk penyimpanan.');
            }
        }

        return Storage::disk($disk)->response($asset->file_path, $asset->file_name);
    }

    /**
     * Delete a design asset permanently from storage and database.
     */
    public function destroy(Request $request, DesignAsset $asset)
    {
        $user = Auth::user();
        $job = $asset->designJob;
        $order = $job?->order;

        if (!$job || !$order) {
            abort(404, 'Tiket order untuk berkas ini tidak ditemukan.');
        }

        if (!$user->isOwner() && $order->branch_id !== $user->branch_id) {
            abort(403, 'Akses ditolak. Anda tidak berhak menghapus berkas cabang lain.');
        }

        if ($order->isLocked()) {
            $lockedMsg = 'Order sudah selesai atau dibatalkan. Berkas arsip terkunci dan tidak dapat dihapus kecuali tiket dibuka kembali oleh Owner.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $lockedMsg,
                ], 422);
            }
            return back()->with('error', $lockedMsg);
        }

        $fileName = $asset->file_name;
        $statusReverted = false;

        DB::transaction(function () use ($asset, $job, $order, &$statusReverted) {
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->first();
            $wasFinalPrint = ($asset->file_type === 'final_print');

            $asset->delete();

            if ($wasFinalPrint && $lockedOrder && $lockedOrder->work_status === 'ready_to_print') {
                $remainingFinalCount = DesignAsset::where('design_job_id', $job->id)
                    ->where('file_type', 'final_print')
                    ->count();

                if ($remainingFinalCount === 0) {
                    $lockedOrder->work_status = 'in_progress';
                    $lockedOrder->save();
                    $statusReverted = true;
                }
            }
        });

        $message = $statusReverted
            ? "Berkas {$fileName} berhasil dihapus. Karena tidak ada file cetak final tersisa, status order dikembalikan ke Proses Desain."
            : "Berkas {$fileName} berhasil dihapus dari arsip & penyimpanan server.";

        if ($request->expectsJson() || $request->ajax()) {
            session()->flash('success', $message);
            return response()->json([
                'success' => true,
                'message' => $message,
                'status_reverted' => $statusReverted,
            ]);
        }

        return back()->with('success', $message);
    }
}
