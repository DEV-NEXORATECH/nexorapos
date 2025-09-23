@extends('admin.layout.app')

@section('title', 'POS Sell')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">POS</h4>
            <div class="row">
                <!-- Products Section -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title">Products</div>
                            <div class="form-inline">
                                <select id="categoryFilter" class="form-control mr-2">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <input id="productSearch" type="text" class="form-control" placeholder="Search product name">
                            </div>
                        </div>
                        <div class="card-body">
                          <div class="row" id="productGrid">
    @foreach($products as $p)
        <div class="col-md-4 mb-3 product-card"
             data-id="{{ $p->id }}"
             data-price="{{ $p->price ?? 0 }}"
             data-category="{{ $p->category_id }}"
             data-name="{{ strtolower($p->name) }}">
            <div class="border p-2 h-100 d-flex flex-column clickable-card" style="cursor:pointer;">
                <strong>{{ $p->name }}</strong>
                <small class="text-muted">{{ optional($p->category)->name }}</small>
                <div class="mt-2">Price: {{ number_format($p->price ?? 0, 2) }}</div>

                {{-- @if($p->product_variants && $p->product_variants->count())
                    <select class="form-control form-control-sm mt-2 variant-select">
                        <option value="">No Variant</option>
                        @foreach($p->product_variants as $v)
                            <option value="{{ $v->id }}">{{ $v->name ?? ('#'.$v->id) }}</option>
                        @endforeach
                    </select>
                @endif --}}
            </div>
        </div>
    @endforeach
</div>

                        </div>
                    </div>
                </div>

                <!-- Cart Section -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title">Cart</div>
                        </div>
                        <div class="card-body" id="cartContainer">
                            <div id="cartItems"></div>
                            <hr>
                            <div id="cartTotals">
                                <div>Subtotal: <span id="subtotal">0.00</span></div>
                                <div>Discount: <span id="discount">0.00</span></div>
                                <div>Tax: <span id="tax">0.00</span></div>
                                <div><strong>Total: <span id="total">0.00</span></strong></div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label>Customer</label>
                                <select id="customerId" class="form-control">
                                    <option value="1">Walk-in</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Store</label>
                                <select id="storeId" class="form-control">
                                    <option value="1">Default</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button id="checkoutBtn" class="btn btn-success btn-block">Checkout & Print</button>
                        </div>
                    </div>
                </div>
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
@endpush
