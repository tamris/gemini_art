<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\DesignAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArchiveController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = DesignAsset::with(['designJob.order.branch', 'uploader'])->latest();

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

        // Calculate total storage used
        $totalBytes = DesignAsset::sum('file_size');
        $totalCount = DesignAsset::count();

        return view('archive.index', compact('assets', 'branches', 'totalBytes', 'totalCount'));
    }

    public function download(DesignAsset $asset)
    {
        $user = Auth::user();
        $order = $asset->designJob->order;

        if (!$user->isOwner() && $order->branch_id !== $user->branch_id) {
            abort(403, 'Akses file ditolak.');
        }

        if (!Storage::disk('public')->exists($asset->file_path)) {
            abort(404, 'File tidak ditemukan di disk penyimpanan.');
        }

        return Storage::disk('public')->download($asset->file_path, $asset->file_name);
    }
}
