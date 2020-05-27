const flashdata = $('.flash-data').data('flashdata');

if (flashdata) {
    Swal.fire({
        title: 'Data Mahasiswa ',
        text: 'Berhasil ' + flashdata,
        type: 'success'
    });
}

$('.tombol-hapus').on('click', function (e) {

    e.preventDefault();

    const href = $(this).attr('href');
    const remark = $(this).attr('id');

    Swal.fire({
        title: 'Are you sure?',
        text: "( " + remark + " )",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.value) {
            document.location.href = href;
        }
    })

});