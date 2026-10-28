<?php

namespace App\Http\Controllers;

use App\Mail\VendorPrintDispatchMail;
use App\Models\DesignAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class VendorShareController extends Controller
{
    /**
     * Public portal for print vendor to inspect and download print-ready asset.
     * No authentication required.
     */
    public function show(string $token)
    {
        $asset = DesignAsset::where('share_token', $token)
            ->with(['designJob.order.branch', 'uploader'])
            ->firstOrFail();

        $job = $asset->designJob;
        $order = $job->order;

        return view('vendor.share', compact('asset', 'job', 'order'));
    }

    /**
     * Direct download endpoint for print vendor.
     */
    public function download(string $token)
    {
        $asset = DesignAsset::where('share_token', $token)->firstOrFail();
        $disk = $asset->disk ?: config('filesystems.archive_disk', 'public');

        if (!Storage::disk($disk)->exists($asset->file_path)) {
            // Fallback to public disk if stored before R2 migration
            $disk = 'public';
        }

        return Storage::disk($disk)->download($asset->file_path, $asset->file_name);
    }

    /**
     * Inline preview endpoint for print vendor (images & pdf).
     */
    public function preview(string $token)
    {
        $asset = DesignAsset::where('share_token', $token)->firstOrFail();
        $disk = $asset->disk ?: config('filesystems.archive_disk', 'public');

        if (!Storage::disk($disk)->exists($asset->file_path)) {
            $disk = 'public';
        }

        return Storage::disk($disk)->response($asset->file_path, $asset->file_name);
    }

    /**
     * Dispatch print order to vendor via Email from Owner/Staff.
     */
    public function sendEmail(Request $request, DesignAsset $asset)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $request->validate([
            'vendor_email' => 'required|email',
            'notes' => 'nullable|string|max:1000',
        ]);

        $asset->ensureShareToken();

        try {
            Mail::to($request->vendor_email)->send(
                new VendorPrintDispatchMail($asset, $request->notes, $user->name)
            );

            $msg = "Email order cetak berhasil dikirimkan ke {$request->vendor_email}!";

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                ]);
            }

            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            Log::error("Failed to send vendor dispatch email: " . $e->getMessage());

            $errMsg = "Gagal mengirim email: " . $e->getMessage();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errMsg,
                ], 500);
            }

            return back()->with('error', $errMsg);
        }
    }

    /**
     * Preview the rendered dispatch email in the browser.
     */
    public function previewEmail(DesignAsset $asset)
    {
        $asset->ensureShareToken();
        $mail = new VendorPrintDispatchMail(
            $asset,
            'Mohon diprioritaskan cetak segera ya mas operator.',
            Auth::user()?->name ?? 'Owner Gemini Art'
        );

        return $mail->render();
    }
}

