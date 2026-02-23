@extends('layouts.app')
@section('title', 'Modifica Corso - GestionaleHR')
@section('page-title', 'Modifica Corso')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Modifica: {{ $training->title }}</h5>
    <a href="{{ route('training.show', $training) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Indietro</a>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-body">
        <form action="{{ route('training.update', $training) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-medium">Titolo Corso *</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $training->title) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Ente Erogatore</label>
                <input type="text" name="provider" class="form-control" value="{{ old('provider', $training->provider) }}">
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Descrizione</label>
                <textarea name="description" class="form-control" rows="4">{{ old('description', $training->description) }}</textarea>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Data Inizio *</label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $training->start_date) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Data Fine</label>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $training->end_date) }}">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Max Partecipanti</label>
                <input type="number" name="max_participants" class="form-control" value="{{ old('max_participants', $training->max_participants) }}" min="1">
            </div>
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_mandatory" id="is_mandatory" value="1" {{ old('is_mandatory', $training->is_mandatory) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_mandatory">Corso Obbligatorio</label>
                </div>
            </div>
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $training->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Corso Attivo</label>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-2"></i>Salva Modifiche</button>
                <a href="{{ route('training.show', $training) }}" class="btn btn-outline-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection
