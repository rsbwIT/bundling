@extends('..layout.layoutLogin')
@section('title', 'Login')

@section('konten')
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="#" class="h1"><img src="data:image/png;base64,{{ base64_encode($getSetting->logo) }}"
                    alt="Girl in a jacket" width="100" height="100"></a>
            </div>
            <div class="card-body">
                @if (session('errorLogin'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Gagal Login! </strong> {{ session('errorLogin') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @elseif (session('reqLogin'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>Peringatan! </strong> {{ session('reqLogin') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @elseif (session('sucsessLogout'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>Berhasil! </strong> {{ session('sucsessLogout') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @else
                    <p class="login-box-msg">Masuk untuk memulai sesi baru</p>
                @endif
                <form action="{{ url('/mesinlogin') }}" method="POST">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="id_user" placeholder="Id User" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember">
                                <label for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                    </div>
                </form>
                <hr class="my-4">
            </div>
        </div>
    </div>
@endsection
