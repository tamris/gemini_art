@extends('layouts.app')

@section('title', 'Kasir Ritel POS (Fotokopi, ATK & Print Lembaran)')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b-2 border-black">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-black uppercase tracking-wider text-emerald-800">
                    Kasir Transaksi Cepat & Ritel POS
                </span>
            </div>
            <h1 class="text-2xl font-black text-black">Terminal Kasir POS (FC, ATK & Cetak Lembaran)</h1>
            <!-- <p class="text-xs font-semibold text-slate-600 mt-0.5">
                Pencatatan ritel mandiri untuk mencegah omset tercecer saat toko ramai.
            </p> -->
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-black uppercase px-2.5 py-1 rounded-lg border-2 border-black bg-white shadow-[2px_2px_0px_0px_#000]">
                Cabang: {{ $defaultBranch->name ?? 'Cabang' }}
            </span>
        </div>
    </div>

    <!-- Main Split POS Screen: Left Catalog & Custom Input, Right Dynamic Cart -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Column: Presets & Custom Input (7 cols) -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Quick Presets Grid -->
            <div class="neo-box p-6 bg-white space-y-4">
                <div class="flex items-center justify-between pb-3 border-b-2 border-black">
                    <h2 class="font-black text-base text-black flex items-center gap-2.5">
                        <div class="w-7 h-7 bg-amber-300 border-2 border-black rounded-lg flex items-center justify-center text-black shadow-[1px_1px_0px_0px_#000]">
                            <x-icon name="sparkles" class="w-4 h-4" />
                        </div>
                        <span>Layanan Cepat / Item Favorit</span>
                    </h2>
                    <span class="text-xs font-semibold text-slate-500">Klik untuk tambah ke keranjang</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($presets as $p)
                    <button type="button" 
                            onclick="addItemToCart('{{ addslashes($p['name']) }}', 1, {{ $p['price'] }})"
                            class="p-3 bg-slate-50 hover:bg-amber-100 border-2 border-black rounded-xl text-left shadow-[2px_2px_0px_0px_#000] active:translate-x-[2px] active:translate-y-[2px] active:shadow-none transition-all cursor-pointer flex flex-col justify-between">
                        <div>
                            <span class="text-[9px] font-black uppercase px-1.5 py-0.2 rounded border border-black bg-white">
                                {{ $p['category'] }}
                            </span>
                            <div class="font-black text-xs text-black mt-1 leading-snug">{{ $p['name'] }}</div>
                        </div>
                        <div class="font-black text-sm text-emerald-700 mt-2">
                            Rp {{ number_format($p['price'], 0, ',', '.') }}
                        </div>
                    </button>
                    @endforeach
                </div>
            </div>

            <!-- Custom Item Input Card -->
            <div class="neo-box p-6 bg-white space-y-3">
                <h2 class="font-black text-base text-black pb-2 border-b-2 border-black flex items-center gap-2.5">
                    <div class="w-7 h-7 bg-blue-300 border-2 border-black rounded-lg flex items-center justify-center text-black shadow-[1px_1px_0px_0px_#000]">
                        <x-icon name="plus" class="w-4 h-4" stroke-width="2.5" />
                    </div>
                    <span>Input Layanan / Barang Manual</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    <div class="sm:col-span-6">
                        <label class="block text-[11px] font-black uppercase text-slate-800 mb-1 tracking-wider">Nama Barang / Jasa</label>
                        <input type="text" id="custom-name" placeholder="Misal: Print Kalkir A3 / Map Snellhecter" class="w-full h-10 neo-input text-xs">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-black uppercase text-slate-800 mb-1 tracking-wider">Qty</label>
                        <input type="number" id="custom-qty" value="1" min="1" class="w-full h-10 neo-input text-xs font-bold text-center">
                    </div>
                    <div class="sm:col-span-4">
                        <label class="block text-[11px] font-black uppercase text-slate-800 mb-1 tracking-wider">Harga Satuan</label>
                        <div class="flex h-10 rounded-xl overflow-hidden border-[1.5px] border-slate-300 bg-white shadow-[1.5px_1.5px_0px_0px_#e2e8f0] focus-within:border-slate-800 focus-within:shadow-[2.5px_2.5px_0px_0px_#0f172a] transition-all">
                            <span class="inline-flex items-center px-3 bg-amber-200 border-r-[1.5px] border-slate-300 text-xs font-black text-slate-800 select-none">
                                Rp
                            </span>
                            <input type="text" 
                                   id="custom-price" 
                                   inputmode="numeric" 
                                   oninput="formatRupiahInput(this)" 
                                   placeholder="0" 
                                   autocomplete="off"
                                   class="w-full px-3 text-xs font-bold text-slate-900 bg-transparent focus:outline-none">
                        </div>
                    </div>
                </div>

                <div class="pt-2 text-right">
                    <button type="button" onclick="addCustomItem()" class="neo-btn bg-sky-400 hover:bg-sky-300 text-xs py-2 px-4 font-black text-black flex items-center gap-1.5 ml-auto cursor-pointer">
                        <x-icon name="plus" class="w-3.5 h-3.5" stroke-width="2.5" />
                        <span>Tambah ke Keranjang</span>
                    </button>
                </div>
            </div>

        </div>

        <!-- Right Column: Interactive Cart & Checkout (5 cols) -->
        <div class="lg:col-span-5">
            <div class="neo-box p-6 bg-white space-y-4 sticky top-24">
                
                <div class="flex items-center justify-between pb-3 border-b-2 border-black">
                    <h2 class="font-black text-base text-black flex items-center gap-2.5">
                        <div class="w-7 h-7 bg-emerald-400 border-2 border-black rounded-lg flex items-center justify-center text-black shadow-[1px_1px_0px_0px_#000]">
                            <x-icon name="shopping-cart" class="w-4 h-4" />
                        </div>
                        <span>Keranjang Transaksi</span>
                    </h2>
                    <button type="button" onclick="clearCart()" class="text-xs font-bold text-rose-600 hover:underline cursor-pointer">
                        Kosongkan
                    </button>
                </div>

                <!-- Cart Items Container -->
                <div id="cart-items-container" class="space-y-2 max-h-72 overflow-y-auto pr-1">
                    <div id="empty-cart-msg" class="py-8 text-center text-xs font-semibold text-slate-400 border-2 border-dashed border-slate-300 rounded-xl">
                        Keranjang masih kosong.<br>Pilih layanan cepat atau input barang manual.
                    </div>
                </div>

                <!-- Checkout Form -->
                <form id="checkout-form" method="POST" action="{{ route('pos.checkout') }}" class="pt-3 border-t-2 border-black space-y-3">
                    @csrf
                    
                    @if(Auth::user()->isOwner())
                    <div>
                        <label class="block text-[11px] font-black uppercase text-black mb-1">Cabang Transaksi</label>
                        <select name="branch_id" class="neo-select">
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->code }} - {{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div>
                        <label class="block text-[11px] font-black uppercase text-slate-800 mb-1 tracking-wider">Nama Pelanggan (Opsional)</label>
                        <input type="text" name="customer_name" placeholder="Pelanggan Ritel Walk-In" class="w-full h-10 neo-input text-xs font-medium">
                    </div>

                    <div class="p-3.5 bg-amber-50 border-2 border-black rounded-xl space-y-1.5 text-xs shadow-[2px_2px_0px_0px_#000]">
                        <div class="flex justify-between items-center text-base">
                            <span class="font-black text-black">TOTAL BELANJA:</span>
                            <span id="cart-total-text" class="font-black text-black">Rp 0</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 items-end">
                        <div>
                            <label class="block text-[11px] font-black uppercase text-slate-800 mb-1 tracking-wider">Metode Bayar</label>
                            <select name="payment_method" id="payment-method" required class="neo-select h-10">
                                <option value="cash">Tunai (Cash Laci)</option>
                                <option value="qris">QRIS</option>
                                <option value="transfer">Transfer Bank</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-black uppercase text-slate-800 mb-1 tracking-wider">Uang Diterima</label>
                            <div class="flex h-10 rounded-xl overflow-hidden border-[1.5px] border-slate-300 bg-white shadow-[1.5px_1.5px_0px_0px_#e2e8f0] focus-within:border-slate-800 focus-within:shadow-[2.5px_2.5px_0px_0px_#0f172a] transition-all">
                                <span class="inline-flex items-center px-3 bg-emerald-200 border-r-[1.5px] border-slate-300 text-xs font-black text-slate-800 select-none">
                                    Rp
                                </span>
                                <input type="text" 
                                       id="cash-received" 
                                       inputmode="numeric" 
                                       oninput="formatCashReceived(this)" 
                                       placeholder="0" 
                                       autocomplete="off"
                                       class="w-full px-3 text-xs font-bold text-slate-900 bg-transparent focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- Fast Cash Presets -->
                    <div class="flex flex-wrap items-center gap-1">
                        <button type="button" onclick="setCashPreset('exact')" class="px-2 py-0.5 text-[10px] font-black uppercase bg-emerald-100 hover:bg-emerald-200 text-emerald-900 border border-black rounded shadow-[1px_1px_0px_0px_#000] cursor-pointer">
                            ⚡ Pas
                        </button>
                        <button type="button" onclick="setCashPreset(50000)" class="px-2 py-0.5 text-[10px] font-bold bg-white hover:bg-slate-100 text-slate-800 border border-black rounded shadow-[1px_1px_0px_0px_#000] cursor-pointer">
                            50rb
                        </button>
                        <button type="button" onclick="setCashPreset(100000)" class="px-2 py-0.5 text-[10px] font-bold bg-white hover:bg-slate-100 text-slate-800 border border-black rounded shadow-[1px_1px_0px_0px_#000] cursor-pointer">
                            100rb
                        </button>
                        <button type="button" onclick="setCashPreset(200000)" class="px-2 py-0.5 text-[10px] font-bold bg-white hover:bg-slate-100 text-slate-800 border border-black rounded shadow-[1px_1px_0px_0px_#000] cursor-pointer">
                            200rb
                        </button>
                    </div>

                    <!-- Change Calculation Box -->
                    <div id="change-box" class="p-2.5 bg-slate-50 border-2 border-black rounded-lg flex justify-between items-center text-xs font-bold">
                        <span class="text-slate-600">Kembalian:</span>
                        <span id="change-amount-text" class="text-sm font-black text-emerald-800">Rp 0</span>
                    </div>

                    <!-- Hidden inputs for items injected via JS -->
                    <div id="hidden-cart-inputs"></div>

                    <button type="submit" 
                            id="btn-checkout" 
                            disabled 
                            class="w-full neo-btn bg-emerald-400 hover:bg-emerald-300 disabled:opacity-50 disabled:cursor-not-allowed text-black font-black text-sm py-3 shadow-[4px_4px_0px_0px_#000] flex items-center justify-center gap-2 cursor-pointer">
                        <x-icon name="receipt" class="w-4 h-4" />
                        <span>Selesaikan Transaksi (Cetak Struk)</span>
                    </button>
                </form>

            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
let cart = [];

function addItemToCart(name, qty, price) {
    const existing = cart.find(item => item.name === name);
    if (existing) {
        existing.qty += qty;
    } else {
        cart.push({ name, qty, price });
    }
    renderCart();
}

function addCustomItem() {
    const nameInput = document.getElementById('custom-name');
    const qtyInput = document.getElementById('custom-qty');
    const priceInput = document.getElementById('custom-price');

    const name = nameInput.value.trim();
    const qty = parseInt(qtyInput.value) || 1;
    const rawPrice = priceInput.value.replace(/\D/g, '');
    const price = parseFloat(rawPrice) || 0;

    if (!name) {
        alert('Harap isi nama barang / jasa!');
        nameInput.focus();
        return;
    }
    if (price <= 0) {
        alert('Harap isi harga satuan yang valid!');
        priceInput.focus();
        return;
    }

    addItemToCart(name, qty, price);

    nameInput.value = '';
    qtyInput.value = '1';
    priceInput.value = '';
    nameInput.focus();
}

function updateQty(index, delta) {
    cart[index].qty += delta;
    if (cart[index].qty <= 0) {
        cart.splice(index, 1);
    }
    renderCart();
}

function removeItem(index) {
    cart.splice(index, 1);
    renderCart();
}

function clearCart() {
    cart = [];
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cart-items-container');
    const hiddenInputs = document.getElementById('hidden-cart-inputs');
    const emptyMsg = document.getElementById('empty-cart-msg');
    const totalText = document.getElementById('cart-total-text');
    const btnCheckout = document.getElementById('btn-checkout');

    if (cart.length === 0) {
        container.innerHTML = `
            <div id="empty-cart-msg" class="py-8 text-center text-xs font-semibold text-slate-400 border-2 border-dashed border-slate-300 rounded-xl">
                Keranjang masih kosong.<br>Pilih layanan cepat atau input barang manual.
            </div>
        `;
        hiddenInputs.innerHTML = '';
        totalText.innerText = 'Rp 0';
        btnCheckout.disabled = true;
        calculateChange();
        return;
    }

    let total = 0;
    let html = '';
    let inputsHtml = '';

    cart.forEach((item, idx) => {
        const subtotal = item.qty * item.price;
        total += subtotal;

        html += `
            <div class="p-2.5 bg-slate-50 border-2 border-black rounded-xl flex items-center justify-between text-xs gap-2">
                <div class="min-w-0 flex-1">
                    <div class="font-bold text-black truncate">${item.name}</div>
                    <div class="text-[11px] text-slate-500 font-medium">Rp ${item.price.toLocaleString('id-ID')} / unit</div>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <button type="button" onclick="updateQty(${idx}, -1)" class="w-6 h-6 rounded border border-black bg-white hover:bg-slate-200 font-black text-xs flex items-center justify-center cursor-pointer">-</button>
                    <span class="font-bold w-6 text-center">${item.qty}</span>
                    <button type="button" onclick="updateQty(${idx}, 1)" class="w-6 h-6 rounded border border-black bg-white hover:bg-slate-200 font-black text-xs flex items-center justify-center cursor-pointer">+</button>
                </div>
                <div class="text-right font-black w-20 shrink-0">
                    Rp ${subtotal.toLocaleString('id-ID')}
                </div>
                <button type="button" onclick="removeItem(${idx})" class="text-rose-600 hover:text-rose-800 font-black px-1 cursor-pointer">✕</button>
            </div>
        `;

        inputsHtml += `
            <input type="hidden" name="items[${idx}][name]" value="${item.name}">
            <input type="hidden" name="items[${idx}][qty]" value="${item.qty}">
            <input type="hidden" name="items[${idx}][price]" value="${item.price}">
        `;
    });

    container.innerHTML = html;
    hiddenInputs.innerHTML = inputsHtml;
    totalText.innerText = 'Rp ' + total.toLocaleString('id-ID');
    btnCheckout.disabled = false;
    calculateChange();
}

function formatRupiahInput(input) {
    const raw = input.value.replace(/\D/g, '');
    if (!raw) {
        input.value = '';
    } else {
        const num = parseInt(raw, 10);
        input.value = num.toLocaleString('id-ID');
    }
}

function formatCashReceived(input) {
    formatRupiahInput(input);
    calculateChange();
}

function setCashPreset(preset) {
    let total = 0;
    cart.forEach(i => total += (i.qty * i.price));
    const input = document.getElementById('cash-received');
    if (!input) return;

    let val = 0;
    if (preset === 'exact') {
        val = total;
    } else if (typeof preset === 'number') {
        val = preset;
    }

    input.value = val > 0 ? val.toLocaleString('id-ID') : '0';
    calculateChange();
}

function calculateChange() {
    let total = 0;
    cart.forEach(i => total += (i.qty * i.price));

    const receivedInput = document.getElementById('cash-received');
    const raw = receivedInput ? receivedInput.value.replace(/\D/g, '') : '';
    const received = raw ? parseInt(raw, 10) : 0;
    const change = Math.max(0, received - total);

    document.getElementById('change-amount-text').innerText = 'Rp ' + change.toLocaleString('id-ID');
}

function attachRupiahBackspace(el) {
    if (!el) return;
    el.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace') {
            const cursor = el.selectionStart;
            if (cursor > 1 && el.value[cursor - 1] === '.') {
                e.preventDefault();
                const val = el.value;
                const newVal = val.slice(0, cursor - 2) + val.slice(cursor - 1);
                el.value = newVal;
                el.setSelectionRange(cursor - 2, cursor - 2);
                el.dispatchEvent(new Event('input'));
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const customPrice = document.getElementById('custom-price');
    const cashReceived = document.getElementById('cash-received');
    const customName = document.getElementById('custom-name');

    attachRupiahBackspace(customPrice);
    attachRupiahBackspace(cashReceived);

    customName?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            customPrice?.focus();
        }
    });

    customPrice?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            addCustomItem();
        }
    });
});
</script>
@endpush
@endsection
