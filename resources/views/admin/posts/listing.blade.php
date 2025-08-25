@extends('admin.layout.app')

@section('title', 'All Posts')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="page-title">Posts</h4>
                <div id="currentDate" class="text-muted fw-bold"></div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title">Posts</div>
                            <div>
                                <a href="{{ url('admin-panel/posts/add') }}" class="btn btn-primary">Add New Post</a>
                            </div>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered table-striped" id="categoriesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User Name</th>
                                        <th>Category</th>
                                        <th>Post Title</th>
                                        <th>Content</th>
                                        <th>Status</th>
                                        <th>Image</th>
                                        <th class="min-w-120 text-nowrap">Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .min-w-120 { min-width: 120px; }
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {

    // Display current date
    let today = new Date();
    let options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    $('#currentDate').text(today.toLocaleDateString(undefined, options));

    // Initialize DataTable with Buttons
    var table = $('#categoriesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('admin-panel/posts/listing') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'user_name', name: 'user_name' },
            { data: 'category_name', name: 'category_name' },
            { data: 'title', name: 'title' },
            { data: 'content', name: 'content' },
            { data: 'status', name: 'status' },
            { data: 'image', name: 'image', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        dom: 'Bfrtip', // Display buttons at the top
        buttons: [
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5',
            'print',
            {
                text: 'Reload',
                action: function ( e, dt, node, config ) {
                    dt.ajax.reload();
                }
            }
        ]
    });

    // Delete confirmation
    $('#categoriesTable').on('click', '.delete-post', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete the post!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) window.location.href = url;
        })
    });
});
</script>
@endsection
