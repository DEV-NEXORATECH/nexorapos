$(document).ready(function () {

    // Initialize DataTable
    $('#categoriesTable').DataTable({
        paging: true,
        searching: true,
        lengthChange: false,
        pageLength: 10,
        info: true,
        autoWidth: false,
    });

    // SweetAlert Delete
    $(document).on('click', '.delete-category', function(e) {
        e.preventDefault();
        var link = $(this).attr('href');

        Swal.fire({
            title: 'Are you sure?',
            text: "This category will be permanently deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = link;
            }
        });
    });

    // SweetAlert Update (optional)
    $(document).on('click', '.update-category', function(e) {
        e.preventDefault();
        var link = $(this).attr('href');

        Swal.fire({
            title: 'Are you sure you want to edit this category?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = link;
            }
        });
    });

});
