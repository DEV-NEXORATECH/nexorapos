@extends('admin.layout.app')

@section('title', 'Update Product')

@section('content')


        	<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

                         <h4 class="page-title">Products</h4>
						<div >
							<div class="">
								<div class="card">
									<div class="card-header">
										<div class="card-title">Update Product</div>
									</div>
                                    <form action="{{ url('admin-panel/products/update', $product->id) }}" method="POST" id="categoryForm">
                                        @csrf
                                        <div class="card-body">
										<div class="form-group">
											<label for="tax_id">Tax Type</label>
											<select name="tax_id" id="tax_id" class="form-control">
                                                @foreach($taxes as $tax)
                                                    <option value="{{ $tax->id }}" {{ $product->tax_id == $tax->id ? 'selected' : '' }}>
                                                        {{ $tax->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('tax_id')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="categoty_id">Category Name</label>
											<select name="category_id" id="category_id" class="form-control">
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                                                        {{ $cat->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

                                        {{-- product name --}}
                                        <div class="form-group">
                                            <label for="name">Product Name</label>
                                            <input type="text" class="form-control" name="name" id="name" value="{{$product->name}}">
                                            @error('name')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        {{-- product sku --}}
                                        <div class="form-group">
                                            <label for="sku">SKU</label>
                                            <input type="text" class="form-control" name="sku" id="sku"  value="{{$product->sku}}">
                                            @error('sku')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        {{-- product barcode --}}
                                        <div class="form-group">
                                            <label for="barcode">Barcode</label>
                                            <input type="text" class="form-control" name="barcode" id="barcode"  value="{{$product->barcode}}">
                                            @error('barcode')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        {{-- product price --}}
                                        <div class="form-group">
                                            <label for="price">Price</label>
                                            <input type="text" class="form-control" name="price" id="price"  value="{{$product->price}}">
                                            @error('price')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        {{-- product cost --}}
                                        <div class="form-group">
                                            <label for="cost">Cost</label>
                                            <input type="text" class="form-control" name="cost" id="cost"  value="{{$product->cost}}">
                                            @error('cost')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        {{-- track stock --}}
                                        <div class="form-group">
                                            <label for="track_stock">Track Stock</label>
                                            <select name="track_stock" id="track_stock" class="form-control">
                                                <option value="1" {{ $product->track_stock == 1 ? 'selected' : '' }}>Yes</option>
                                                <option value="0" {{ $product->track_stock == 0 ? 'selected' : '' }}>No</option>
                                            </select>
                                            @error('track_stock')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        </div>
                                        <div class="card-action">
											<button class="btn btn-success">Update</button>
											 <a href="{{ url('admin-panel/tax/listing') }}" class="btn btn-danger">Cancel</a>
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
