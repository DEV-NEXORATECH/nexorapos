@extends('admin.layout.app')

@section('title', 'Add Stock')

@section('content')


        	<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

                                     <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-edit"></i> Stock</h4>
                <a href="javascript:history.back()" class="btn btn-secondary" style="border: none;">Cancel</a>
            </div>
						<div >
							<div class="">
								<div class="nexora-control-card is-neutral mb-4">
                    <h5><i class="fas fa-folder"></i> Move Stock To ...</h5>
                    
                                    <form action="{{ url('admin-panel/stock/add') }}" method="POST" id="categoryForm">
                                        @csrf

                                        <div class="row">
                                            @include('admin.partials.company-selector')
                                        </div>
                                                            <div class="card-body p-0 mt-3">
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
                                                <option value="">No Variant</option>
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
											<input type="number" min="0" step="1" class="form-control" name="quantity" id="quantity"  placeholder="Enter Stock quantity">
                                            @error('quantity')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="type">Type</label>
											<select name="type" id="type" class="form-control">
                                                <option value="in">Stock In</option>
                                                <option value="out">Stock Out</option>
                                            </select>
                                            @error('type')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

                                        <div class="nexora-form-actions mt-3">
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
@section('scripts')


@endsection
