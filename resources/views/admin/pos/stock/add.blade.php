@extends('admin.layout.app')

@section('title', 'Add Stock')

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
                                    <form action="{{ url('admin-panel/stock/add') }}" method="POST" id="categoryForm">
                                        @csrf
                                        <div class="card-body">
										<div class="form-group">
											<label for="store_id">Store Name</label>
											<select name="store_id" id="store_id" class="form-control">
                                                @foreach ($stores as $store)
                                                <option value="{{ $store->id }}">{{ $store->name }}</option>
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
                                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('product_id')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="product_variant_id">Variant Name</label>
											<select name="product_variant_id" id="product_variant_id" class="form-control">
                                                @foreach ($variants as $var)
                                                    <option value="{{ $var->id }}">{{ $var->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('product_variant_id')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="quantity">Quantity</label>
											<input type="text" class="form-control" name="quantity" id="quantity"  placeholder="Enter Stock quantity">
                                            @error('quantity')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="type">Type</label>
											<input type="text" class="form-control" name="type" id="type"  placeholder="Enter Stock Type">
                                            @error('type')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

                                        <div class="card-action">
											<button class="btn btn-success">Submit</button>
											 <a href="{{ url('admin-panel/stock/listing') }}" class="btn btn-danger">Cancel</a>
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
