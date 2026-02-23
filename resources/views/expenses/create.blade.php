@extends('layouts.app')
@section('title', 'Nuova Nota Spese - GestionaleHR')
@section('page-title', 'Nuova Nota Spese')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Nuova Nota Spese</h5>
    <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Indietro</a>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-body">
        <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-medium">Titolo *</label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required placeholder="Es. Pranzo di lavoro con cliente">
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Categoria *</label>
                    <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                        <option value="">Seleziona...</option>
                        <option value="Vitto" {{ old('category')=='Vitto'?'selected':'' }}>Vitto</option>
                        <option value="Alloggio" {{ old('category')=='Alloggio'?'selected':'' }}>Alloggio</option>
                        <option value="Trasporto" {{ old('category')=='Trasporto'?'selected':'' }}>Trasporto</option>
                        <option value="Carburante" {{ old('category')=='Carburante'?'selected':'' }}>Carburante</option>
                        <option value="Telefonia" {{ old('category')=='Telefonia'?'selected':'' }}>Telefonia</option>
                        <option value="Cancelleria" {{ old('category')=='Cancelleria'?'selected':'' }}>Cancelleria</option>
                        <option value="Formazione" {{ old('category')=='Formazione'?'selected':'' }}>Formazione</option>
                        <option value="Rappresentanza" {{ old('category')=='Rappresentanza'?'selected':'' }}>Rappresentanza</option>
                        <option value="Altro" {{ old('category')=='Altro'?'selected':'' }}>Altro</option>
                    </select>
                    @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Importo (€) *</label>
                    <div class="input-group">
                        <span class="input-group-text">€</span>
                        <input type="number" name="amount" step="0.01" min="0.01" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required>
                        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Data Spesa *</label>
                <input type="date" name="expense_date" class="form-control @error('expense_date') is-invalid @enderror" value="{{ old('expense_date', date('Y-m-d')) }}" required>
                @error('expense_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Descrizione</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Dettagli aggiuntivi...">{{ old('description') }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Scontrino / Ricevuta</label>
                <input type="file" name="receipt" class="form-control" accept="image/*,.pdf">
                <div class="form-text">Immagine o PDF. Max 10MB</div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-send me-2"></i>Invia Richiesta</button>
                <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection
