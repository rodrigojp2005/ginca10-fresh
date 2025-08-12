@extends('layouts.app')
@section('content')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'info',
            title: 'Você já jogou esta gincana!',
            text: 'Você será redirecionado para a página inicial.',
            confirmButtonText: 'OK',
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then(function() {
            window.location.href = '/';
        });
    });
</script>
@endsection