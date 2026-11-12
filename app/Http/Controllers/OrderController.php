<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CashMovement;
use App\Models\DesignAsset;
use App\Models\DesignJob;
use App\Models\Order;
use App\Models\OrderItem;
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
        $query = Order::with([
            'branch',
            'creator',
            'designJob.designer',
            'designJob.finalPrintAssets:id,design_job_id,file_type,created_at',
            'designJob.emailSender:id,name',
            'items',
        ])->latest();

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
        if ($request->filled('cost_status')) {
            if ($request->cost_status === 'missing') {
                $query->whereNull('total_cost');
            } elseif ($request->cost_status === 'complete') {
                $query->whereNotNull('total_cost');
            }
        }
        if ($request->filled('order_type')) {
            $query->where('order_type', $request->order_type);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $rawPhone = preg_replace('/\D/', '', $search);
            $query->where(function ($q) use ($search, $rawPhone) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
                if (strlen($rawPhone) >= 4) {
                    $q->orWhereRaw("REPLACE(REPLACE(customer_phone, '-', ''), ' ', '') LIKE ?", ["%{$rawPhone}%"]);
                }
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
        if ($request->has('main_price')) {
            $request->merge([
                'main_price' => preg_replace('/[^\d]/', '', (string) $request->input('main_price')),
            ]);
        }
        if ($request->has('design_fee')) {
            $request->merge([
                'design_fee' => preg_replace('/[^\d]/', '', (string) $request->input('design_fee')),
            ]);
        }
        if ($request->has('main_cost_price')) {
            $rawCost = (string) $request->input('main_cost_price');
            $cleanCost = preg_replace('/[^\d]/', '', $rawCost);
            $request->merge([
                'main_cost_price' => ($rawCost !== '' && $cleanCost !== '') ? (float) $cleanCost : null,
            ]);
        }
        if ($request->has('main_unit_price')) {
            $request->merge([
                'main_unit_price' => preg_replace('/[^\d]/', '', (string) $request->input('main_unit_price')),
            ]);
        }
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

        // Sanitize add-on items
        if ($request->has('items') && is_array($request->input('items'))) {
            $cleanItems = [];
            foreach ($request->input('items') as $it) {
                if (!empty($it['name'])) {
                    $rawCost = isset($it['cost_price']) ? (string) $it['cost_price'] : '';
                    $cleanCost = preg_replace('/[^\d]/', '', $rawCost);
                    $cleanItems[] = [
                        'name' => trim($it['name']),
                        'qty' => max(1, (int) ($it['qty'] ?? 1)),
                        'price' => (float) preg_replace('/[^\d]/', '', (string) ($it['price'] ?? 0)),
                        'cost_price' => ($rawCost !== '' && $cleanCost !== '') ? (float) $cleanCost : null,
                    ];
                }
            }
            $request->merge(['items' => $cleanItems]);
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
            'design_fee' => 'nullable|numeric|min:0',
            'main_qty' => 'nullable|integer|min:1',
            'main_unit' => 'nullable|string|max:30',
            'main_unit_price' => 'nullable|numeric|min:0',
            'main_cost_price' => 'nullable|numeric|min:0',
            'main_price' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:1',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,qris,transfer',
            'brief_file' => 'nullable|file|max:51200', // max 50MB
            'items' => 'nullable|array',
            'items.*.name' => 'required_with:items|string|max:150',
            'items.*.qty' => 'required_with:items|integer|min:1',
            'items.*.price' => 'required_with:items|numeric|min:0',
            'items.*.cost_price' => 'nullable|numeric|min:0',
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

            // Calculate main service price vs add-ons
            $extraTotal = 0;
            if (!empty($data['items'])) {
                foreach ($data['items'] as $it) {
                    $extraTotal += $it['qty'] * $it['price'];
                }
            }
            $mainPrice = !empty($data['main_price']) ? (float) $data['main_price'] : max(0, $total - $extraTotal);

            // Determine payment status
            $paymentStatus = 'unpaid';
            if ($paid >= $total) {
                $paymentStatus = 'paid';
            } elseif ($paid > 0) {
                $paymentStatus = 'partial_dp';
            }

            $mainQty = max(1, (int) ($request->input('main_qty', 1)));
            $mainUnit = trim((string) $request->input('main_unit', '')) ?: 'Pcs';
            $mainSubtotal = (float) $mainPrice;
            $mainUnitPrice = $request->filled('main_unit_price') && (float) $request->input('main_unit_price') > 0
                ? (float) $request->input('main_unit_price')
                : ($mainQty > 0 ? round($mainSubtotal / $mainQty, 2) : $mainSubtotal);
            $mainCostPrice = $request->filled('main_cost_price') ? (float) $request->input('main_cost_price') : null;

            // Determine if full HPP/modal is known upfront
            $allHaveCost = ($mainCostPrice !== null);
            $totalCost = ($mainCostPrice !== null) ? ($mainQty * $mainCostPrice) : 0;

            if (!empty($data['items'])) {
                foreach ($data['items'] as $extra) {
                    if (!isset($extra['cost_price']) || $extra['cost_price'] === null) {
                        $allHaveCost = false;
                    } else {
                        $totalCost += ((int) $extra['qty']) * ((float) $extra['cost_price']);
                    }
                }
            }

            $orderTotalCost = $allHaveCost ? $totalCost : null;

            // SOP: Tiket otomatis masuk ke antrean cabang terkait dengan status in_progress
            $order = Order::create([
                'branch_id' => $branch->id,
                'user_id' => $user->id,
                'order_number' => $orderNumber,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'order_type' => 'design_printing',
                'total_amount' => $total,
                'total_cost' => $orderTotalCost,
                'paid_amount' => $paid,
                'payment_status' => $paymentStatus,
                'work_status' => 'in_progress',
            ]);

            $designJob = DesignJob::create([
                'order_id' => $order->id,
                'designer_id' => $data['designer_id'] ?? null,
                'title' => $data['title'],
                'design_fee' => (float) ($data['design_fee'] ?? 0),
                'dimensions' => $data['dimensions'] ?? null,
                'material_notes' => $data['material_notes'] ?? null,
                'brief_notes' => $data['brief_notes'] ?? null,
                'deadline_at' => !empty($data['deadline_at']) ? Carbon::parse($data['deadline_at']) : null,
            ]);

            // Save Line Item #1: Main Design & Print service
            OrderItem::create([
                'order_id' => $order->id,
                'item_name' => $data['title'],
                'qty' => $mainQty,
                'unit' => $mainUnit,
                'unit_price' => $mainUnitPrice,
                'cost_price' => $mainCostPrice,
                'subtotal' => $mainSubtotal,
            ]);

            // Save Line Items #2..N: Add-on tools / items
            if (!empty($data['items'])) {
                foreach ($data['items'] as $extra) {
                    $itemSubtotal = $extra['qty'] * $extra['price'];
                    OrderItem::create([
                        'order_id' => $order->id,
                        'item_name' => $extra['name'],
                        'qty' => $extra['qty'],
                        'unit_price' => $extra['price'],
                        'cost_price' => $extra['cost_price'] ?? null,
                        'subtotal' => $itemSubtotal,
                    ]);
                }
            }

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
                $disk = config('filesystems.archive_disk', 'public');
                $path = $file->storeAs($folder, $fileName, $disk);

                DesignAsset::create([
                    'design_job_id' => $designJob->id,
                    'uploaded_by' => $user->id,
                    'file_type' => 'brief_ref',
                    'file_path' => $path,
                    'disk' => $disk,
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
            'designJob.emailSender:id,name',
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

    public function printInvoice(Order $order)
    {
        $user = Auth::user();
        if (!$user->isOwner() && $order->branch_id !== $user->branch_id) {
            abort(403, 'Akses ditolak.');
        }

        $order->load(['branch', 'creator', 'designJob.designer', 'items', 'cashMovements']);
        return view('orders.invoice', compact('order'));
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
            $order->completed_at = now();
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

        // State Guard: If order is locked (completed or canceled)
        if ($order->isLocked()) {
            if (!$user->isOwner()) {
                return back()->with('error', "Status tiket sudah {$order->status_label} dan dikunci. Hanya Owner yang berwenang membuka kembali tiket ini.");
            }

            if ($request->work_status === 'reopen') {
                $request->validate([
                    'reopen_reason' => 'required|string|max:1000',
                    'target_status' => 'required|in:in_progress,revision',
                ], [
                    'reopen_reason.required' => 'Alasan membuka kembali tiket wajib diisi.',
                ]);

                $order->work_status = $request->target_status;
                if ($order->isCompleted()) {
                    $order->completed_at = null;
                }
                $order->save();

                return back()->with('success', "Tiket {$order->order_number} berhasil dibuka kembali oleh Owner ke status {$order->status_label}.");
            }

            return back()->with('error', "Status tiket sudah dikunci.");
        }

        $request->validate([
            'work_status' => 'required|in:draft,in_progress,revision,ready_to_print,completed,canceled',
        ]);

        // Guard: Cannot mark as completed if still unpaid
        if ($request->work_status === 'completed') {
            if ($order->remaining_amount > 0) {
                return back()->with('error', "Pesanan belum dapat ditandai Selesai / Diambil Customer karena masih memiliki sisa tagihan sebesar Rp " . number_format($order->remaining_amount, 0, ',', '.') . ". Harap selesaikan pelunasan pembayaran terlebih dahulu di kartu Status Pembayaran!");
            }

            $order->work_status = 'completed';
            $order->completed_at = now();
            $order->save();

            return back()->with('success', "Status order diperbarui menjadi Selesai & Diambil Customer. Tiket telah dikunci.");
        }

        // Handling: Revision status requires notes & increments revision count
        if ($request->work_status === 'revision') {
            $request->validate([
                'revision_notes' => 'required|string|max:1000',
            ], [
                'revision_notes.required' => 'Catatan revisi dari pelanggan wajib diisi agar desainer mengetahui bagian yang perlu diperbaiki.',
            ]);

            if ($order->designJob) {
                $order->designJob->revision_notes = $request->revision_notes;
                $order->designJob->increment('revision_count');
                $order->designJob->save();
            }

            $order->work_status = 'revision';
            $order->save();

            $revCount = $order->designJob ? $order->designJob->revision_count : 1;
            return back()->with('success', "Status tiket diperbarui menjadi Revisi (ke-{$revCount}). Tiket otomatis diprioritaskan di antrean desainer!");
        }

        // Handling: Cancellation with refund or forfeit
        if ($request->work_status === 'canceled') {
            $request->validate([
                'cancellation_reason' => 'required|string|max:1000',
            ], [
                'cancellation_reason.required' => 'Alasan pembatalan pesanan wajib diisi.',
            ]);

            if ($order->paid_amount > 0) {
                $request->validate([
                    'cancellation_type' => 'required|in:refund,forfeit',
                    'refund_method' => 'nullable|in:cash,qris,transfer',
                ], [
                    'cancellation_type.required' => 'Pilih perlakuan uang muka (DP): apakah di-refund ke pelanggan atau hangus.',
                ]);

                if ($request->cancellation_type === 'refund') {
                    $refundAmount = (float) $order->paid_amount;

                    DB::transaction(function () use ($order, $user, $request, $refundAmount) {
                        CashMovement::create([
                            'branch_id' => $order->branch_id,
                            'order_id' => $order->id,
                            'user_id' => $user->id,
                            'type' => 'out',
                            'amount' => $refundAmount,
                            'payment_method' => $request->refund_method ?? 'cash',
                            'category' => 'order_refund',
                            'notes' => "Refund DP pembatalan order {$order->order_number}: " . $request->cancellation_reason,
                        ]);

                        $order->cancellation_type = 'refund';
                        $order->cancellation_reason = $request->cancellation_reason;
                        $order->work_status = 'canceled';
                        $order->save();
                    });

                    return back()->with('success', "Pesanan {$order->order_number} berhasil dibatalkan. Pengeluaran kas refund sebesar Rp " . number_format($refundAmount, 0, ',', '.') . " telah dicatat di Buku Kas Laci.");
                }

                $order->cancellation_type = 'forfeit';
            } else {
                $order->cancellation_type = 'none';
            }

            $order->cancellation_reason = $request->cancellation_reason;
            $order->work_status = 'canceled';
            $order->save();

            return back()->with('success', "Pesanan {$order->order_number} berhasil dibatalkan.");
        }

        // Regular transition (draft, in_progress, ready_to_print)
        $order->work_status = $request->work_status;
        $order->save();

        return back()->with('success', "Status order diperbarui menjadi: " . $order->status_label);
    }

    public function updateCost(Request $request, Order $order)
    {
        $user = Auth::user();
        if (!$user->isOwner() && $order->branch_id !== $user->branch_id) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:order_items,id',
        ]);

        foreach ($request->input('items') as $itemData) {
            $raw = isset($itemData['cost_price']) ? (string) $itemData['cost_price'] : '';
            $clean = preg_replace('/[^\d]/', '', $raw);
            $costPrice = ($raw !== '' && $clean !== '') ? (float) $clean : null;

            $order->items()->where('id', $itemData['id'])->update([
                'cost_price' => $costPrice,
            ]);
        }

        $order->load('items');
        $order->recalculateTotalCost();

        return back()->with('success', "Harga modal (HPP) pesanan {$order->order_number} berhasil diperbarui!");
    }
}
