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
            text: "Would You Like to Delete This Item!",
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

     $(document).on('click', '.force-delete', function(e) {
        e.preventDefault();
        var link = $(this).attr('href');

        Swal.fire({
            title: 'Are you sure?',
            text: "Would You Really Like TO Delete Your Record Permanently?",
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
    $(document).on('click', '.restore-category', function(e) {
        e.preventDefault();
        var link = $(this).attr('href');

        Swal.fire({
            title: 'Are you sure you want to restore this Item?',
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
