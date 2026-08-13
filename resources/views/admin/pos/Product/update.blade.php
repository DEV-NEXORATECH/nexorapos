@extends('admin.layout.app')

@section('title', 'Update Product')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="nexora-page-head">
                <div>
                    <h4 class="page-title mb-1">Edit Product</h4>
                    <p class="card-category mb-0">Pastikan relasi kategori dan tax tetap sesuai company.</p>
                </div>
                <a href="{{ url('admin-panel/products/listing') }}" class="btn btn-secondary">Cancel</a>
            </div>

            <form action="{{ url('admin-panel/products/update', $product->id) }}" method="POST" class="nexora-form">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Product Information</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Product Name *</label>
                                    <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $product->name) }}" required>
                                    @error('name')<div class="nexora-field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category_id">Category *</label>
                                    <select name="category_id" id="category_id" class="form-control select2" required>
                                        <option value="">Select category</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id) == $cat->id)>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')<div class="nexora-field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sku">SKU *</label>
                                    <input type="text" class="form-control" name="sku" id="sku" value="{{ old('sku', $product->sku) }}" required>
                                    @error('sku')<div class="nexora-field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="barcode">Barcode</label>
                                    <input type="text" class="form-control" name="barcode" id="barcode" value="{{ old('barcode', $product->barcode) }}">
                                    @error('barcode')<div class="nexora-field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <div class="card-title">Pricing</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cost">Cost Price</label>
                                    <input type="number" step="0.01" min="0" class="form-control" name="cost" id="cost" value="{{ old('cost', $product->cost) }}">
                                    @error('cost')<div class="nexora-field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="price">Selling Price *</label>
                                    <input type="number" step="0.01" min="0" class="form-control" name="price" id="price" value="{{ old('price', $product->price) }}" required>
                                    @error('price')<div class="nexora-field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tax_id">Tax *</label>
                                    <select name="tax_id" id="tax_id" class="form-control select2" required>
                                        <option value="">Select tax</option>
                                        @foreach($taxes as $tax)
                                            <option value="{{ $tax->id }}" @selected(old('tax_id', $product->tax_id) == $tax->id)>{{ $tax->name }} - {{ $tax->rate }}%</option>
                                        @endforeach
                                    </select>
                                    @error('tax_id')<div class="nexora-field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <div class="card-title">Inventory</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="track_stock">Track Stock *</label>
                                    <select name="track_stock" id="track_stock" class="form-control" required>
                                        <option value="1" @selected(old('track_stock', $product->track_stock) == 1)>Yes</option>
                                        <option value="0" @selected(old('track_stock', $product->track_stock) == 0)>No</option>
                                    </select>
                                    @error('track_stock')<div class="nexora-field-error">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer nexora-form-actions">
                        <a href="{{ url('admin-panel/products/listing') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Product</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
