@extends('admin.layout.app')

{{-- Set the page title --}}
@section('title', 'Add Purchase Item')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">

            {{-- Page title --}}
            <h4 class="page-title">Purchase</h4>

            <div>
                <div class="">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Add New Purchase</div>
                        </div>

                        {{-- Form to add new purchase item --}}
                        <form action="{{ url('admin-panel/purchase-item/add') }}" method="POST" id="categoryForm">
                            @csrf {{-- Laravel CSRF protection --}}
                            <div class="card-body">

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
                                <div class="card-action">
                                    <button class="btn btn-success">Submit</button>

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
