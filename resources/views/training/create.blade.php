@extends('layouts.app')
@section('title', 'Nuovo Corso - GestionaleHR')
@section('page-title', 'Nuovo Corso di Formazione')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Crea Nuovo Corso</h5>
    <a href="{{ route('training.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Indietro</a>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-body">
        <form action="{{ route('training.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-medium">Titolo Corso *</label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Ente Erogatore</label>
                <input type="text" name="provider" class="form-control" value="{{ old('provider') }}" placeholder="Es. Coursera, interna, consulente esterno...">
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Descrizione</label>
                <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Data Inizio *</label>
                    <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}" required>
                    @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Data Fine</label>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Numero Massimo Partecipanti</label>
                <input type="number" name="max_participants" class="form-control" value="{{ old('max_participants') }}" min="1" placeholder="Lascia vuoto per illimitato">
            </div>
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_mandatory" id="is_mandatory" value="1" {{ old('is_mandatory') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_mandatory">Corso Obbligatorio</label>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-2"></i>Crea Corso</button>
                <a href="{{ route('training.index') }}" class="btn btn-outline-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection
