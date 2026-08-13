$(document).ready(function () {

    // Initialize DataTable (retrieve: true prevents reinitialization errors when page scripts also call DataTable)
    if (!$.fn.DataTable.isDataTable('#categoriesTable')) {
        $('#categoriesTable').DataTable({
            paging: true,
            searching: true,
            lengthChange: false,
            pageLength: 10,
            info: true,
            autoWidth: false,
            responsive: false,
            language: {
                search: '',
                searchPlaceholder: 'Cari data...',
                emptyTable: 'Belum ada data untuk ditampilkan.',
                zeroRecords: 'Data tidak ditemukan.',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ data)',
                loadingRecords: 'Memuat data...',
                processing: 'Memproses...',
                paginate: {
                    previous: 'Prev',
                    next: 'Next',
                },
            },
        });
    }

    // SweetAlert Delete
    $(document).on('click', '.delete-category', function(e) {
        e.preventDefault();
        var link = $(this).attr('href');

        Swal.fire({
            title: 'Delete this data?',
            text: 'Data akan dihapus dari listing. Pastikan data ini memang sudah tidak dipakai sebelum melanjutkan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0058A6',
            cancelButtonColor: '#FFFFFF',
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                submitDelete(link);
            }
        });
    });

    function submitDelete(action) {
        var token = $('meta[name="csrf-token"]').attr('content');
        var form = $('<form>', {
            method: 'POST',
            action: action
        });

        form.append($('<input>', { type: 'hidden', name: '_token', value: token }));
        form.append($('<input>', { type: 'hidden', name: '_method', value: 'DELETE' }));
        $('body').append(form);
        form.trigger('submit');
    }

     $(document).on('click', '.force-delete', function(e) {
        e.preventDefault();
        var link = $(this).attr('href');

        Swal.fire({
            title: 'Delete permanently?',
            text: 'Data akan dihapus permanen. Aksi ini tidak bisa dibatalkan dari aplikasi.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0058A6',
            cancelButtonColor: '#FFFFFF',
            confirmButtonText: 'Delete Permanently',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                submitDelete(link);
            }
        });
    });

    // SweetAlert Update (optional)
    $(document).on('click', '.restore-category', function(e) {
        e.preventDefault();
        var link = $(this).attr('href');

        Swal.fire({
            title: 'Restore this data?',
            text: 'Data akan dikembalikan ke listing aktif. Setelah restore, data bisa digunakan kembali.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0058A6',
            cancelButtonColor: '#FFFFFF',
            confirmButtonText: 'Restore',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = link;
            }
        });
    });

});
