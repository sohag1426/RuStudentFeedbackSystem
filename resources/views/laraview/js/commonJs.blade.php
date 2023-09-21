<script type="text/javascript">
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 6000
    });
</script>

@if(session('success'))
<script type="text/javascript">
    Toast.fire({
            icon: 'success',
            title: '{{ session('success') }}'
        })
</script>
@endif

@if(session('error'))
<script type="text/javascript">
    Toast.fire({
            icon: 'error',
            title: '{{ session('error') }}'
        })
</script>
@endif

@if(session('info'))
<script type="text/javascript">
    Toast.fire({
            icon: 'info',
            title: '{{ session('info') }}'
        })
</script>
@endif

@if(session('warning'))
<script type="text/javascript">
    Toast.fire({
            icon: 'warning',
            title: '{{ session('warning') }}'
        })
</script>
@endif

<script>
    $('#data_table').DataTable( {
        responsive: {
            details: true
        },
        "searching": true,
        "pageLength": 50,
        "lengthChange": true,
        "ordering": false,
        "autoWidth": false
    });

    $("#phpPaging").DataTable({
        "autoWidth": false,
        "info": false,
        "pageLength": 50,
        "lengthChange": false,
        "ordering": false,
        "paging": false,
        "searching": false,
        responsive: {
            details: true
        }
    });

    function callURL(url, id) {
        let selector = "#" + id;
        $(selector).html('<i class="fas fa-sync-alt fa-spin"></i>');
        $(selector).prop("onclick", null).off("click", selector);
        $.get(url, function(data) {
            $(selector).html('Done!');
            Toast.fire({
                icon: 'success',
                title: data
            });
        });
    }
</script>