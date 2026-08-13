@extends('admin.layout.app')

@section('title', 'Update Sale')

@section('content')


        	<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

                                     <div class="superadmin-header-card mb-4">
                <h4><i class="fas fa-edit"></i> Sale Item</h4>
                <a href="javascript:history.back()" class="btn btn-secondary" style="border: none;">Cancel</a>
            </div>
						<div >
							<div class="">
								<div class="nexora-control-card is-neutral mb-4">
                    <h5><i class="fas fa-folder"></i> Update Sale Item</h5>
                    
                                    <form action="{{ url('admin-panel/sale-items/update', $sale->id) }}" method="POST" id="categoryForm">
                                        @csrf
                                                            <div class="card-body p-0 mt-3">
										<div class="form-group">
											<label for="sale_id">Sale Status</label>
											<select name="sale_id" id="sale_id" class="form-control">
                                                @foreach ($sales as $s)
                                                <option value="{{ $s->id }}" {{ $sale->sale_id == $s->id ? 'selected' : '' }}>{{ $s->status }}</option>
                                                @endforeach
                                            </select>
                                            @error('sale_id ')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="product_id">Product Name</label>
											<select name="product_id" id="product_id" class="form-control">
                                                @foreach ($products as $product)
                                                <option value="{{ $product->id }}" {{ $sale->product_id == $product->id ? 'selected' : ''}}>{{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('product_id')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="product_variant_id">Prduct Variant Name</label>
											<select name="product_variant_id" id="product_variant_id" class="form-control">
                                                @foreach ($variants as $variant)
                                                <option value="{{ $variant->id }}" {{ $sale->product_variant_id == $variant->id ? 'selected': '' }}>{{ $variant->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('product_variant_id')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>


										<div class="form-group">
											<label for="quantity">Quantity</label>
											<input type="number" class="form-control" name="quantity" id="quantity" value="{{ $sale->quantity }}">
                                            @error('quantity')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="price">Price</label>
											<input type="number" class="form-control" name="price" id="price" value="{{ $sale->price }}">
                                            @error('price')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="discount">Discount</label>
											<input type="number" class="form-control" name="discount" id="discount" value="{{ $sale->discount }}">
                                            @error('discount')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="tax">Tax</label>
											<input type="number" class="form-control" name="tax" id="tax" value="{{ $sale->tax }}">
                                            @error('tax')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

                                        <div class="nexora-form-actions mt-3">
											<button class="btn btn-primary">Submit</button>
											 <a href="{{ url('admin-panel/sale-items/listing') }}" class="btn btn-danger">Cancel</a>
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
