@extends('..layout.layoutLogin')
@section('title', 'Login')

@section('konten')

<style>
    body {
        min-height: 100vh;
        background:
            radial-gradient(circle at 20% 30%, #5f9cff 0%, transparent 40%),
            radial-gradient(circle at 80% 20%, #9b5cff 0%, transparent 40%),
            radial-gradient(circle at 50% 80%, #20c997 0%, transparent 45%),
            #0b1020;
        overflow: hidden;
        font-family: 'Segoe UI', sans-serif;
    }

    /* grid halus */
    body::before {
        content: "";
        position: fixed;
        inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
        background-size: 40px 40px;
        pointer-events: none;
        z-index: 0;
    }

    .login-box {
        position: relative;
        z-index: 2;
        animation: fadeInUp 0.9s ease;
    }

    .card {
        background: rgba(255,255,255,0.94);
        border-radius: 18px;
        box-shadow: 0 35px 90px rgba(0,0,0,.45);
        backdrop-filter: blur(12px);
    }

    .card-header {
        border-bottom: none;
        padding-top: 25px;
    }

    .login-box-msg {
        font-weight: 500;
        color: #444;
    }

    .btn-primary {
        border-radius: 10px;
        font-weight: 600;
        letter-spacing: .5px;
    }

    .input-group-text {
        border-radius: 0 8px 8px 0;
    }

    /* animasi masuk */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(35px) scale(0.97);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @keyframes floatLogo {
        0% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0); }
    }

    .footer-text {
        font-size: 13px;
        color: #777;
    }
</style>

<div class="login-box">
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <a href="#" class="h1">
                <img class="logo-login"
                     src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
                     alt="Logo"
                     width="100"
                     height="100">
            </a>
        </div>

        <div class="card-body">

            @if (session('errorLogin'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <strong>Gagal Login!</strong> {{ session('errorLogin') }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>

            @elseif (session('reqLogin'))
                <div class="alert alert-warning alert-dismissible fade show">
                    <strong>Peringatan!</strong> {{ session('reqLogin') }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>

            @elseif (session('sucsessLogout'))
                <div class="alert alert-success alert-dismissible fade show">
                    <strong>Berhasil!</strong> {{ session('sucsessLogout') }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>

            @else
                <p class="login-box-msg">Masuk untuk memulai sesi baru</p>
            @endif

            <form action="{{ url('/mesinlogin') }}" method="POST">
                @csrf

                <div class="input-group mb-3">
                    <input type="text"
                           class="form-control"
                           name="id_user"
                           placeholder="ID User"
                           required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <input type="password"
                           class="form-control"
                           name="password"
                           placeholder="Password"
                           required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>

                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember">
                            <label for="remember">Remember Me</label>
                        </div>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">
                            Sign In
                        </button>
                    </div>
                </div>
            </form>

            <hr class="my-4">

            <p class="text-center footer-text mb-0">
                © {{ date('Y') }} Sistem Informasi Rumah Sakit
            </p>

        </div>
    </div>
</div>

@endsection
