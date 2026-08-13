@extends('admin.layout.app')

@section('title', 'Update Purchase Item')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
                        <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-edit"></i> Purchase</h4>
                <a href="javascript:history.back()" class="btn btn-secondary" style="border: none;">Cancel</a>
            </div>
            <div>
                <div class="">
                    <div class="nexora-control-card is-neutral mb-4">
                    <h5><i class="fas fa-folder"></i> Update Purchase</h5>
                    

                        <form action="{{ url('admin-panel/purchase-items/update', $item->id) }}" method="POST" id="categoryForm">
                            @csrf

                                        <div class="row">
                                            @include('admin.partials.company-selector')
                                        </div>
                                                <div class="card-body p-0 mt-3">
                                {{-- Purchase Dropdown --}}
                                <div class="form-group">
                                    <label for="purchase_id">Purchase Status</label>
                                    <select name="purchase_id" id="purchase_id" class="form-control">
                                        @foreach ($purchases as $purchase)
                                            <option value="{{ $purchase->id }}"
                                                {{ $purchase->id == $item->purchase_id ? 'selected' : '' }}>
                                                {{ $purchase->status }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('purchase_id')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Product Dropdown --}}
                                <div class="form-group">
                                    <label for="product_id">Purchase Product</label>
                                    <select name="product_id" id="product_id" class="form-control">
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}"
                                                {{ $product->id == $item->product_id ? 'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Quantity --}}
                                <div class="form-group">
                                    <label for="quantity">Quantity</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control"
                                        value="{{ $item->quantity }}">
                                    @error('quantity')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Cost --}}
                                <div class="form-group">
                                    <label for="cost">Cost Per Item</label>
                                    <input type="number" name="cost" id="cost" class="form-control"
                                        value="{{ $item->cost }}">
                                    @error('cost')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Buttons --}}
                                <div class="nexora-form-actions mt-3">
                                    <button class="btn btn-primary">Update</button>
                                    <a href="{{ url('admin-panel/purchase-items/listing') }}" class="btn btn-danger">Cancel</a>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
