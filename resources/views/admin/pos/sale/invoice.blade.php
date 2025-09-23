@extends('admin.layout.app')

@section('title', 'Invoice')

@section('content')

		<div class="main-panel">
			<div class="content">
				<div class="container-fluid">
					<div class="card">
						<div class="card-header d-flex justify-content-between align-items-center">
							<button class="btn btn-primary print">Print</button>
						</div>
						<div class="card-body" id="invoice">
							<div class="row">
								<div class="col-md-6">
									<h5>Store</h5>
									<div>{{ optional($sale->store)->name }}</div>
									<div class="card-title">Invoice #{{ $sale->id }}</div>
									<div>{{ optional($sale->store)->address ?? 'Islamabad, Pakistan' }}</div>
								</div>
								<div class="col-md-6 text-right">
									<h5>Customer</h5>
									<div>{{ optional($sale->customer)->name ?? 'Walk-in' }}</div>
									<div>Cashier: {{ optional($sale->user)->name }}</div>
									<div>Date: {{ $sale->created_at }}</div>
								</div>
							</div>
							<hr>
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>Item</th>
										<th>Qty</th>
										<th>Price</th>
										<th>Discount</th>
										<th>Total</th>
									</tr>
								</thead>
								<tbody>
									@foreach($sale->sale_items as $it)
										@php
											$lineBase = ($it->price ?? 0) * ($it->quantity ?? 0);
											$lineDiscount = ($it->discount ?? 0) * ($it->quantity ?? 0);
											$lineTotal = $lineBase - $lineDiscount;
										@endphp
										<tr>
											<td>{{ optional($it->product)->name }}</td>
											<td>{{ $it->quantity }}</td>
											<td>{{ number_format($it->price ?? 0, 2) }}</td>
											<td>{{ number_format($it->discount ?? 0, 2) }}</td>
											<td>{{ number_format($lineTotal, 2) }}</td>
										</tr>
									@endforeach
								</tbody>
							</table>
							<div class="text-right">
								<div>Subtotal: {{ number_format($sale->subtotal ?? 0, 2) }}</div>
								<div>Discount: {{ number_format($sale->discount ?? 0, 2) }}</div>
								<div>Tax: {{ number_format($sale->tax ?? 0, 2) }}</div>
								<div><strong>Grand Total: {{ number_format($sale->total ?? 0, 2) }}</strong></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

@endsection


@push('script')
    <script src="{{ asset('admin/pos/custom.js') }}"></script>
@endpush
