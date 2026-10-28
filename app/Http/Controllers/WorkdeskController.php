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
        $query = DesignJob::with(['order.branch', 'order.creator', 'designer', 'assets.uploader']);

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

        $jobs = $query->orderBy('deadline_at', 'asc')->paginate(12)->withQueryString();
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

        $request->validate([
            'file' => 'required|file|max:512000', // max 500MB
            'file_type' => 'required|in:final_print,preview_proof,brief_ref',
        ]);

        $file = $request->file('file');
        $branch = $order->branch;
        $ym = date('Y-m');

        // Path structure: storage/designs/{branch_code}/{YYYY-MM}/{order_number}/{prefix}_{file_name}
        $prefix = match ($request->file_type) {
            'final_print' => 'print',
            'preview_proof' => 'proof',
            'brief_ref' => 'ref',
        };

        $cleanOriginalName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file->getClientOriginalName());
        $fileName = "{$prefix}_{$cleanOriginalName}";
        $folder = "designs/{$branch->code}/{$ym}/{$order->order_number}";
        $disk = config('filesystems.archive_disk', 'public');
        $path = $file->storeAs($folder, $fileName, $disk);

        $asset = DesignAsset::create([
            'design_job_id' => $job->id,
            'uploaded_by' => $user->id,
            'file_type' => $request->file_type,
            'file_path' => $path,
            'disk' => $disk,
            'file_name' => $fileName,
            'file_size' => $file->getSize(),
        ]);

        // Auto update order status to ready_to_print if final_print
        if ($request->file_type === 'final_print') {
            $order->work_status = 'ready_to_print';
            $order->save();
        }

        if ($request->expectsJson() || $request->ajax()) {
            session()->flash('success', "File {$fileName} berhasil diunggah! Status tiket otomatis diperbarui.");
            return response()->json([
                'success' => true,
                'message' => "File {$fileName} berhasil diunggah! Status tiket otomatis diperbarui.",
                'file_name' => $fileName,
                'asset_id' => $asset->id,
            ]);
        }

        return back()->with('success', "File {$fileName} berhasil diunggah! Status tiket otomatis diperbarui.");
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
