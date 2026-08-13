@extends('admin.layout.app')

@section('title', 'Setting')

@section('content')

<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="superadmin-header-card mb-4" style="align-items: center;">
                <h4 class="mt-1 mb-2" style="color: #fff;"><i class="fas fa-sliders-h"></i> System Settings</h4>
                <a href="{{ url('admin-panel/settings/add') }}" class="btn" style="background-color: #FFFFFF !important; color: #0058A6 !important; border: none !important; white-space: nowrap; font-weight: 600; padding: 8px 16px; border-radius: 6px;">
                    + Add New Setting
                </a>
            </div>

            <table class="table nexora-data-table" id="categoriesTable" style="width:100%">
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

<style>
    .min-w-120 {
        min-width: 120px;
    }
</style>
@endsection

@section('scripts')
<script src="{{ asset('admin/custom/js/category.js') }}"></script>
@endsection
