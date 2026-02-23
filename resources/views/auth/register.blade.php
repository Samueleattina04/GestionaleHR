@extends('layouts.app')
@section('title', 'Registrazione - GestionaleHR')
@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center py-5" style="background:linear-gradient(135deg,#1e293b,#334155);">
    <div class="card shadow-lg" style="width:100%;max-width:540px;border-radius:16px;border:none;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white mb-3" style="width:60px;height:60px;"><i class="bi bi-person-plus-fill" style="font-size:1.5rem;"></i></div>
                <h3 class="fw-bold">Registrazione</h3>
                <p class="text-muted">Crea il tuo account - sara' approvato da un amministratore</p>
            </div>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label fw-medium">Nome *</label><input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6 mb-3"><label class="form-label fw-medium">Cognome *</label><input type="text" class="form-control @error('surname') is-invalid @enderror" name="surname" value="{{ old('surname') }}" required>@error('surname')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                </div>
                <div class="mb-3"><label class="form-label fw-medium">Email *</label><input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label fw-medium">Telefono</label><input type="text" class="form-control" name="phone" value="{{ old('phone') }}"></div>
                    <div class="col-md-6 mb-3"><label class="form-label fw-medium">Codice Fiscale</label><input type="text" class="form-control" name="fiscal_code" value="{{ old('fiscal_code') }}" maxlength="16" style="text-transform:uppercase;"></div>
                </div>
                <div class="mb-3"><label class="form-label fw-medium">Ruolo richiesto *</label>
                    <select class="form-select @error('role') is-invalid @enderror" name="role" required>
                        <option value="">Seleziona...</option>
                        <option value="employee" {{ old('role')=='employee'?'selected':'' }}>Dipendente / Operatore</option>
                        <option value="manager" {{ old('role')=='manager'?'selected':'' }}>Preposto / Responsabile</option>
                        <option value="hr" {{ old('role')=='hr'?'selected':'' }}>HR / Risorse Umane</option>
                    </select>@error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label fw-medium">Password *</label><input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required><div class="form-text">Min. 8 caratteri, maiuscole, minuscole e numeri</div>@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6 mb-3"><label class="form-label fw-medium">Conferma Password *</label><input type="password" class="form-control" name="password_confirmation" required></div>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 fw-medium"><i class="bi bi-person-plus me-2"></i>Registrati</button>
            </form>
            <div class="text-center mt-4"><p class="text-muted mb-0">Hai gia' un account? <a href="{{ route('login') }}" class="text-primary fw-medium">Accedi</a></p></div>
        </div>
    </div>
</div>
@endsection
