@extends('admin.layout.app')

@section('title', 'Setting')

@section('content')

<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h4 class="page-title">Settings</h4>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title">Setting</div>
                            <div>
                                <a href="{{ url('admin-panel/settings/add') }}" class="btn btn-primary">Add New Setting</a>
                            </div>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered table-striped" id="categoriesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Key</th>
                                        <th>Value</th>
                                        <th class="min-w-120 text-nowrap">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($settings as $setting)
                                        <tr>
                                            <td>{{ $setting->id }}</td>
                                            <td>{{ $setting->key }}</td>
                                            <td>{{ $setting->value }}</td>
                                            <td class="text-nowrap">
                                                <a href="{{ url('admin-panel/settings/edit', $setting->id) }}" class="btn btn-success me-2"><i class="fas fa-edit"></i></a>
                                                <a href="{{ url('admin-panel/settings/delete', $setting->id) }}" class="btn btn-danger delete-category"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .min-w-120 {
        min-width: 120px;
    }
</style>
@endsection

@section('scripts')
<script src="{{ asset('admin/custom/js/category.js') }}"></script>
@endsection
