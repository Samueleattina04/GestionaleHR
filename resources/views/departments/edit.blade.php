@extends('layouts.app')
@section('title', 'Modifica Dipartimento - GestionaleHR')
@section('page-title', 'Modifica Dipartimento')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Modifica: {{ $department->name }}</h5>
    <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Indietro</a>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-body">
        <form action="{{ route('departments.update', $department) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-medium">Nome Dipartimento *</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $department->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Codice *</label>
                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $department->code) }}" maxlength="10" style="text-transform:uppercase;" required>
                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Descrizione</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $department->description) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Manager / Responsabile</label>
                <select name="manager_id" class="form-select">
                    <option value="">Nessun responsabile</option>
                    @foreach($managers as $mgr)
                    <option value="{{ $mgr->id }}" {{ old('manager_id', $department->manager_id) == $mgr->id ? 'selected' : '' }}>
                        {{ $mgr->full_name }} ({{ ucfirst($mgr->role) }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $department->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Dipartimento Attivo</label>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-2"></i>Salva Modifiche</button>
                <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection
