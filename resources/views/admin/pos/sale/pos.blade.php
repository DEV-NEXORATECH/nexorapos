@extends('admin.layout.app')

@section('title', 'POS Sell')

@section('content')
<div class="pos-fullscreen-container">
    <!-- Left Section: Products & Categories -->
    <div class="pos-left-panel">
        
        <!-- Category Bar -->
        <div class="pos-category-bar">
            <div class="pos-cat-btn active" data-id="">
                <div class="pos-cat-icon" style="background:#E0E0E0; color:#9E9E9E;">Al</div>
                <div class="pos-cat-label">All Items</div>
            </div>
            @foreach($categories as $cat)
                <div class="pos-cat-btn" data-id="{{ $cat->id }}">
                    <div class="pos-cat-icon" style="background:#9E9E9E; color:#FFF;">{{ strtoupper(substr($cat->name, 0, 2)) }}</div>
                    <div class="pos-cat-label">{{ $cat->name }}</div>
                </div>
            @endforeach
        </div>

        <!-- Product Grid -->
        <div class="pos-product-grid" id="productGrid">
            @foreach($products as $p)
                <div class="pos-product-card clickable-card"
                     data-id="{{ $p->id }}"
                     data-price="{{ $p->price ?? 0 }}"
                     data-category="{{ $p->category_id }}"
                     data-name="{{ strtolower($p->name) }}">
                    <div class="pos-product-image">
                        <!-- Placeholder colored block since no images exist -->
                        <div class="placeholder-img" style="background-color: hsl({{ rand(0, 360) }}, 70%, 85%);">
                            {{ substr($p->name, 0, 1) }}
                        </div>
                    </div>
                    <div class="pos-product-info">
                        <div class="pos-product-name">{{ $p->name }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Bottom Navigation -->
        <div class="pos-bottom-nav">
            <button type="button" class="nav-item active" id="posFavoritesBtn">
                <i class="fas fa-star"></i>
                <span>Favorites</span>
            </button>
            <button type="button" class="nav-item" id="posLibraryBtn">
                <i class="fas fa-list-ul"></i>
                <span>Library</span>
            </button>
            <button type="button" class="nav-item" id="posCustomAmountBtn">
                <i class="fas fa-calculator"></i>
                <span>Custom</span>
            </button>
        </div>
    </div>

    <!-- Right Section: Cart / Billing -->
    <div class="pos-right-panel">
        <div class="pos-cart-header">
            @if($stores->isEmpty())
                <div class="pos-setup-alert mb-3">
                    <strong>Store belum tersedia</strong>
                    <span>Buat store dulu supaya checkout POS bisa diproses.</span>
                    <a href="{{ url('admin-panel/stores/add') }}" class="btn btn-sm btn-primary mt-2">Create Store</a>
                </div>
            @endif
            <div class="pos-shift-card mb-3 {{ $openShift ? 'is-open' : 'is-closed' }}">
                <div>
                    <small>Cashier Shift</small>
                    @if($openShift)
                        <strong>{{ $openShift->shift_name ?: 'Open Shift' }}</strong>
                        <span>{{ $openShift->store?->name ?? 'Store' }} | Open {{ $openShift->opened_at?->format('H:i') }}</span>
                    @else
                        <strong>Shift belum dibuka</strong>
                        <span>Buka shift untuk catat kas awal dan laporan kasir.</span>
                    @endif
                </div>
                @if($openShift)
                    <div class="pos-shift-actions">
                        <a href="{{ url('admin-panel/pos/shifts/report') }}" class="btn btn-sm btn-outline-primary">Report</a>
                        <button type="button" class="btn btn-sm btn-secondary" id="closeShiftBtn">Close Shift</button>
                    </div>
                @else
                    <div class="pos-shift-actions">
                        <a href="{{ url('admin-panel/pos/shifts/report') }}" class="btn btn-sm btn-outline-primary">Report</a>
                        <button type="button" class="btn btn-sm btn-primary" id="openShiftBtn" {{ $stores->isEmpty() ? 'disabled' : '' }}>Open Shift</button>
                    </div>
                @endif
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="billing-icon">
                    <i class="fas fa-receipt"></i>
                    <small>Billing List</small>
                </div>
                <div>
                    <a href="{{ url('cfd/'.auth()->id()) }}" target="_blank" class="btn btn-sm btn-outline-primary" style="border-radius:8px; font-weight:600; margin-right: 5px;" title="Open Customer Facing Display">
                        <i class="fas fa-desktop"></i> CFD
                    </a>
                    <button type="button" class="btn pos-add-customer-btn">
                        + Add Customer
                    </button>
                </div>
            </div>
            <div class="pos-order-type">
                <select id="orderType" class="form-control pos-minimal-select">
                    <option value="dine_in">Dine In</option>
                    <option value="take_away">Take Away</option>
                </select>
            </div>
        </div>

        <div class="pos-cart-items" id="cartContainer">
            <div id="cartItems" class="cart-items-list">
                <!-- Cart items will be rendered here by JS -->
            </div>
        </div>

        <div class="pos-cart-footer">
            <div class="pos-totals" id="cartTotals">
                <div class="tot-row">
                    <span>Sub-Total</span>
                    <span id="subtotal">Rp 0</span>
                </div>
                <div class="tot-row text-muted small">
                    <span>Discounts</span>
                    <span id="discount">Rp 0</span>
                </div>
                <div class="tot-row text-muted small">
                    <span>Tax</span>
                    <span id="tax">Rp 0</span>
                </div>
                <div class="tot-row grand-total mt-2">
                    <span>Total</span>
                    <span id="total">Rp 0</span>
                </div>
            </div>

            <!-- Hidden inputs required by existing JS logic -->
            <div style="display: none;">
                <select id="customerId"><option value=""></option></select>
                <select id="storeId">
                    @foreach($stores as $store) <option value="{{ $store->id }}" @selected(($assignedStoreId ?? null) == $store->id)>{{ $store->name }}</option> @endforeach
                </select>
                <select id="methodId">
                    @foreach($methods as $method) <option value="{{ $method->id }}">{{ $method->name }}</option> @endforeach
                </select>
                <input type="number" id="paidAmount" value="0">
                <input type="text" id="changeAmount" value="0">
            </div>

            <div class="d-flex mt-3 mb-2" style="gap: 10px;">
                <button id="saveBillBtn" class="btn pos-action-btn flex-fill">Save Bill</button>
                <button id="printBillBtn" class="btn pos-action-btn flex-fill">Print Bill</button>
            </div>
            
            <div class="d-flex">
                <button class="btn pos-split-btn" id="openCheckoutSplitBtn">
                    <i class="fas fa-layer-group"></i><br><small>Split Bill</small>
                </button>
                <button id="openCheckoutBtn" class="btn pos-charge-btn flex-fill">
                    Charge <span id="chargeTotalText">Rp 0</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addCustomerForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Checkout Modal (Includes Split Bill Support) -->
<div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Checkout / Payment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 text-center mb-3">
                        <h4 class="text-muted">Total Due</h4>
                        <h1 id="checkoutTotalDue" class="text-primary font-weight-bold">Rp 0</h1>
                    </div>
                    <div class="col-md-6 text-center mb-3">
                        <h4 class="text-muted">Change</h4>
                        <h1 id="checkoutChangeDue" class="text-success font-weight-bold">Rp 0</h1>
                    </div>
                </div>
                
                <h5 class="mt-3">Payments <button class="btn btn-sm btn-outline-info float-right" id="addSplitPaymentBtn">+ Split Payment</button></h5>
                <hr>
                <form id="checkoutForm">
                    <div id="paymentRowsContainer">
                        <div class="payment-row row mb-2">
                            <div class="col-md-6">
                                <label>Method</label>
                                <select name="payments[0][method_id]" class="form-control payment-method-select">
                                    @foreach($methods as $method) <option value="{{ $method->id }}">{{ $method->name }}</option> @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Amount</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                    <input type="number" name="payments[0][amount]" class="form-control payment-amount-input" step="1" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-lg w-100" id="confirmCheckoutBtn">Confirm Payment</button>
            </div>
        </div>
    </div>
</div>

<!-- Open Shift Modal -->
<div class="modal fade" id="openShiftModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Open Cashier Shift</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="openShiftForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Store</label>
                        <select name="store_id" class="form-control" required>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}" @selected(($assignedStoreId ?? null) == $store->id)>{{ $store->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Shift Name</label>
                        <input type="text" name="shift_name" class="form-control" value="{{ auth()->user()?->shift_name }}" placeholder="Contoh: Pagi">
                    </div>
                    <div class="form-group">
                        <label>Opening Cash</label>
                        <input type="number" name="opening_cash" class="form-control" min="0" step="1000" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Open Shift</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Close Shift Modal -->
