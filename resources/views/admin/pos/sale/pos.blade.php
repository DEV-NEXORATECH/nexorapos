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
            <div class="nav-item active">
                <i class="fas fa-star"></i>
                <span>Favorites</span>
            </div>
            <div class="nav-item">
                <i class="fas fa-list-ul"></i>
                <span>Library</span>
            </div>
            <div class="nav-item">
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
                    <button class="btn pos-add-customer-btn">
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
                <button class="btn pos-action-btn flex-fill">Save Bill</button>
                <button class="btn pos-action-btn flex-fill">Print Bill</button>
            </div>
            
            <div class="d-flex">
                <button class="btn pos-split-btn">
                    <i class="fas fa-layer-group"></i><br><small>Split Bill</small>
                </button>
                <button id="checkoutBtn" class="btn pos-charge-btn flex-fill">
                    Charge <span id="chargeTotalText">Rp 0</span>
                </button>
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
        checkout: "{{ url('admin-panel/pos/checkout') }}"
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
        });
    </script>
@endpush
