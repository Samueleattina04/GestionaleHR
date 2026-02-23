@extends('layouts.app')
@section('title', 'Modifica Profilo - GestionaleHR')
@section('page-title', 'Modifica Profilo')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Modifica Profilo</h5>
    <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Indietro</a>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-body">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="mb-3 text-center">
                @if($user->avatar)
                <img src="{{ asset('storage/'.$user->avatar) }}" class="rounded-circle mb-2" style="width:80px;height:80px;object-fit:cover;">
                @else
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-2" style="width:80px;height:80px;font-size:1.5rem;">
                    {{ strtoupper(substr($user->name,0,1)) }}{{ strtoupper(substr($user->surname,0,1)) }}
                </div>
                @endif
                <div>
                    <input type="file" name="avatar" class="form-control form-control-sm" accept="image/*" style="max-width:300px;margin:auto;">
                    <div class="form-text">Foto profilo (JPG, PNG. Max 2MB)</div>
                </div>
            </div>
            <hr>
            <div class="mb-3">
                <label class="form-label fw-medium">Telefono</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Indirizzo</label>
                <input type="text" name="address" class="form-control" value="{{ old('address', $user->address) }}">
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-5">
                    <label class="form-label fw-medium">Città</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city', $user->city) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Provincia</label>
                    <input type="text" name="province" class="form-control" value="{{ old('province', $user->province) }}" maxlength="2" style="text-transform:uppercase;">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">CAP</label>
                    <input type="text" name="zip_code" class="form-control" value="{{ old('zip_code', $user->zip_code) }}" maxlength="5">
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-2"></i>Salva Modifiche</button>
                <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection
