@extends('layouts.app')
@section('title', 'Profilo - GestionaleHR')
@section('page-title', 'Il Mio Profilo')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Il Mio Profilo</h5>
    <a href="{{ route('profile.edit') }}" class="btn btn-primary"><i class="bi bi-pencil me-2"></i>Modifica Profilo</a>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-body text-center">
                @if($user->avatar)
                <img src="{{ asset('storage/'.$user->avatar) }}" class="rounded-circle mb-3" style="width:100px;height:100px;object-fit:cover;">
                @else
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width:100px;height:100px;font-size:2rem;">
                    {{ strtoupper(substr($user->name,0,1)) }}{{ strtoupper(substr($user->surname,0,1)) }}
                </div>
                @endif
                <h5 class="fw-bold mb-1">{{ $user->full_name }}</h5>
                <p class="text-muted mb-2">{{ $user->job_title ?? ucfirst($user->role) }}</p>
                @php $roleColors=['admin'=>'danger','hr'=>'info','manager'=>'warning','employee'=>'secondary']; @endphp
                <span class="badge bg-{{ $roleColors[$user->role] ?? 'secondary' }}">{{ ucfirst($user->role) }}</span>
            </div>
            <div class="list-group list-group-flush small">
                <div class="list-group-item d-flex justify-content-between"><span class="text-muted"><i class="bi bi-envelope me-2"></i>Email</span><span>{{ $user->email }}</span></div>
                @if($user->phone)<div class="list-group-item d-flex justify-content-between"><span class="text-muted"><i class="bi bi-telephone me-2"></i>Telefono</span><span>{{ $user->phone }}</span></div>@endif
                @if($user->department)<div class="list-group-item d-flex justify-content-between"><span class="text-muted"><i class="bi bi-building me-2"></i>Dipartimento</span><span>{{ $user->department->name }}</span></div>@endif
                @if($user->hire_date)<div class="list-group-item d-flex justify-content-between"><span class="text-muted"><i class="bi bi-calendar me-2"></i>Assunto il</span><span>{{ \Carbon\Carbon::parse($user->hire_date)->format('d/m/Y') }}</span></div>@endif
                @if($user->contract_type)<div class="list-group-item d-flex justify-content-between"><span class="text-muted"><i class="bi bi-file-text me-2"></i>Contratto</span><span>{{ ucfirst($user->contract_type) }}</span></div>@endif
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">Dati Anagrafici</div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-md-4 text-muted">Nome Completo</dt><dd class="col-md-8">{{ $user->full_name }}</dd>
                    @if($user->fiscal_code)<dt class="col-md-4 text-muted">Cod. Fiscale</dt><dd class="col-md-8">{{ $user->fiscal_code }}</dd>@endif
                    @if($user->birth_date)<dt class="col-md-4 text-muted">Data di Nascita</dt><dd class="col-md-8">{{ \Carbon\Carbon::parse($user->birth_date)->format('d/m/Y') }}</dd>@endif
                    @if($user->birth_place)<dt class="col-md-4 text-muted">Luogo di Nascita</dt><dd class="col-md-8">{{ $user->birth_place }}</dd>@endif
                    @if($user->address)<dt class="col-md-4 text-muted">Indirizzo</dt><dd class="col-md-8">{{ $user->address }}, {{ $user->city }} ({{ $user->province }}) {{ $user->zip_code }}</dd>@endif
                </dl>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Sicurezza</span>
                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="collapse" data-bs-target="#pwdForm">
                    <i class="bi bi-shield-lock me-1"></i>Cambia Password
                </button>
            </div>
            <div class="collapse" id="pwdForm">
                <div class="card-body">
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Password Attuale</label>
                                <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Nuova Password</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Conferma Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-warning btn-sm"><i class="bi bi-check-lg me-2"></i>Aggiorna Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
