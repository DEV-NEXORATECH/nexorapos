@extends('admin.layout.app')

@section('title', 'Update Stock')

@section('content')


        	<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

                         <h4 class="page-title">Stock</h4>
						<div >
							<div class="">
								<div class="card">
									<div class="card-header">
										<div class="card-title">Move Stock To ...</div>
									</div>
                                    <form action="{{ url('admin-panel/stock/update', $stock->id) }}" method="POST" id="categoryForm">
                                        @csrf
                                        <div class="card-body">
										<div class="form-group">
											<label for="store_id">Store Name</label>
											<select name="store_id" id="store_id" class="form-control">
                                                @foreach ($stores as $store)
                                                <option value="{{ $store->id }}" {{ $store->id == $stock->store_id ? 'selected': '' }}>{{ $store->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('store_id')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>
										<div class="form-group">
											<label for="product_id">Product Name</label>
											<select name="product_id" id="product_id" class="form-control">
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}" {{ $stock->product_id ==$product->id ? 'selected' : ''}}>{{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('product_id')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="product_variant_id">Variant Name</label>
											<select name="product_variant_id" id="product_variant_id" class="form-control">
                                                <option value="">No Variant</option>
                                                @foreach ($variants as $var)
                                                    <option value="{{ $var->id }}" {{ $var->id == $stock->product_variant_id ? 'selected' : '' }}>{{ $var->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('product_variant_id')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="quantity">Quantity</label>
											<input type="number" min="0" step="1" class="form-control" name="quantity" id="quantity" value="{{ $stock->quantity }}">
                                            @error('quantity')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="type">Type</label>
											<select name="type" id="type" class="form-control">
                                                <option value="in" {{ $stock->type === 'in' ? 'selected' : '' }}>Stock In</option>
                                                <option value="out" {{ $stock->type === 'out' ? 'selected' : '' }}>Stock Out</option>
                                            </select>
                                            @error('type')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

                                        <div class="card-action">
											<button class="btn btn-primary">Submit</button>
											 <a href="{{ url('admin-panel/stock/listing') }}" class="btn btn-secondary">Cancel</a>
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