<div class="modal fade" id="closeShiftModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Close Cashier Shift</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="closeShiftForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Closing Cash</label>
                        <input type="number" name="closing_cash" class="form-control" min="0" step="1000" required>
                    </div>
                    <div class="form-group">
                        <label>Note</label>
                        <textarea name="note" class="form-control" rows="3" placeholder="Catatan selisih kas, bila ada"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Close Shift</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Held Bills Modal -->
<div class="modal fade" id="heldBillsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Held Bills</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @forelse($heldBills as $bill)
                    <div class="pos-held-bill-row">
                        <div>
                            <strong>{{ $bill->bill_name }}</strong>
                            <span>{{ $bill->store?->name ?? '-' }} | {{ $bill->created_at->format('d M Y H:i') }} | Rp {{ number_format((float) data_get($bill->totals_snapshot, 'total', 0), 0, ',', '.') }}</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary restore-held-bill" data-id="{{ $bill->id }}">Restore</button>
                    </div>
                @empty
                    <div class="text-muted">Belum ada held bill.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Pass URLs to JS -->
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
        // Additional UI logic for the new design
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('pos-fullscreen-mode');

            $('#addCustomerModal, #checkoutModal, #openShiftModal, #closeShiftModal, #heldBillsModal').appendTo('body');

            // Category filtering
            const catBtns = document.querySelectorAll('.pos-cat-btn');
            const productCards = document.querySelectorAll('.pos-product-card');
            const cartItemsContainer = document.getElementById('cartItems');
            const subtotalEl = document.getElementById('subtotal');
            const discountEl = document.getElementById('discount');
            const taxEl = document.getElementById('tax');
            const totalEl = document.getElementById('total');
            const setActivePosNav = (activeButton) => {
                document.querySelectorAll('.pos-bottom-nav .nav-item').forEach(item => item.classList.remove('active'));
                activeButton?.classList.add('active');
            };

            const renderPosCart = (cart) => {
                if (!cartItemsContainer) return;
                cartItemsContainer.innerHTML = '';
                Object.keys(cart || {}).forEach((key) => {
                    const item = cart[key];
                    const div = document.createElement('div');
                    div.className = 'd-flex align-items-center justify-content-between mb-2';
                    div.innerHTML = `
                        <div>
                            <div><strong>${item.product_name}</strong> ${item.category_name ? '(' + item.category_name + ')' : ''}</div>
                            <small>Price: ${Number(item.price).toFixed(2)} | Tax: ${Number(item.tax_percent || 0).toFixed(2)}%</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <input type="number" min="1" value="${item.quantity}" data-key="${key}" class="form-control form-control-sm qty-change" style="width:80px;">
                            <button class="btn btn-danger btn-sm ml-2 remove-item" data-key="${key}">x</button>
                        </div>
                    `;
                    cartItemsContainer.appendChild(div);
                });
            };

            const updatePosTotals = (totals) => {
                if (!totals) return;
                subtotalEl.textContent = Number(totals.subtotal || 0).toFixed(2);
                discountEl.textContent = Number(totals.discount || 0).toFixed(2);
                taxEl.textContent = Number(totals.tax || 0).toFixed(2);
                totalEl.textContent = Number(totals.total || 0).toFixed(2);
            };

            const notifyInfo = (title, text) => {
                if (window.NexoraPopup) {
                    window.NexoraPopup.success({
                        title,
                        message: text,
                        detail: 'Aksi POS berhasil dijalankan.',
                        timer: 1800,
                    });
                    return;
                }

                Swal.fire(title, text, 'info');
            };
            
            catBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    catBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    
                    const catId = btn.getAttribute('data-id');
                    productCards.forEach(card => {
                        if(catId === "" || card.getAttribute('data-category') === catId) {
                            card.style.display = 'flex';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            });

            $('#posFavoritesBtn').on('click', function() {
                setActivePosNav(this);
                catBtns.forEach(b => b.classList.remove('active'));
                catBtns[0]?.classList.add('active');
                productCards.forEach(card => card.style.display = 'flex');
                notifyInfo('Favorites', 'Semua item favorit/default ditampilkan.');
            });

            $('#posLibraryBtn').on('click', function() {
                setActivePosNav(this);
                catBtns.forEach(b => b.classList.remove('active'));
                catBtns[0]?.classList.add('active');
                productCards.forEach(card => card.style.display = 'flex');
                document.getElementById('productGrid')?.scrollTo({ top: 0, behavior: 'smooth' });
                $('#heldBillsModal').modal('show');
            });

            $('#posCustomAmountBtn').on('click', async function() {
                setActivePosNav(this);
                const result = await Swal.fire({
                    title: 'Custom Amount',
                    input: 'number',
                    inputLabel: 'Masukkan nominal custom',
                    inputPlaceholder: 'Contoh: 25000',
                    inputAttributes: {
                        min: 1,
                        step: 1,
                    },
                    showCancelButton: true,
                    confirmButtonText: 'OK',
                    cancelButtonText: 'Cancel',
                    inputValidator: (value) => {
                        if (!value || Number(value) <= 0) {
                            return 'Nominal harus lebih dari 0';
                        }
                    },
                });

                if (result.isConfirmed) {
                    $.ajax({
                        url: urls.customAmount,
                        type: 'POST',
                        data: {
                            amount: result.value,
                            name: 'Custom Amount',
                            _token: $('meta[name="csrf-token"]').attr('content'),
                        },
                        success: function(res) {
                            if (res.success) {
                                renderPosCart(res.cart);
                                updatePosTotals(res.totals);
                                Swal.fire('Custom Amount', 'Nominal custom masuk ke cart.', 'success');
                            }
                        },
                        error: function(err) {
                            const msg = err.responseJSON?.message || 'Custom amount gagal ditambahkan.';
                            Swal.fire('Error', msg, 'error');
                        }
                    });
                }
            });

            $('.pos-add-customer-btn').on('click', function(e) {
                e.preventDefault();
                $('#addCustomerModal').modal('show');
            });

            // Update giant charge button text when total changes
            const totalSpan = document.getElementById('total');
            const chargeText = document.getElementById('chargeTotalText');
            if (totalSpan && chargeText) {
                const observer = new MutationObserver(() => {
                    chargeText.textContent = totalSpan.textContent;
                });
                observer.observe(totalSpan, { childList: true, subtree: true });
            }

            // AJAX Add Customer
            $('#addCustomerForm').on('submit', function(e) {
                e.preventDefault();
                let btn = $(this).find('button[type="submit"]');
                let form = $(this);
                btn.prop('disabled', true).text('Saving...');
                $.ajax({
                    url: urls.apiCustomer,
                    type: 'POST',
                    data: form.serialize() + '&_token=' + $('meta[name="csrf-token"]').attr('content'),
                    success: function(res) {
                        btn.prop('disabled', false).text('Save Customer');
                        if (res.success) {
                            // Add to hidden select and make it selected
                            let option = new Option(res.customer.name, res.customer.id, true, true);
                            $('#customerId').append(option).trigger('change');
                            $('#addCustomerModal').modal('hide');
                            form[0].reset();
                            Swal.fire({ icon: 'success', title: 'Success', text: 'Customer Added!', timer: 1500, showConfirmButton: false });
                        }
                    },
                    error: function(err) {
                        btn.prop('disabled', false).text('Save Customer');
                        let msg = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'Error occurred';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            // Checkout UI Logic
            let rawTotalDue = 0;
            
            function openCheckout() {
                if (!posSetup.hasStores) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Store belum tersedia',
                        text: 'Buat store terlebih dahulu supaya transaksi POS punya cabang/toko tujuan.',
                        showCancelButton: true,
                        confirmButtonText: 'Create Store',
                        cancelButtonText: 'Nanti',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = urls.createStore;
                        }
                    });
                    return;
                }

                if (!posSetup.hasMethods) {
                    Swal.fire('Payment Method belum tersedia', 'Buat metode pembayaran dulu sebelum checkout.', 'warning');
                    return;
                }

                // The POS total is rendered with two decimal places. Removing
                // every non-digit character turns 12000.00 into 1200000, so
                // preserve the decimal separator when reading it back.
                const totalText = $('#total').text().replace(/[^0-9.,-]/g, '').replace(/,/g, '');
                rawTotalDue = Math.round(parseFloat(totalText) || 0);
                
                if (rawTotalDue === 0) {
                    Swal.fire('Empty Cart', 'Please add items to cart first', 'warning');
                    return;
                }

                $('#checkoutTotalDue').text('Rp ' + rawTotalDue.toLocaleString());
                
                // Reset to 1 payment row filled with full amount
                $('#paymentRowsContainer').html(`
                    <div class="payment-row row mb-2">
                        <div class="col-md-5">
                            <label>Method</label>
                            <select name="payments[0][method_id]" class="form-control payment-method-select">
                                @foreach($methods as $method) <option value="{{ $method->id }}">{{ $method->name }}</option> @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label>Amount</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                <input type="number" name="payments[0][amount]" class="form-control payment-amount-input" step="1" required value="${rawTotalDue}">
                            </div>
                        </div>
                        <div class="col-md-2 text-center" style="padding-top: 32px;">
                            <button type="button" class="btn btn-danger btn-sm remove-payment-row"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                `);
                calculateChange();
                $('#checkoutModal').modal('show');
            }

            $('#openCheckoutBtn, #openCheckoutSplitBtn').on('click', function() {
                openCheckout();
            });

            $('#saveBillBtn').on('click', function() {
                Swal.fire({
                    title: 'Save Bill',
                    input: 'text',
                    inputLabel: 'Nama bill / meja / customer',
                    inputValue: 'Bill ' + new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: urls.holdBill,
                        type: 'POST',
                        data: {
                            bill_name: result.value,
                            store_id: $('#storeId').val(),
                            customer_id: $('#customerId').val(),
                            _token: $('meta[name="csrf-token"]').attr('content'),
                        },
                        success: function(res) {
                            if (res.success) {
                                renderPosCart(res.cart);
                                updatePosTotals(res.totals);
                                Swal.fire('Bill Saved', 'Bill tersimpan server-side sebagai held bill.', 'success').then(() => window.location.reload());
                            }
                        },
                        error: function(err) {
                            const msg = err.responseJSON?.message || 'Save bill gagal.';
                            Swal.fire('Error', msg, 'error');
                        }
                    });
                });
            });

            $('#printBillBtn').on('click', function() {
                window.print();
            });

            $('#openShiftBtn').on('click', function() {
                $('#openShiftModal').modal('show');
            });

            $('#closeShiftBtn').on('click', function() {
                $('#closeShiftModal').modal('show');
            });

            $('#openShiftForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: urls.openShift,
                    type: 'POST',
                    data: $(this).serialize() + '&_token=' + $('meta[name="csrf-token"]').attr('content'),
                    success: function(res) {
                        if (res.success) {
                            Swal.fire('Shift Opened', 'Shift kasir berhasil dibuka.', 'success').then(() => window.location.reload());
                        }
                    },
                    error: function(err) {
                        Swal.fire('Error', err.responseJSON?.message || 'Open shift gagal.', 'error');
                    }
                });
            });

            $('#closeShiftForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: urls.closeShift,
                    type: 'POST',
                    data: $(this).serialize() + '&_token=' + $('meta[name="csrf-token"]').attr('content'),
                    success: function(res) {
                        if (res.success) {
                            const shift = res.shift;
                            Swal.fire({
                                icon: 'success',
                                title: 'Shift Closed',
                                html: `Sales: <b>${shift.sales_count}</b><br>Total: <b>Rp ${Number(shift.sales_total).toLocaleString()}</b><br>Expected Cash: <b>Rp ${Number(shift.expected_cash).toLocaleString()}</b><br>Difference: <b>Rp ${Number(shift.cash_difference).toLocaleString()}</b>`,
                            }).then(() => window.location.reload());
                        }
                    },
                    error: function(err) {
                        Swal.fire('Error', err.responseJSON?.message || 'Close shift gagal.', 'error');
                    }
                });
            });

            $('.restore-held-bill').on('click', function() {
                const billId = $(this).data('id');
                $.ajax({
                    url: urls.restoreHeldBill + '/' + billId + '/restore',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function(res) {
                        if (res.success) {
                            renderPosCart(res.cart);
                            updatePosTotals(res.totals);
                            $('#heldBillsModal').modal('hide');
                            Swal.fire('Bill Restored', 'Held bill berhasil dikembalikan ke cart.', 'success').then(() => window.location.reload());
                        }
                    },
                    error: function(err) {
                        Swal.fire('Error', err.responseJSON?.message || 'Restore bill gagal.', 'error');
                    }
                });
            });

            $('#addSplitPaymentBtn').on('click', function() {
                let index = $('.payment-row').length;
                const currentInputs = $('.payment-amount-input');
                let currentTotal = 0;
                currentInputs.each(function() { currentTotal += parseInt($(this).val()) || 0; });

                // When splitting the first payment, divide it automatically.
                // Further rows receive the remaining amount.
                if (currentInputs.length === 1 && currentTotal >= rawTotalDue) {
                    const firstHalf = Math.floor(rawTotalDue / 2);
                    currentInputs.eq(0).val(firstHalf);
                    currentTotal = firstHalf;
                }
                const remainder = Math.max(0, rawTotalDue - currentTotal);
                $('#paymentRowsContainer').append(`
                    <div class="payment-row row mb-2">
                        <div class="col-md-5">
                            <label>Method</label>
                            <select name="payments[${index}][method_id]" class="form-control payment-method-select">
                                @foreach($methods as $method) <option value="{{ $method->id }}">{{ $method->name }}</option> @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label>Amount</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                <input type="number" name="payments[${index}][amount]" class="form-control payment-amount-input" step="1" required value="${remainder}">
                            </div>
                        </div>
                        <div class="col-md-2 text-center" style="padding-top: 32px;">
                            <button type="button" class="btn btn-danger btn-sm remove-payment-row"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                `);
                calculateChange();
            });

            $(document).on('click', '.remove-payment-row', function() {
                if ($('.payment-row').length > 1) {
                    $(this).closest('.payment-row').remove();
                    
                    // re-index
                    $('.payment-row').each(function(i) {
                        $(this).find('select').attr('name', `payments[${i}][method_id]`);
                        $(this).find('input').attr('name', `payments[${i}][amount]`);
                    });
                    rebalanceSplitPayments();
                    calculateChange();
                } else {
                    Swal.fire('Cannot Remove', 'You must have at least one payment method', 'warning');
                }
            });

            function rebalanceSplitPayments() {
                const inputs = $('.payment-amount-input');
                if (inputs.length <= 1) return;

                let paidBeforeLast = 0;
                inputs.each(function(index) {
                    if (index < inputs.length - 1) {
                        paidBeforeLast += Math.max(0, parseInt($(this).val()) || 0);
                    }
                });

                const remainder = Math.max(0, rawTotalDue - paidBeforeLast);
                inputs.last().val(remainder);
            }

            $(document).on('input', '.payment-amount-input', function() {
                rebalanceSplitPayments();
                calculateChange();
            });

            $(document).on('change', '.payment-method-select', function() {
                const isQris = $(this).find('option:selected').text().trim().toLowerCase() === 'qris';
                const row = $(this).closest('.payment-row');
                row.find('.qris-hint').remove();
                if (isQris) {
                    row.append('<div class="col-12 qris-hint text-info small mt-1"><i class="fas fa-qrcode"></i> QRIS Midtrans akan dibuka setelah checkout.</div>');
                }
            });

            function calculateChange() {
                let totalPaid = 0;
                $('.payment-amount-input').each(function() {
                    totalPaid += parseInt($(this).val()) || 0;
                });
                
                let change = totalPaid - rawTotalDue;
                if (change < 0) {
                    $('#checkoutChangeDue').text('Short by Rp ' + Math.abs(change).toLocaleString()).removeClass('text-success').addClass('text-danger');
                } else {
                    $('#checkoutChangeDue').text('Rp ' + change.toLocaleString()).removeClass('text-danger').addClass('text-success');
                }
            }

            $('#confirmCheckoutBtn').on('click', function() {
                let totalPaid = 0;
                $('.payment-amount-input').each(function() {
                    totalPaid += parseInt($(this).val()) || 0;
                });

                const isSplitPayment = $('.payment-row').length > 1;
                if (isSplitPayment && totalPaid !== rawTotalDue) {
                    Swal.fire('Nominal belum pas', 'Split payment harus sama persis dengan total transaksi.', 'warning');
                    return;
                }
                
                if (totalPaid < rawTotalDue) {
                    Swal.fire('Insufficient Amount', 'The total paid amount is less than the total due.', 'warning');
                    return;
                }

                let btn = $(this);
                btn.prop('disabled', true).text('Processing...');

                let qrisAmount = 0;
                $('.payment-row').each(function() {
                    const methodName = $(this).find('.payment-method-select option:selected').text().trim().toLowerCase();
                    if (methodName === 'qris') {
                        qrisAmount += parseInt($(this).find('.payment-amount-input').val()) || 0;
                    }
                });

                let formData = $('#checkoutForm').serializeArray();
                formData.push({name: 'customer_id', value: $('#customerId').val()});
                formData.push({name: 'store_id', value: $('#storeId').val() || 1});
                formData.push({name: 'status', value: 'completed'});
                formData.push({name: '_token', value: $('meta[name="csrf-token"]').attr('content')});

                $.ajax({
                    url: urls.checkout,
                    type: 'POST',
                    data: $.param(formData),
                    success: function(res) {
                        btn.prop('disabled', false).text('Confirm Payment');
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
                                                Swal.fire('Menunggu pembayaran', 'QRIS belum dibayar. Selesaikan pembayaran terlebih dahulu.', 'info');
                                            },
                                            onError: function() { Swal.fire('QRIS gagal', 'Pembayaran QRIS gagal diproses.', 'error'); },
                                            onClose: function() { Swal.fire('QRIS dibatalkan', 'Pembayaran belum berhasil. Transaksi tetap menunggu pembayaran.', 'info'); },
                                        });
                                    },
                                    error: function(err) {
                                        Swal.fire('QRIS gagal dibuat', err.responseJSON?.message || 'Periksa konfigurasi Midtrans.', 'error').then(openInvoice);
                                    }
                                });
                            } else {
                                Swal.fire('Success', 'Payment completed successfully!', 'success').then(openInvoice);
                            }
                        }
                    },
                    error: function(err) {
                        btn.prop('disabled', false).text('Confirm Payment');
                        let msg = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'Error occurred';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });
        });
    </script>
    <script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
@endpush
