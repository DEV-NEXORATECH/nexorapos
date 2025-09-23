@extends('admin.layout.app')

@section('title', 'Update Product Variant')

@section('content')


        	<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

                         <h4 class="page-title">Product Variant</h4>
						<div >
							<div class="">
								<div class="card">
									<div class="card-header">
										<div class="card-title"> Update Product Variant</div>
									</div>
                                    <form action="{{ url('admin-panel/variants/update', $product->id) }}" method="POST" id="categoryForm">
                                        @csrf
                                        <div class="card-body">
										<div class="form-group">
											<label for="product_id">Product Name</label>
											<select name="product_id" id="product_id" class="form-control">
                                                @foreach ($products as $p)
                                                <option value="{{ $p->id }}" {{ $product->product_id == $p->id? 'selected' : '' }}>{{ $p->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('product_id')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>
										<div class="form-group">
											<label for="name">Variant Name</label>
											<input type="text" class="form-control" name="name" id="name" value="{{ $product->name }}">
                                            @error('name')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="sku">Variant SKU</label>
											<input type="text" class="form-control" name="sku" id="sku" value="{{ $product->sku }}">
                                            @error('name')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="barcode">Variant Barcode</label>
											<input type="text" class="form-control" name="barcode" id="barcode" value="{{ $product->barcode }}">
                                            @error('name')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="price">Variant Price</label>
											<input type="text" class="form-control" name="price" id="price" value="{{ $product->price }}">
                                            @error('price')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

										<div class="form-group">
											<label for="cost">Variant Cost</label>
											<input type="text" class="form-control" name="cost" id="cost" value="{{ $product->cost }}">
                                            @error('cost')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
										</div>

                                        <div class="card-action">
											<button class="btn btn-success">Submit</button>
											 <a href="{{ url('admin-panel/variants/listing') }}" class="btn btn-danger">Cancel</a>
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
