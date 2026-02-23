@extends('layouts.app')
@section('title', 'Nuovo Dipartimento - GestionaleHR')
@section('page-title', 'Nuovo Dipartimento')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Crea Nuovo Dipartimento</h5>
    <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Indietro</a>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-body">
        <form action="{{ route('departments.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-medium">Nome Dipartimento *</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Codice *</label>
                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" maxlength="10" style="text-transform:uppercase;" required>
                <div class="form-text">Codice breve univoco (es. IT, HR, MKT)</div>
                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Descrizione</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Manager / Responsabile</label>
                <select name="manager_id" class="form-select">
                    <option value="">Nessun responsabile</option>
                    @foreach($managers as $mgr)
                    <option value="{{ $mgr->id }}" {{ old('manager_id') == $mgr->id ? 'selected' : '' }}>
                        {{ $mgr->full_name }} ({{ ucfirst($mgr->role) }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-2"></i>Crea Dipartimento</button>
                <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection
