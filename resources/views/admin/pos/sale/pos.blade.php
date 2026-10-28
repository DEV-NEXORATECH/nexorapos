@extends('admin.layout.app')

@section('title', 'Kasir POS — Point of Sale')

@section('content')
<div class="pos-fullscreen-container">
    {{-- Top Navigation Bar --}}
    <header class="pos-top-bar">
        <div class="pos-top-brand">
            {{-- Tombol Navigasi Kembali ke Dashboard Admin yang JELAS & MENONJOL --}}
            <a href="{{ url('admin-panel/dashboard') }}" class="pos-back-dashboard-btn" title="Kembali ke Dashboard Admin">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali ke Dashboard</span>
            </a>

            <div class="pos-brand-divider"></div>

            <div class="pos-brand-logo">
                <i class="fas fa-cash-register"></i>
            </div>
            <div class="pos-brand-info">
                <h4>Nexora POS</h4>
                <span>{{ $stores->firstWhere('id', $assignedStoreId)?->name ?? ($stores->first()?->name ?? 'Point of Sale') }}</span>
            </div>
        </div>

        {{-- Center Search Bar with Barcode Scanner Icon --}}
        <div class="pos-top-center">
            <div class="pos-search-wrapper">
                <i class="fas fa-search pos-search-icon"></i>
                <input type="text" id="productSearch" class="pos-search-input" placeholder="Cari nama produk, SKU, barcode (Tekan F2)..." autocomplete="off" autofocus>
                <i class="fas fa-barcode pos-barcode-icon" title="Scan Barcode Reader Ready"></i>
            </div>
        </div>

        {{-- Right Controls & Quick Actions --}}
        <div class="pos-top-actions">
            <button type="button" class="pos-top-btn" id="posCustomAmountBtn" title="Input Nominal Bebas">
                <i class="fas fa-calculator"></i>
                <span>Custom</span>
            </button>

            <button type="button" class="pos-top-btn" id="posLibraryBtn" title="Daftar Transaksi Ditahan (Held Bills)">
                <i class="fas fa-history"></i>
                <span>Held Bills</span>
                @if($heldBills->isNotEmpty())
                    <span class="badge badge-warning ml-1" style="font-size: 10px; border-radius: 10px;">{{ $heldBills->count() }}</span>
                @endif
            </button>

            <a href="{{ url('cfd/'.auth()->id()) }}" target="_blank" class="pos-top-btn" title="Buka Layar Pelanggan (Customer Facing Display)">
                <i class="fas fa-desktop"></i>
                <span>CFD</span>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="d-inline m-0">
                @csrf
                <button type="submit" class="pos-top-btn is-danger" title="Keluar dari Sistem">
                    <i class="fas fa-power-off"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </header>

    {{-- Main Workspace Body --}}
    <div class="pos-workspace-body">
        {{-- Left Section: Product Catalog & Category Filter --}}
        <div class="pos-left-panel">
            {{-- Category Filter Bar (Chips) --}}
            <div class="pos-category-bar">
                <div class="pos-cat-chip active pos-cat-btn" data-id="">
                    <i class="fas fa-border-all"></i>
                    <span>Semua Produk</span>
                </div>
                @foreach($categories as $cat)
                    @php
                        $catName = strtolower($cat->name);
                        $catIcon = match(true) {
                            str_contains($catName, 'makan') => 'fas fa-utensils',
                            str_contains($catName, 'minum') => 'fas fa-coffee',
                            str_contains($catName, 'snack') || str_contains($catName, 'camil') => 'fas fa-cookie',
                            str_contains($catName, 'sembako') => 'fas fa-basket-shopping',
                            str_contains($catName, 'layan') || str_contains($catName, 'jasa') => 'fas fa-concierge-bell',
                            default => 'fas fa-tag'
                        };
                    @endphp
                    <div class="pos-cat-chip pos-cat-btn" data-id="{{ $cat->id }}">
                        <i class="{{ $catIcon }}"></i>
                        <span>{{ $cat->name }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Product Grid --}}
            <div class="pos-product-grid" id="productGrid">
                @forelse($products as $p)
                    @php
                        $price = (float)($p->price ?? 0);
                        $categoryName = $p->category?->name ?? 'Umum';
                        $firstLetter = strtoupper(substr($p->name, 0, 1));
                    @endphp
                    <div class="pos-product-card clickable-card product-card"
                         data-id="{{ $p->id }}"
                         data-price="{{ $price }}"
                         data-category="{{ $p->category_id }}"
                         data-name="{{ strtolower($p->name) }}"
                         title="Klik untuk tambah ke struk pesanan">
                        <div class="pos-product-card-top">
                            <div class="pos-product-avatar">
                                @if($p->image && file_exists(public_path('storage/' . $p->image)))
                                    <img src="{{ asset('storage/' . $p->image) }}" alt="{{ $p->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                                @else
                                    {{ $firstLetter }}
                                @endif
                            </div>
                            <div class="pos-product-meta">
                                <div class="pos-product-category">{{ $categoryName }}</div>
                                <div class="pos-product-title">{{ $p->name }}</div>
                            </div>
                        </div>
                        <div class="pos-product-card-bottom">
                            <div class="pos-product-price">Rp {{ number_format($price, 0, ',', '.') }}</div>
                            <div class="pos-product-add-badge">
                                <i class="fas fa-plus"></i>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-5">
                        <i class="fas fa-box-open fa-3x mb-3 text-muted"></i>
                        <h5>Belum Ada Produk Tersedia</h5>
                        <p class="small">Tambahkan produk dari menu Kelola Produk terlebih dahulu.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Right Section: Cart / Billing Panel --}}
        <div class="pos-right-panel">
            {{-- Shift Status Banner --}}
            <div class="pos-cart-shift-banner">
                <div class="shift-left">
                    <i class="fas fa-user-clock"></i>
                    @if($openShift)
                        <span>{{ $openShift->shift_name ?: 'Shift Aktif' }} • Kasir: {{ auth()->user()?->name ?? 'Kasir' }}</span>
                    @else
                        <span class="text-danger">Shift Kasir Belum Dibuka</span>
                    @endif
                </div>
                <div class="shift-right">
                    @if($openShift)
                        <button type="button" class="btn btn-sm btn-outline-danger" id="closeShiftBtn" style="padding: 2px 8px; font-size: 11px;">Tutup</button>
                    @else
                        <button type="button" class="btn btn-sm btn-primary" id="openShiftBtn" style="padding: 2px 8px; font-size: 11px;">Buka Shift</button>
                    @endif
                    <a href="{{ url('admin-panel/pos/shifts/report') }}" class="btn btn-sm btn-light" style="padding: 2px 8px; font-size: 11px;" title="Lihat Laporan Shift">
                        <i class="fas fa-file-invoice"></i>
                    </a>
                </div>
            </div>

            {{-- Cart Header Controls --}}
            <div class="pos-cart-header">
                {{-- Order Type Switcher (Dine In vs Take Away) --}}
                <div class="pos-order-type-switch">
                    <button type="button" class="pos-order-type-btn active" data-type="dine_in">
                        <i class="fas fa-utensils"></i>
                        <span>Dine In</span>
                    </button>
                    <button type="button" class="pos-order-type-btn" data-type="take_away">
                        <i class="fas fa-bag-shopping"></i>
                        <span>Take Away</span>
                    </button>
                </div>
                {{-- Hidden select for backward compatibility --}}
                <select id="orderType" style="display: none;">
                    <option value="dine_in" selected>Dine In</option>
                    <option value="take_away">Take Away</option>
                </select>

                {{-- Customer Selector --}}
                <div class="pos-customer-row">
                    <select id="customerIdVisible" class="pos-customer-select">
                        <option value="">Umum / Walk-in Customer</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone ?? '-' }})</option>
                        @endforeach
                    </select>
                    <button type="button" class="pos-customer-add-btn" id="openAddCustomerBtn" title="Tambah Pelanggan Baru">
                        <i class="fas fa-user-plus"></i>
                        <span>Customer</span>
                    </button>
                </div>
            </div>

            {{-- Cart Items List (Scrollable Area) --}}
            <div class="pos-cart-items-wrapper" id="cartContainer">
                {{-- Dynamic Cart Items injected by JS --}}
                <div id="cartItems" class="cart-items-list">
                    {{-- Empty Cart State (Shown when 0 items) --}}
                    <div class="pos-empty-cart" id="emptyCartNotice">
                        <div class="pos-empty-cart-icon">
                            <i class="fas fa-shopping-basket"></i>
                        </div>
                        <h6>Keranjang Belanja Kosong</h6>
                        <p>Pilih produk dari katalog di sebelah kiri untuk memulai transaksi struk baru.</p>
                    </div>
                </div>
            </div>

            {{-- Cart Footer: Totals & Checkout Actions --}}
            <div class="pos-cart-footer">
                <div class="pos-totals-list" id="cartTotals">
                    <div class="pos-total-row">
                        <span>Sub-Total</span>
                        <span id="subtotal">Rp 0</span>
                    </div>
                    <div class="pos-total-row">
                        <span>Diskon</span>
                        <span id="discount" class="text-danger">Rp 0</span>
                    </div>
                    <div class="pos-total-row">
                        <span>Pajak (PPN)</span>
                        <span id="tax">Rp 0</span>
                    </div>
                    <div class="pos-grand-total-row">
                        <span class="pos-grand-total-label">Total Belanja</span>
                        <span class="pos-grand-total-amount" id="total">Rp 0</span>
                    </div>
                </div>

                {{-- Hidden Form Fields Required by Existing JS API --}}
                <div style="display: none;">
                    <select id="customerId"><option value=""></option></select>
                    <select id="storeId">
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}" @selected(($assignedStoreId ?? null) == $store->id)>{{ $store->name }}</option>
                        @endforeach
                    </select>
                    <select id="methodId">
                        @foreach($methods as $method)
                            <option value="{{ $method->id }}">{{ $method->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" id="paidAmount" value="0">
                    <input type="text" id="changeAmount" value="0">
                </div>

                {{-- Auxiliary Actions Grid --}}
                <div class="pos-action-btn-grid">
                    <button type="button" id="saveBillBtn" class="pos-aux-btn" title="Simpan / Tahan Pesanan (Hold Bill)">
                        <i class="fas fa-pause text-warning"></i>
                        <span>Tahan</span>
                    </button>
                    <button type="button" id="printBillBtn" class="pos-aux-btn" title="Cetak Bill Sementara">
                        <i class="fas fa-print text-primary"></i>
                        <span>Bill</span>
                    </button>
                    <button type="button" id="openCheckoutSplitBtn" class="pos-aux-btn" title="Split Pembayaran">
                        <i class="fas fa-layer-group text-info"></i>
                        <span>Split</span>
                    </button>
                </div>

                {{-- Giant Primary Charge Button --}}
                <button type="button" id="openCheckoutBtn" class="pos-main-charge-btn">
                    <span><i class="fas fa-credit-card mr-2"></i> Bayar (F9)</span>
                    <span id="chargeTotalText">Rp 0</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal 1: Add Customer Modal --}}
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-user-plus mr-2 text-primary"></i> Tambah Pelanggan Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addCustomerForm">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small">Nama Pelanggan <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control nexora-input" placeholder="Nama Lengkap" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small">No. Handphone / WhatsApp</label>
                        <input type="text" name="phone" class="form-control nexora-input" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small">Email</label>
                        <input type="email" name="email" class="form-control nexora-input" placeholder="email@contoh.com">
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold small">Alamat</label>
                        <textarea name="address" class="form-control nexora-input" rows="2" placeholder="Alamat lengkap..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold">Simpan Pelanggan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal 2: Checkout / Payment Modal --}}
