@extends('layouts.app')
@section('title', 'Login - GestionaleHR')
@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center" style="background:linear-gradient(135deg,#1e293b,#334155);">
    <div class="card shadow-lg" style="width:100%;max-width:440px;border-radius:16px;border:none;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white mb-3" style="width:60px;height:60px;"><i class="bi bi-people-fill" style="font-size:1.5rem;"></i></div>
                <h3 class="fw-bold">GestionaleHR</h3>
                <p class="text-muted">Accedi al tuo account</p>
            </div>
            @if(session('success'))<div class="alert alert-success small">{{ session('success') }}</div>@endif
            @if(session('warning'))<div class="alert alert-warning small">{{ session('warning') }}</div>@endif
            @if(session('error'))<div class="alert alert-danger small">{{ session('error') }}</div>@endif
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-medium">Email</label>
                    <div class="input-group"><span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus placeholder="nome@azienda.it"></div>
                    @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium">Password</label>
                    <div class="input-group"><span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required></div>
                    @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Ricordami</label>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 fw-medium"><i class="bi bi-box-arrow-in-right me-2"></i>Accedi</button>
            </form>
            <div class="text-center mt-4"><p class="text-muted mb-0">Non hai un account? <a href="{{ route('register') }}" class="text-primary fw-medium">Registrati</a></p></div>
        </div>
    </div>
</div>
@endsection
