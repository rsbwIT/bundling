<script>
    $(function() {
        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000
        });
        @if (session('sucsessLogin'))
            Toast.fire({
                icon: 'success',
                title: '{{ session('sucsessLogin') }}'
            });
        @endif
        @if (session('successupload'))
            Toast.fire({
                icon: 'success',
                title: '{{ session('successupload') }}'
            });
        @endif
        @if (session('successSavePDF'))
            Toast.fire({
                icon: 'success',
                title: '{{ session('successSavePDF') }}'
            });
        @endif
        @if (session('successGabungberkas'))
            Toast.fire({
                icon: 'success',
                title: '{{ session('successGabungberkas') }}'
            });
        @endif
        @if (session('sucsessSimpanNomor'))
            Toast.fire({
                icon: 'success',
                title: '{{ session('sucsessSimpanNomor') }}'
            });
        @endif
        @if (session('gagalSimpanNomor'))
            Toast.fire({
                icon: 'error',
                title: '{{ session('gagalSimpanNomor') }}'
            });
        @endif
        @if (session('successSaveINACBG'))
            Toast.fire({
                icon: 'success',
                title: 'Berhasil Simpan File {{ session('successSaveINACBG') }}'
            });
        @endif
    });
</script>


