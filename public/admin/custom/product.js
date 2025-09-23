$(document).ready(function(){
    $('#importModal').on('click', function(){
        $('#fileInput').click();
    });

    $('#fileInput').on('change', function(e){
        const file = e.target.files[0];
        if(file){
            console.log("Selected file:", file.name);

            var formData = new FormData();
            formData.append('file', file);

            $.ajax({
                url: '/admin-panel/products/import',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response){
                    console.log("Upload successful:", response);
                    alert("File imported successfully!");
                },
                error: function(xhr, status, error){
                    console.error("Upload error:", xhr.status, error);
                    alert("File import failed!");
                }
            });
        }
    });
});
