<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CashMovement;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $branches = Branch::all();
        $defaultBranch = $user->isOwner() ? $branches->first() : $user->branch;

        // Common quick retail presets
        $presets = [
            ['name' => 'Fotokopi A4 HVS', 'price' => 300, 'category' => 'Fotokopi'],
            ['name' => 'Fotokopi F4/Folio HVS', 'price' => 400, 'category' => 'Fotokopi'],
            ['name' => 'Print Text Hitam Putih A4', 'price' => 1000, 'category' => 'Print Lembaran'],
            ['name' => 'Print Warna Dokumen A4', 'price' => 2500, 'category' => 'Print Lembaran'],
            ['name' => 'Print Art Paper A3+ (Poster)', 'price' => 12000, 'category' => 'Print Lembaran'],
            ['name' => 'Jilid Mika Lakban', 'price' => 5000, 'category' => 'Finishing'],
            ['name' => 'Jilid Spiral Kawat + Cover', 'price' => 15000, 'category' => 'Finishing'],
            ['name' => 'Laminating Panas A4', 'price' => 5000, 'category' => 'Finishing'],
            ['name' => 'Kertas Bufalo / Asturo Lembar', 'price' => 3000, 'category' => 'ATK'],
            ['name' => 'Map Kancing Plastik', 'price' => 4500, 'category' => 'ATK'],
        ];

        return view('pos.index', compact('branches', 'defaultBranch', 'presets'));
    }

    public function checkout(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'customer_name' => 'nullable|string|max:100',
            'payment_method' => 'required|in:cash,qris,transfer',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:150',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ];

        if ($user->isOwner()) {
            $rules['branch_id'] = 'required|exists:branches,id';
        }

        $data = $request->validate($rules);
        $branchId = $user->isOwner() ? $data['branch_id'] : $user->branch_id;
        $branch = Branch::findOrFail($branchId);

        return DB::transaction(function () use ($user, $branch, $data) {
            $totalAmount = 0;
            foreach ($data['items'] as $item) {
                $totalAmount += ((int) $item['qty']) * ((float) $item['price']);
            }

            // Generate POS Order Number
            $ym = date('Ym');
            $prefix = "POS-{$branch->code}-{$ym}-";
            $lastOrder = Order::where('order_number', 'like', "{$prefix}%")
                ->orderBy('id', 'desc')
                ->first();

            $sequence = 1;
            if ($lastOrder && preg_match('/-(\d+)$/', $lastOrder->order_number, $matches)) {
                $sequence = ((int) $matches[1]) + 1;
            }
            $orderNumber = $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);

            $order = Order::create([
                'branch_id' => $branch->id,
                'user_id' => $user->id,
                'order_number' => $orderNumber,
                'customer_name' => !empty($data['customer_name']) ? $data['customer_name'] : 'Pelanggan Ritel (Walk-In)',
                'customer_phone' => null,
                'order_type' => 'retail_pos',
                'total_amount' => $totalAmount,
                'paid_amount' => $totalAmount,
                'payment_status' => 'paid',
                'work_status' => 'completed',
            ]);

            foreach ($data['items'] as $item) {
                $qty = (int) $item['qty'];
                $price = (float) $item['price'];
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_name' => $item['name'],
                    'qty' => $qty,
                    'unit_price' => $price,
                    'subtotal' => $qty * $price,
                ]);
            }

            // Record cash movement
            CashMovement::create([
                'branch_id' => $branch->id,
                'order_id' => $order->id,
                'user_id' => $user->id,
                'type' => 'in',
                'amount' => $totalAmount,
                'payment_method' => $data['payment_method'],
                'category' => 'retail_pos',
                'notes' => "Transaksi Kasir POS Ritel {$orderNumber}",
            ]);

            return redirect()->route('orders.show', $order->id)
                ->with('success', "Transaksi POS {$orderNumber} berhasil dicatat (Total: Rp " . number_format($totalAmount, 0, ',', '.') . ")!");
        });
    }
}
