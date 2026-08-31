@extends('layout.layoutModern')

@section('title', 'Demo Tema Modern')

@section('content')
<div class="row align-items-center" style="min-height: 70vh;">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-lg border-0 rounded-4" style="background: linear-gradient(145deg, #ffffff, #f9fafb);">
            <div class="card-body text-center p-5">
                <h1 class="display-5 fw-bold text-primary mb-4">Yeay! Tema Berhasil Dipasang 🎉</h1>
                <p class="lead text-muted mb-4">
                    Ini adalah halaman demonstrasi yang menggunakan <strong>layoutModern.blade.php</strong>. 
                    Anda bisa melihat bahwa desain ini terpisah dari desain AdminLTE bawaan Anda.
                </p>
                <div class="d-flex justify-content-center gap-3 mt-4">
                    <a href="/test-theme" class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm">Muat Ulang</a>
                    <a href="/" class="btn btn-outline-secondary btn-lg rounded-pill px-4">Kembali ke App Asli</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