<div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-cash-register mr-2 text-primary"></i> Pembayaran Transaksi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="row mb-4">
                    <div class="col-md-6 text-center border-right">
                        <small class="text-muted text-uppercase font-weight-bold">Total Tagihan</small>
                        <h2 id="checkoutTotalDue" class="font-weight-bold text-primary mt-1 mb-0">Rp 0</h2>
                    </div>
                    <div class="col-md-6 text-center">
                        <small class="text-muted text-uppercase font-weight-bold">Uang Kembalian</small>
                        <h2 id="checkoutChangeDue" class="font-weight-bold text-success mt-1 mb-0">Rp 0</h2>
                    </div>
                </div>

                {{-- Quick Cash Suggestion Buttons --}}
                <div class="mb-3">
                    <small class="text-muted font-weight-bold d-block mb-2">Pilihan Nominal Cepat:</small>
                    <div class="d-flex flex-wrap" style="gap: 8px;">
                        <button type="button" class="btn btn-sm btn-outline-primary quick-cash-btn" data-val="exact">Uang Pas</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary quick-cash-btn" data-val="10000">10.000</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary quick-cash-btn" data-val="20000">20.000</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary quick-cash-btn" data-val="50000">50.000</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary quick-cash-btn" data-val="100000">100.000</button>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="font-weight-bold mb-0">Rincian Pembayaran</h6>
                    <button class="btn btn-sm btn-outline-info" id="addSplitPaymentBtn">
                        <i class="fas fa-plus mr-1"></i> Tambah Split Bayar
                    </button>
                </div>
                <hr class="mt-1 mb-3">

                <form id="checkoutForm">
                    <div id="paymentRowsContainer">
                        <div class="payment-row row mb-3 align-items-center">
                            <div class="col-md-6">
                                <label class="small font-weight-bold">Metode Pembayaran</label>
                                <select name="payments[0][method_id]" class="form-control payment-method-select nexora-input">
                                    @foreach($methods as $method)
                                        <option value="{{ $method->id }}">{{ $method->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="small font-weight-bold">Nominal Diterima (Rp)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text font-weight-bold">Rp</span></div>
                                    <input type="number" name="payments[0][amount]" class="form-control payment-amount-input nexora-input" step="1" required placeholder="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer p-3 bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary btn-lg px-4 font-weight-bold" id="confirmCheckoutBtn">
                    <i class="fas fa-check-circle mr-1"></i> Konfirmasi & Selesai
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal 3: Open Shift Modal --}}
<div class="modal fade" id="openShiftModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-door-open mr-2 text-primary"></i> Buka Shift Kasir</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="openShiftForm">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small">Pilih Toko / Cabang</label>
                        <select name="store_id" class="form-control nexora-input" required>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}" @selected(($assignedStoreId ?? null) == $store->id)>{{ $store->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small">Nama Sesi Shift</label>
                        <input type="text" name="shift_name" class="form-control nexora-input" value="{{ auth()->user()?->shift_name ?: 'Shift Pagi' }}" placeholder="Contoh: Shift Pagi / Siang" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold small">Kas Awal / Modal Kasir (Rp)</label>
                        <input type="number" name="opening_cash" class="form-control nexora-input" min="0" step="1000" value="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold">Buka Shift</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal 4: Close Shift Modal --}}
