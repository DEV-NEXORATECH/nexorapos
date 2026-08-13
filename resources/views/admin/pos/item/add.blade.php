@extends('admin.layout.app')

{{-- Set the page title --}}
@section('title', 'Add Purchase Item')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">

            {{-- Page title --}}
                        <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-edit"></i> Purchase</h4>
                <a href="javascript:history.back()" class="btn btn-secondary" style="border: none;">Cancel</a>
            </div>

            <div>
                <div class="">
                    <div class="nexora-control-card is-neutral mb-4">
                    <h5><i class="fas fa-folder"></i> Add New Purchase</h5>
                    

                        {{-- Form to add new purchase item --}}
                        <form action="{{ url('admin-panel/purchase-item/add') }}" method="POST" id="categoryForm">
                            @csrf {{-- Laravel CSRF protection --}}
                                                <div class="card-body p-0 mt-3">

                                {{-- Dropdown to select purchase (status shown) --}}
                                <div class="form-group">
                                    <label for="purchase_id">Purchase</label>
                                    <select name="purchase_id" id="purchase_id" class="form-control">
                                        @foreach ($purchases as $purchase)
                                            <option value="{{ $purchase->id }}">{{ $purchase->status }}</option>
                                        @endforeach
                                    </select>
                                    {{-- Error display for validation --}}
                                    @error('purchase_id')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Dropdown to select product --}}
                                <div class="form-group">
                                    <label for="product_id">Purchase Product</label>
                                    <select name="product_id" id="product_id" class="form-control">
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Input field for quantity --}}
                                <div class="form-group">
                                    <label for="quantity">Quantity</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control" placeholder="Enter Quantity of Purchase Item">
                                    @error('quantity')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Input field for cost per item --}}
                                <div class="form-group">
                                    <label for="cost">Cost Per Item</label>
                                    <input type="number" name="cost" id="cost" class="form-control" placeholder="Enter Cost Here..">
                                    @error('cost')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Submit and cancel buttons --}}
                                <div class="nexora-form-actions mt-3">
                                    <button class="btn btn-primary">Submit</button>

                                    {{-- Updated cancel link to correct route --}}
                                    <a href="{{ url('admin-panel/purchase-items/listing') }}" class="btn btn-danger">Cancel</a>
                                </div>

                            </div>
                        </form>
                        {{-- End of form --}}

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
