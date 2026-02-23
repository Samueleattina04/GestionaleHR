@extends('layouts.app')
@section('title', 'Nuovo Dipendente - GestionaleHR')
@section('page-title', 'Nuovo Dipendente')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Crea Nuovo Dipendente</h5>
    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Indietro</a>
</div>

<form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">Dati Anagrafici</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Nome *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Cognome *</label>
                            <input type="text" name="surname" class="form-control @error('surname') is-invalid @enderror" value="{{ old('surname') }}" required>
                            @error('surname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Email *</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Telefono</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Codice Fiscale</label>
                            <input type="text" name="fiscal_code" class="form-control @error('fiscal_code') is-invalid @enderror" value="{{ old('fiscal_code') }}" maxlength="16" style="text-transform:uppercase;">
                            @error('fiscal_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Data di Nascita</label>
                            <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Luogo di Nascita</label>
                            <input type="text" name="birth_place" class="form-control" value="{{ old('birth_place') }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Indirizzo</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-medium">Indirizzo</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address') }}">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-medium">Città</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Provincia</label>
                            <input type="text" name="province" class="form-control" value="{{ old('province') }}" maxlength="2" style="text-transform:uppercase;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">CAP</label>
                            <input type="text" name="zip_code" class="form-control" value="{{ old('zip_code') }}" maxlength="5">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Dati Lavorativi</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Ruolo *</label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="">Seleziona...</option>
                                <option value="employee" {{ old('role')=='employee'?'selected':'' }}>Dipendente</option>
                                <option value="manager" {{ old('role')=='manager'?'selected':'' }}>Manager</option>
                                <option value="hr" {{ old('role')=='hr'?'selected':'' }}>HR</option>
                                <option value="admin" {{ old('role')=='admin'?'selected':'' }}>Admin</option>
                            </select>
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Dipartimento</label>
                            <select name="department_id" class="form-select">
                                <option value="">Nessun dipartimento</option>
                                @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Mansione / Job Title</label>
                            <input type="text" name="job_title" class="form-control" value="{{ old('job_title') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Tipo Contratto</label>
                            <select name="contract_type" class="form-select">
                                <option value="">Seleziona...</option>
                                <option value="indeterminato">Tempo Indeterminato</option>
                                <option value="determinato">Tempo Determinato</option>
                                <option value="apprendistato">Apprendistato</option>
                                <option value="parttime">Part-time</option>
                                <option value="consulenza">Consulenza</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Data Assunzione</label>
                            <input type="date" name="hire_date" class="form-control" value="{{ old('hire_date') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Scadenza Contratto</label>
                            <input type="date" name="contract_end_date" class="form-control" value="{{ old('contract_end_date') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Stipendio Lordo (€)</label>
                            <input type="number" name="salary" step="0.01" class="form-control" value="{{ old('salary') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">IBAN</label>
                            <input type="text" name="iban" class="form-control" value="{{ old('iban') }}" maxlength="34">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">Credenziali Accesso</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Password *</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        <div class="form-text">Min. 8 caratteri</div>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Conferma Password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">Foto Profilo</div>
                <div class="card-body">
                    <input type="file" name="avatar" class="form-control" accept="image/*">
                    <div class="form-text">Formati: JPG, PNG. Max 2MB</div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-2"></i>Crea Dipendente</button>
        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">Annulla</a>
    </div>
</form>
@endsection