<div class="modal fade" id="closeShiftModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-door-closed mr-2 text-primary"></i> Tutup Shift Kasir</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="closeShiftForm">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small">Total Kas Fisik di Laci (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="closing_cash" class="form-control nexora-input" min="0" step="1000" placeholder="Masukkan total uang fisik di kasir" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold small">Catatan Selisih / Keterangan</label>
                        <textarea name="note" class="form-control nexora-input" rows="3" placeholder="Catatan selisih kas bila ada..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger font-weight-bold">Tutup Shift</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal 5: Held Bills Modal --}}
<div class="modal fade" id="heldBillsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-history mr-2 text-primary"></i> Transaksi Ditahan (Held Bills)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                @forelse($heldBills as $bill)
                    <div class="d-flex justify-content-between align-items-center p-3 mb-2 bg-light rounded border">
                        <div>
                            <strong class="text-dark">{{ $bill->bill_name }}</strong>
                            <div class="small text-muted">
                                {{ $bill->store?->name ?? 'Store' }} • {{ $bill->created_at->format('d M Y H:i') }} • 
                                <span class="text-primary font-weight-bold">Rp {{ number_format((float) data_get($bill->totals_snapshot, 'total', 0), 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary restore-held-bill" data-id="{{ $bill->id }}">
                            <i class="fas fa-undo mr-1"></i> Pulihkan
                        </button>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-3x mb-2 text-muted"></i>
                        <p class="mb-0">Tidak ada transaksi yang sedang ditahan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Pass URLs & Config to JS --}}
<script>
    const urls = {
        add: "{{ url('admin-panel/pos/cart/add') }}",
        update: "{{ url('admin-panel/pos/cart/update') }}",
        remove: "{{ url('admin-panel/pos/cart/remove') }}",
        checkout: "{{ url('admin-panel/pos/checkout') }}",
        posQris: "{{ url('admin-panel/pos/payment/qris') }}",
        apiCustomer: "{{ url('admin-panel/pos/customers/api') }}",
        createStore: "{{ url('admin-panel/stores/add') }}",
        customAmount: "{{ url('admin-panel/pos/cart/custom') }}",
        holdBill: "{{ url('admin-panel/pos/hold-bills') }}",
        restoreHeldBill: "{{ url('admin-panel/pos/hold-bills') }}",
        openShift: "{{ url('admin-panel/pos/shifts/open') }}",
        closeShift: "{{ url('admin-panel/pos/shifts/close') }}"
    };
    const posSetup = {
        hasStores: @json($stores->isNotEmpty()),
        hasMethods: @json($methods->isNotEmpty()),
        hasOpenShift: @json((bool) $openShift)
    };
</script>
@endsection

@push('script')
    <script src="{{ asset('admin/pos/custom.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('pos-fullscreen-mode');

            // Move modals directly to body for clean Bootstrap 4 layering
            $('#addCustomerModal, #checkoutModal, #openShiftModal, #closeShiftModal, #heldBillsModal').appendTo('body');

            // Category Filtering
            const catChips = document.querySelectorAll('.pos-cat-chip');
            const productCards = document.querySelectorAll('.pos-product-card');
            const cartItemsContainer = document.getElementById('cartItems');
            const subtotalEl = document.getElementById('subtotal');
            const discountEl = document.getElementById('discount');
            const taxEl = document.getElementById('tax');
            const totalEl = document.getElementById('total');
            const emptyCartNotice = document.getElementById('emptyCartNotice');

            // Category click handling
            catChips.forEach(chip => {
                chip.addEventListener('click', () => {
                    catChips.forEach(c => c.classList.remove('active'));
                    chip.classList.add('active');

                    const catId = chip.getAttribute('data-id');
                    filterProducts();
                });
            });

            // Product search filter
            const searchInput = document.getElementById('productSearch');
            if (searchInput) {
                searchInput.addEventListener('input', filterProducts);
            }

            // Keyboard shortcut F2 (Search) & F9 (Checkout)
            window.addEventListener('keydown', function(e) {
                if (e.key === 'F2') {
                    e.preventDefault();
                    searchInput?.focus();
                    searchInput?.select();
                } else if (e.key === 'F9') {
                    e.preventDefault();
                    document.getElementById('openCheckoutBtn')?.click();
                }
            });

            function filterProducts() {
                const activeChip = document.querySelector('.pos-cat-chip.active');
                const catId = activeChip ? activeChip.getAttribute('data-id') : '';
                const query = (searchInput?.value || '').trim().toLowerCase();

                productCards.forEach(card => {
                    const cardCat = card.getAttribute('data-category');
                    const cardName = (card.getAttribute('data-name') || '').toLowerCase();

                    const matchCategory = !catId || cardCat === catId;
                    const matchQuery = !query || cardName.includes(query);

                    if (matchCategory && matchQuery) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            // Order Type switcher
            $('.pos-order-type-btn').on('click', function() {
                $('.pos-order-type-btn').removeClass('active');
                $(this).addClass('active');
                const type = $(this).data('type');
                $('#orderType').val(type);
            });

            // Sync visible customer select to hidden #customerId
            $('#customerIdVisible').on('change', function() {
                $('#customerId').val($(this).val());
            });

            $('#openAddCustomerBtn').on('click', function() {
                $('#addCustomerModal').modal('show');
            });

            // Cart Rendering Helper
            window.renderPosCart = function(cart) {
                if (!cartItemsContainer) return;
                const keys = Object.keys(cart || {});

                if (keys.length === 0) {
                    cartItemsContainer.innerHTML = `
                        <div class="pos-empty-cart" id="emptyCartNotice">
                            <div class="pos-empty-cart-icon">
                                <i class="fas fa-shopping-basket"></i>
                            </div>
                            <h6>Keranjang Belanja Kosong</h6>
                            <p>Pilih produk dari katalog di sebelah kiri untuk memulai transaksi struk baru.</p>
                        </div>
                    `;
                    return;
                }

                cartItemsContainer.innerHTML = '';
                keys.forEach(key => {
                    const item = cart[key];
                    const itemPrice = Number(item.price || 0);
                    const itemQty = Number(item.quantity || 1);
                    const itemSubtotal = itemPrice * itemQty;

                    const row = document.createElement('div');
                    row.className = 'cart-item-row';
                    row.innerHTML = `
                        <div class="cart-item-row-top">
                            <div>
                                <div class="cart-item-name">${item.product_name}</div>
                                <div class="cart-item-price-unit">Rp ${itemPrice.toLocaleString('id-ID')} / item</div>
                            </div>
                            <button type="button" class="cart-item-remove-btn remove-item" data-key="${key}" title="Hapus item">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                        <div class="cart-item-row-bottom">
                            <div class="cart-qty-stepper">
                                <button type="button" class="cart-qty-btn cart-qty-minus" data-key="${key}">-</button>
                                <input type="number" min="1" value="${itemQty}" data-key="${key}" class="cart-qty-input qty-change">
                                <button type="button" class="cart-qty-btn cart-qty-plus" data-key="${key}">+</button>
                            </div>
                            <div class="cart-item-subtotal">Rp ${itemSubtotal.toLocaleString('id-ID')}</div>
                        </div>
                    `;
                    cartItemsContainer.appendChild(row);
                });
            };

            // Quantity stepper handlers
            $(document).on('click', '.cart-qty-plus', function() {
                const key = $(this).data('key');
                const input = $(this).siblings('.cart-qty-input');
                let val = parseInt(input.val()) || 1;
                input.val(val + 1).trigger('change');
            });

            $(document).on('click', '.cart-qty-minus', function() {
                const key = $(this).data('key');
                const input = $(this).siblings('.cart-qty-input');
                let val = parseInt(input.val()) || 1;
                if (val > 1) {
                    input.val(val - 1).trigger('change');
                } else {
                    $(this).closest('.cart-item-row').find('.remove-item').trigger('click');
                }
            });

            // Update Totals Helper
            window.updatePosTotals = function(totals) {
                if (!totals) return;
                const sub = Number(totals.subtotal || 0);
                const disc = Number(totals.discount || 0);
                const tx = Number(totals.tax || 0);
                const grand = Number(totals.total || 0);

                if (subtotalEl) subtotalEl.textContent = 'Rp ' + sub.toLocaleString('id-ID');
                if (discountEl) discountEl.textContent = 'Rp ' + disc.toLocaleString('id-ID');
                if (taxEl) taxEl.textContent = 'Rp ' + tx.toLocaleString('id-ID');
                if (totalEl) totalEl.textContent = 'Rp ' + grand.toLocaleString('id-ID');

                const chargeText = document.getElementById('chargeTotalText');
                if (chargeText) chargeText.textContent = 'Rp ' + grand.toLocaleString('id-ID');
                rawTotalDue = grand;
            };

            // Custom Amount Action
            $('#posCustomAmountBtn').on('click', async function() {
                const result = await Swal.fire({
                    title: 'Input Nominal Bebas',
                    input: 'number',
                    inputLabel: 'Masukkan nominal rupiah',
                    inputPlaceholder: 'Contoh: 25000',
                    inputAttributes: { min: 1 },
                    showCancelButton: true,
                    confirmButtonText: 'Tambahkan ke Struk',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#0058A6',
                    inputValidator: (value) => {
                        if (!value || Number(value) <= 0) return 'Nominal harus lebih dari Rp 0';
                    }
                });

                if (result.isConfirmed) {
                    $.ajax({
                        url: urls.customAmount,
                        type: 'POST',
                        data: {
                            amount: result.value,
                            name: 'Nominal Bebas (Custom)',
                            _token: $('meta[name="csrf-token"]').attr('content'),
                        },
                        success: function(res) {
                            if (res.success) {
                                renderPosCart(res.cart);
                                updatePosTotals(res.totals);
                                Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Nominal berhasil dimasukkan ke struk.', timer: 1400, showConfirmButton: false });
                            }
                        },
                        error: function(err) {
                            Swal.fire('Error', err.responseJSON?.message || 'Gagal menambahkan nominal custom.', 'error');
                        }
                    });
                }
            });

            // Held Bills Modal Open
            $('#posLibraryBtn').on('click', function() {
                $('#heldBillsModal').modal('show');
            });

            // Save / Hold Bill
            $('#saveBillBtn').on('click', async function() {
                const result = await Swal.fire({
                    title: 'Tahan Transaksi (Hold Bill)',
                    input: 'text',
                    inputLabel: 'Nama / Catatan Meja Pesanan',
                    inputPlaceholder: 'Contoh: Meja 05 - Bapak Rian',
                    showCancelButton: true,
                    confirmButtonText: 'Simpan & Tahan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#0058A6',
                    inputValidator: (value) => {
                        if (!value) return 'Nama pesanan harus diisi!';
                    }
                });

                if (result.isConfirmed) {
                    $.ajax({
                        url: urls.holdBill,
                        type: 'POST',
                        data: {
                            bill_name: result.value,
                            customer_id: $('#customerId').val(),
                            store_id: $('#storeId').val(),
                            _token: $('meta[name="csrf-token"]').attr('content'),
                        },
                        success: function(res) {
                            if (res.success) {
                                renderPosCart([]);
                                updatePosTotals({ subtotal: 0, discount: 0, tax: 0, total: 0 });
                                Swal.fire({ icon: 'success', title: 'Tersimpan', text: 'Transaksi berhasil ditahan.', timer: 1500, showConfirmButton: false }).then(() => {
                                    window.location.reload();
                                });
                            }
                        },
                        error: function(err) {
                            Swal.fire('Error', err.responseJSON?.message || 'Gagal menahan transaksi. Pastikan keranjang tidak kosong.', 'error');
                        }
                    });
                }
            });

            // Restore Held Bill
            $(document).on('click', '.restore-held-bill', function() {
                const billId = $(this).data('id');
                $.ajax({
                    url: `${urls.restoreHeldBill}/${billId}/restore`,
                    type: 'POST',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function(res) {
                        if (res.success) {
                            $('#heldBillsModal').modal('hide');
                            renderPosCart(res.cart);
                            updatePosTotals(res.totals);
                            Swal.fire({ icon: 'success', title: 'Dipulihkan', text: 'Pesanan ditahan berhasil dibuka kembali.', timer: 1400, showConfirmButton: false });
                        }
                    },
                    error: function(err) {
                        Swal.fire('Error', err.responseJSON?.message || 'Gagal memulihkan bill.', 'error');
                    }
                });
            });

            // Shift Modals
            $('#openShiftBtn').on('click', () => $('#openShiftModal').modal('show'));
            $('#closeShiftBtn').on('click', () => $('#closeShiftModal').modal('show'));

            $('#openShiftForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: urls.openShift,
                    type: 'POST',
                    data: $(this).serialize() + '&_token=' + $('meta[name="csrf-token"]').attr('content'),
                    success: function() {
                        $('#openShiftModal').modal('hide');
                        window.location.reload();
                    },
                    error: function(err) {
                        Swal.fire('Error', err.responseJSON?.message || 'Gagal membuka shift.', 'error');
                    }
                });
            });

            $('#closeShiftForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: urls.closeShift,
                    type: 'POST',
                    data: $(this).serialize() + '&_token=' + $('meta[name="csrf-token"]').attr('content'),
                    success: function() {
                        $('#closeShiftModal').modal('hide');
                        window.location.reload();
                    },
                    error: function(err) {
                        Swal.fire('Error', err.responseJSON?.message || 'Gagal menutup shift.', 'error');
                    }
                });
            });

            // Add Customer AJAX
            $('#addCustomerForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                $.ajax({
                    url: urls.apiCustomer,
                    type: 'POST',
                    data: form.serialize() + '&_token=' + $('meta[name="csrf-token"]').attr('content'),
                    success: function(res) {
                        if (res.success) {
                            const opt = new Option(res.customer.name, res.customer.id, true, true);
                            $('#customerId').append(opt).val(res.customer.id);
                            $('#customerIdVisible').append(new Option(res.customer.name, res.customer.id, true, true)).val(res.customer.id);
                            $('#addCustomerModal').modal('hide');
                            form[0].reset();
                            Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Pelanggan berhasil ditambahkan.', timer: 1400, showConfirmButton: false });
                        }
                    },
                    error: function(err) {
                        Swal.fire('Error', err.responseJSON?.message || 'Gagal menambahkan pelanggan.', 'error');
                    }
                });
            });

            // Checkout Logic
            let rawTotalDue = 0;

            function openCheckout() {
                const totalSpan = document.getElementById('total');
                const totalText = totalSpan ? totalSpan.textContent.replace(/[^\d]/g, '') : 0;
                rawTotalDue = parseInt(totalText) || 0;

                if (rawTotalDue <= 0) {
                    Swal.fire('Keranjang Kosong', 'Pilih setidaknya 1 produk sebelum melakukan pembayaran.', 'warning');
                    return;
                }

                $('#checkoutTotalDue').text('Rp ' + rawTotalDue.toLocaleString('id-ID'));
                $('.payment-amount-input').first().val(rawTotalDue);
                calculateChange();
                $('#checkoutModal').modal('show');
            }

            $('#openCheckoutBtn').on('click', openCheckout);
            $('#openCheckoutSplitBtn').on('click', function() {
                openCheckout();
                if ($('.payment-row').length === 1) {
                    $('#addSplitPaymentBtn').trigger('click');
                }
            });

            // Quick cash buttons
            $(document).on('click', '.quick-cash-btn', function() {
                const val = $(this).data('val');
                const input = $('.payment-amount-input').first();
                if (val === 'exact') {
                    input.val(rawTotalDue);
                } else {
                    input.val(parseInt(val));
                }
                calculateChange();
            });

            // Add Split Row
            $('#addSplitPaymentBtn').on('click', function() {
                const index = $('.payment-row').length;
                const currentInputs = $('.payment-amount-input');
                let currentTotal = 0;
                currentInputs.each(function() { currentTotal += parseInt($(this).val()) || 0; });

                if (currentInputs.length === 1 && currentTotal >= rawTotalDue) {
                    const half = Math.floor(rawTotalDue / 2);
                    currentInputs.eq(0).val(half);
                    currentTotal = half;
                }
                const remainder = Math.max(0, rawTotalDue - currentTotal);

                $('#paymentRowsContainer').append(`
                    <div class="payment-row row mb-3 align-items-center">
                        <div class="col-md-5">
                            <label class="small font-weight-bold">Metode</label>
                            <select name="payments[${index}][method_id]" class="form-control payment-method-select nexora-input">
                                @foreach($methods as $method)
                                    <option value="{{ $method->id }}">{{ $method->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="small font-weight-bold">Nominal (Rp)</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text font-weight-bold">Rp</span></div>
                                <input type="number" name="payments[${index}][amount]" class="form-control payment-amount-input nexora-input" step="1" required value="${remainder}">
                            </div>
                        </div>
                        <div class="col-md-2 text-center" style="padding-top: 24px;">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-payment-row"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                `);
                calculateChange();
            });

            $(document).on('click', '.remove-payment-row', function() {
                if ($('.payment-row').length > 1) {
                    $(this).closest('.payment-row').remove();
                    $('.payment-row').each(function(i) {
                        $(this).find('select').attr('name', `payments[${i}][method_id]`);
                        $(this).find('input').attr('name', `payments[${i}][amount]`);
                    });
                    calculateChange();
                }
            });

            $(document).on('input', '.payment-amount-input', calculateChange);

            function calculateChange() {
                let totalPaid = 0;
                $('.payment-amount-input').each(function() {
                    totalPaid += parseInt($(this).val()) || 0;
                });
                const change = totalPaid - rawTotalDue;
                if (change < 0) {
                    $('#checkoutChangeDue').text('Kurang Rp ' + Math.abs(change).toLocaleString('id-ID')).removeClass('text-success').addClass('text-danger');
                } else {
                    $('#checkoutChangeDue').text('Rp ' + change.toLocaleString('id-ID')).removeClass('text-danger').addClass('text-success');
                }
            }

            // Confirm Payment
            $('#confirmCheckoutBtn').on('click', function() {
                let totalPaid = 0;
                $('.payment-amount-input').each(function() {
                    totalPaid += parseInt($(this).val()) || 0;
                });

                if (totalPaid < rawTotalDue) {
                    Swal.fire('Nominal Kurang', 'Total nominal yang dibayarkan masih kurang dari total tagihan.', 'warning');
                    return;
                }

                const btn = $(this);
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...');

                let qrisAmount = 0;
                $('.payment-row').each(function() {
                    const methodName = $(this).find('.payment-method-select option:selected').text().trim().toLowerCase();
                    if (methodName === 'qris') {
                        qrisAmount += parseInt($(this).find('.payment-amount-input').val()) || 0;
                    }
                });

                let formData = $('#checkoutForm').serializeArray();
                formData.push({ name: 'customer_id', value: $('#customerId').val() });
                formData.push({ name: 'store_id', value: $('#storeId').val() || 1 });
                formData.push({ name: 'status', value: 'completed' });
                formData.push({ name: '_token', value: $('meta[name="csrf-token"]').attr('content') });

                $.ajax({
                    url: urls.checkout,
                    type: 'POST',
                    data: $.param(formData),
                    success: function(res) {
                        btn.prop('disabled', false).html('<i class="fas fa-check-circle mr-1"></i> Konfirmasi & Selesai');
                        if (res.success) {
                            $('#checkoutModal').modal('hide');
                            const openInvoice = () => {
                                if (res.redirect) window.open(res.redirect, '_blank');
                                window.location.reload();
                            };

                            if (qrisAmount > 0 && typeof window.snap !== 'undefined') {
                                $.ajax({
                                    url: urls.posQris,
                                    type: 'POST',
                                    data: {
                                        sale_id: res.sale_id,
                                        amount: qrisAmount,
                                        _token: $('meta[name="csrf-token"]').attr('content'),
                                    },
                                    success: function(payment) {
                                        window.snap.pay(payment.snap_token, {
                                            onSuccess: openInvoice,
                                            onPending: function() {
                                                Swal.fire('Menunggu Pembayaran', 'QRIS belum selesai dibayar.', 'info');
                                            },
                                            onError: function() { Swal.fire('QRIS Gagal', 'Pembayaran QRIS gagal diproses.', 'error'); },
                                            onClose: function() { Swal.fire('QRIS Dibatalkan', 'Transaksi menunggu pembayaran.', 'info'); }
                                        });
                                    },
                                    error: function(err) {
                                        Swal.fire('QRIS Gagal', err.responseJSON?.message || 'Periksa Midtrans.', 'error').then(openInvoice);
                                    }
                                });
                            } else {
                                Swal.fire({ icon: 'success', title: 'Pembayaran Berhasil!', text: 'Transaksi telah berhasil diproses.', timer: 1500, showConfirmButton: false }).then(openInvoice);
                            }
                        }
                    },
                    error: function(err) {
                        btn.prop('disabled', false).html('<i class="fas fa-check-circle mr-1"></i> Konfirmasi & Selesai');
                        Swal.fire('Error', err.responseJSON?.message || 'Terjadi kesalahan saat checkout.', 'error');
                    }
                });
            });
        });
    </script>
    @if(config('services.midtrans.client_key'))
        <script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    @endif
@endpush
