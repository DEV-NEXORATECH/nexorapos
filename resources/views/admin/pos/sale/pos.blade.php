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
            <div class="nav-item active" onclick="Swal.fire('Favorites', 'Menampilkan item favorit.', 'info')">
                <i class="fas fa-star"></i>
                <span>Favorites</span>
            </div>
            <div class="nav-item" onclick="Swal.fire('Library', 'Fitur Library Item sedang dalam pengembangan.', 'info')">
                <i class="fas fa-list-ul"></i>
                <span>Library</span>
            </div>
            <div class="nav-item" onclick="Swal.fire('Custom Amount', 'Fitur input Custom Amount sedang dalam pengembangan.', 'info')">
                <i class="fas fa-calculator"></i>
                <span>Custom</span>
            </div>
        </div>
    </div>

    <!-- Right Section: Cart / Billing -->
    <div class="pos-right-panel">
        <div class="pos-cart-header">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="billing-icon">
                    <i class="fas fa-receipt"></i>
                    <small>Billing List</small>
                </div>
                <div>
                    <a href="{{ url('cfd/'.auth()->id()) }}" target="_blank" class="btn btn-sm btn-outline-primary" style="border-radius:8px; font-weight:600; margin-right: 5px;" title="Open Customer Facing Display">
                        <i class="fas fa-desktop"></i> CFD
                    </a>
                    <button class="btn pos-add-customer-btn" data-toggle="modal" data-target="#addCustomerModal">
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
                    @foreach($stores as $store) <option value="{{ $store->id }}">{{ $store->name }}</option> @endforeach
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

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Pass URLs to JS -->
<script>
    const urls = {
        add: "{{ url('admin-panel/pos/cart/add') }}",
        update: "{{ url('admin-panel/pos/cart/update') }}",
        remove: "{{ url('admin-panel/pos/cart/remove') }}",
        checkout: "{{ url('admin-panel/pos/checkout') }}",
        apiCustomer: "{{ url('admin-panel/pos/customers/api') }}"
    };
</script>
@endsection

@push('script')
    <script src="{{ asset('admin/pos/custom.js') }}"></script>
    <script>
        // Additional UI logic for the new design
        document.addEventListener('DOMContentLoaded', function() {
            // Category filtering
            const catBtns = document.querySelectorAll('.pos-cat-btn');
            const productCards = document.querySelectorAll('.pos-product-card');
            
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
                let totalText = $('#total').text().replace(/[^0-9]/g, '');
                rawTotalDue = parseInt(totalText) || 0;
                
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

            $('#addSplitPaymentBtn').on('click', function() {
                let index = $('.payment-row').length;
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
                                <input type="number" name="payments[${index}][amount]" class="form-control payment-amount-input" step="1" required value="0">
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
                    calculateChange();
                } else {
                    Swal.fire('Cannot Remove', 'You must have at least one payment method', 'warning');
                }
            });

            $(document).on('input', '.payment-amount-input', function() {
                calculateChange();
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
                
                if (totalPaid < rawTotalDue) {
                    Swal.fire('Insufficient Amount', 'The total paid amount is less than the total due.', 'warning');
                    return;
                }

                let btn = $(this);
                btn.prop('disabled', true).text('Processing...');

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
                            Swal.fire('Success', 'Payment completed successfully!', 'success').then(() => {
                                if (res.redirect) {
                                    window.open(res.redirect, '_blank');
                                }
                                window.location.reload();
                            });
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
@endpush
