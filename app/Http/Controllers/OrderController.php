<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CashMovement;
use App\Models\DesignAsset;
use App\Models\DesignJob;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Order::with(['branch', 'creator', 'designJob.designer', 'items'])
            ->latest();

        // Branch restriction
        if (!$user->isOwner()) {
            $query->where('branch_id', $user->branch_id);
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Status filters
        if ($request->filled('work_status')) {
            $query->where('work_status', $request->work_status);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('order_type')) {
            $query->where('order_type', $request->order_type);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $perPage = in_array((int) $request->per_page, [10, 25, 50, 100]) ? (int) $request->per_page : 10;
        $orders = $query->paginate($perPage)->withQueryString();
        $branches = Branch::all();

        return view('orders.index', compact('orders', 'branches'));
    }

    public function create()
    {
        $user = Auth::user();
        $branches = Branch::all();

        // Design staff
        $designers = User::where('role', 'designer');
        if (!$user->isOwner()) {
            $designers->where('branch_id', $user->branch_id);
        }
        $designers = $designers->get();

        return view('orders.create-design', compact('branches', 'designers'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Sanitize nominal inputs before validation (removes dots, spaces, or currency prefixes)
        if ($request->has('total_amount')) {
            $request->merge([
                'total_amount' => preg_replace('/[^\d]/', '', (string) $request->input('total_amount')),
            ]);
        }
        if ($request->has('paid_amount')) {
            $request->merge([
                'paid_amount' => preg_replace('/[^\d]/', '', (string) $request->input('paid_amount')),
            ]);
        }

        $rules = [
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'title' => 'required|string|max:150',
            'dimensions' => 'nullable|string|max:50',
            'material_notes' => 'nullable|string|max:100',
            'brief_notes' => 'nullable|string',
            'deadline_at' => 'nullable|date',
            'designer_id' => 'nullable|exists:users,id',
            'total_amount' => 'required|numeric|min:1',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,qris,transfer',
            'brief_file' => 'nullable|file|max:51200', // max 50MB
        ];

        if ($user->isOwner()) {
            $rules['branch_id'] = 'required|exists:branches,id';
        }

        $data = $request->validate($rules);

        $branchId = $user->isOwner() ? $data['branch_id'] : $user->branch_id;
        $branch = Branch::findOrFail($branchId);

        return DB::transaction(function () use ($request, $user, $data, $branch) {
            $orderNumber = Order::generateOrderNumber($branch);
            $total = (float) $data['total_amount'];
            $paid = (float) $data['paid_amount'];

            // Determine payment status
            $paymentStatus = 'unpaid';
            if ($paid >= $total) {
                $paymentStatus = 'paid';
            } elseif ($paid > 0) {
                $paymentStatus = 'partial_dp';
            }

            // SOP: Tiket otomatis masuk ke antrean cabang terkait dengan status in_progress
            $order = Order::create([
                'branch_id' => $branch->id,
                'user_id' => $user->id,
                'order_number' => $orderNumber,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'order_type' => 'design_printing',
                'total_amount' => $total,
                'paid_amount' => $paid,
                'payment_status' => $paymentStatus,
                'work_status' => 'in_progress',
            ]);

            $designJob = DesignJob::create([
                'order_id' => $order->id,
                'designer_id' => $data['designer_id'] ?? null,
                'title' => $data['title'],
                'dimensions' => $data['dimensions'] ?? null,
                'material_notes' => $data['material_notes'] ?? null,
                'brief_notes' => $data['brief_notes'] ?? null,
                'deadline_at' => !empty($data['deadline_at']) ? Carbon::parse($data['deadline_at']) : null,
            ]);

            // Record cash movement if paid > 0
            if ($paid > 0) {
                CashMovement::create([
                    'branch_id' => $branch->id,
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'type' => 'in',
                    'amount' => $paid,
                    'payment_method' => $data['payment_method'],
                    'category' => $paymentStatus === 'paid' ? 'order_settlement' : 'order_dp',
                    'notes' => ($paymentStatus === 'paid' ? 'Pelunasan awal' : 'DP Order') . " {$orderNumber}",
                ]);
            }

            // Handle optional brief reference file upload
            if ($request->hasFile('brief_file')) {
                $file = $request->file('brief_file');
                $ym = date('Y-m');
                $originalName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file->getClientOriginalName());
                $fileName = "ref_" . $originalName;
                $folder = "designs/{$branch->code}/{$ym}/{$orderNumber}";
                $path = $file->storeAs($folder, $fileName, 'public');

                DesignAsset::create([
                    'design_job_id' => $designJob->id,
                    'uploaded_by' => $user->id,
                    'file_type' => 'brief_ref',
                    'file_path' => $path,
                    'file_name' => $fileName,
                    'file_size' => $file->getSize(),
                ]);
            }

            return redirect()->route('orders.show', $order->id)
                ->with('success', "Order tiket {$orderNumber} berhasil dibuat dan masuk antrean desainer!");
        });
    }

    public function show(Order $order)
    {
        $user = Auth::user();
        if (!$user->isOwner() && $order->branch_id !== $user->branch_id) {
            abort(403, 'Akses ke order cabang lain ditolak.');
        }

        $order->load([
            'branch',
            'creator',
            'designJob.designer',
            'designJob.assets.uploader',
            'items',
            'cashMovements.user',
        ]);

        return view('orders.show', compact('order'));
    }

    public function printReceipt(Order $order)
    {
        $user = Auth::user();
        if (!$user->isOwner() && $order->branch_id !== $user->branch_id) {
            abort(403, 'Akses ditolak.');
        }

        $order->load(['branch', 'creator', 'designJob', 'items', 'cashMovements']);
        return view('orders.receipt', compact('order'));
    }

    public function settlePayment(Request $request, Order $order)
    {
        $user = Auth::user();
        if (!$user->isOwner() && $order->branch_id !== $user->branch_id) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'payment_method' => 'required|in:cash,qris,transfer',
            'notes' => 'nullable|string|max:255',
        ]);

        $remaining = $order->remaining_amount;
        if ($remaining <= 0) {
            return back()->with('info', 'Order ini sudah lunas.');
        }

        DB::transaction(function () use ($order, $user, $remaining, $request) {
            $order->paid_amount += $remaining;
            $order->payment_status = 'paid';
            $order->work_status = 'completed'; // Delivered & settled
            $order->save();

            CashMovement::create([
                'branch_id' => $order->branch_id,
                'order_id' => $order->id,
                'user_id' => $user->id,
                'type' => 'in',
                'amount' => $remaining,
                'payment_method' => $request->payment_method,
                'category' => 'order_settlement',
                'notes' => ($request->notes ? $request->notes . ' - ' : '') . "Pelunasan sisa tagihan order {$order->order_number}",
            ]);
        });

        return back()->with('success', "Pelunasan sebesar Rp " . number_format($remaining, 0, ',', '.') . " berhasil dicatat! Status order kini Selesai.");
    }

    public function updateStatus(Request $request, Order $order)
    {
        $user = Auth::user();
        if (!$user->isOwner() && $order->branch_id !== $user->branch_id) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'work_status' => 'required|in:draft,in_progress,revision,ready_to_print,completed,canceled',
        ]);

        $order->work_status = $request->work_status;
        $order->save();

        return back()->with('success', "Status order diperbarui menjadi: " . $order->status_label);
    }
}
