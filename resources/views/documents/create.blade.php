@extends('layouts.app')
@section('title', 'Carica Documento - GestionaleHR')
@section('page-title', 'Carica Documento')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Carica Nuovo Documento</h5>
    <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Indietro</a>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-body">
        <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-medium">Dipendente *</label>
                <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                    <option value="">Seleziona dipendente...</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ old('user_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                    @endforeach
                </select>
                @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Titolo *</label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Tipo *</label>
                <select name="type" class="form-select" required>
                    <option value="">Seleziona tipo...</option>
                    @foreach(['contract'=>'Contratto','payslip'=>'Cedolino','certificate'=>'Certificato','id_document'=>'Documento Identità','tax_document'=>'Documento Fiscale','medical'=>'Medico','training'=>'Formazione','other'=>'Altro'] as $val=>$label)
                    <option value="{{ $val }}" {{ old('type')==$val?'selected':'' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">File *</label>
                <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" required>
                <div class="form-text">Max 20MB. Formati accettati: PDF, immagini, documenti Office</div>
                @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Descrizione</label>
                <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Data Scadenza</label>
                <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date') }}">
            </div>
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_private" id="is_private" value="1" {{ old('is_private') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_private">Documento privato (visibile solo all'HR)</label>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-2"></i>Carica</button>
                <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection
