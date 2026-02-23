@extends('layouts.app')
@section('title', 'Nuova Richiesta - GestionaleHR')
@section('page-title', 'Nuova Richiesta Ferie/Permesso')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Nuova Richiesta</h5>
    <a href="{{ route('leaves.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Indietro</a>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-body">
        <form action="{{ route('leaves.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-medium">Tipo di Congedo *</label>
                <select name="leave_type_id" class="form-select @error('leave_type_id') is-invalid @enderror" required>
                    <option value="">Seleziona tipo...</option>
                    @foreach($leaveTypes as $type)
                    <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }} (max {{ $type->max_days_per_year }}gg/anno)
                    </option>
                    @endforeach
                </select>
                @error('leave_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Data Inizio *</label>
                    <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}" required min="{{ date('Y-m-d') }}">
                    @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Data Fine *</label>
                    <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}" required min="{{ date('Y-m-d') }}">
                    @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Motivazione</label>
                <textarea name="reason" class="form-control" rows="3" placeholder="Motivo della richiesta (opzionale)">{{ old('reason') }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Documento allegato</label>
                <input type="file" name="document" class="form-control">
                <div class="form-text">Es. certificato medico. Max 10MB</div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-send me-2"></i>Invia Richiesta</button>
                <a href="{{ route('leaves.index') }}" class="btn btn-outline-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection
