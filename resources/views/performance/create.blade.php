@extends('layouts.app')
@section('title', 'Nuova Valutazione - GestionaleHR')
@section('page-title', 'Nuova Valutazione')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Nuova Valutazione Performance</h5>
    <a href="{{ route('performance.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Indietro</a>
</div>

<form action="{{ route('performance.store') }}" method="POST">
    @csrf
    <div class="row g-3">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">Informazioni Generali</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Dipendente *</label>
                            <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                <option value="">Seleziona...</option>
                                @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ old('user_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                                @endforeach
                            </select>
                            @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Anno *</label>
                            <input type="number" name="year" class="form-control" value="{{ old('year', date('Y')) }}" min="2020" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Periodo *</label>
                            <select name="period" class="form-select" required>
                                <option value="Annuale" {{ old('period')=='Annuale'?'selected':'' }}>Annuale</option>
                                <option value="Semestrale H1" {{ old('period')=='Semestrale H1'?'selected':'' }}>Semestrale H1</option>
                                <option value="Semestrale H2" {{ old('period')=='Semestrale H2'?'selected':'' }}>Semestrale H2</option>
                                <option value="Trimestrale Q1" {{ old('period')=='Trimestrale Q1'?'selected':'' }}>Trimestrale Q1</option>
                                <option value="Trimestrale Q2" {{ old('period')=='Trimestrale Q2'?'selected':'' }}>Trimestrale Q2</option>
                                <option value="Trimestrale Q3" {{ old('period')=='Trimestrale Q3'?'selected':'' }}>Trimestrale Q3</option>
                                <option value="Trimestrale Q4" {{ old('period')=='Trimestrale Q4'?'selected':'' }}>Trimestrale Q4</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">Punteggi (da 1 a 10)</div>
                <div class="card-body">
                    @foreach([
                        'productivity_score' => 'Produttività',
                        'quality_score' => 'Qualità del Lavoro',
                        'teamwork_score' => 'Lavoro di Squadra',
                        'initiative_score' => 'Iniziativa e Proattività',
                        'attendance_score' => 'Presenze e Puntualità',
                    ] as $field => $label)
                    <div class="mb-3">
                        <label class="form-label fw-medium d-flex justify-content-between">
                            <span>{{ $label }}</span>
                            <span class="badge bg-primary" id="{{ $field }}_display">{{ old($field, 5) }}</span>
                        </label>
                        <input type="range" name="{{ $field }}" class="form-range" min="1" max="10" value="{{ old($field, 5) }}" id="{{ $field }}"
                            oninput="document.getElementById('{{ $field }}_display').textContent=this.value">
                        <div class="d-flex justify-content-between small text-muted"><span>1 - Insufficiente</span><span>10 - Eccellente</span></div>
                        @error($field)<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="card">
                <div class="card-header">Note e Commenti</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Punti di Forza</label>
                        <textarea name="strengths" class="form-control" rows="3" placeholder="Descrivere i principali punti di forza...">{{ old('strengths') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Aree di Miglioramento</label>
                        <textarea name="improvements" class="form-control" rows="3" placeholder="Indicare le aree su cui lavorare...">{{ old('improvements') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Obiettivi per il Prossimo Periodo</label>
                        <textarea name="goals" class="form-control" rows="3" placeholder="Obiettivi concordati...">{{ old('goals') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Commento Finale</label>
                        <textarea name="comments" class="form-control" rows="3">{{ old('comments') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Punteggio Complessivo</div>
                <div class="card-body text-center">
                    <div class="display-4 fw-bold text-primary" id="overall_score_display">5.0</div>
                    <div class="text-muted mb-3">su 10</div>
                    <div class="progress mb-2" style="height:10px;">
                        <div class="progress-bar bg-primary" id="overall_bar" style="width:50%"></div>
                    </div>
                    <div class="text-muted small" id="score_label">Sufficiente</div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-2"></i>Salva Valutazione</button>
        <a href="{{ route('performance.index') }}" class="btn btn-outline-secondary">Annulla</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
var fields = ['productivity_score','quality_score','teamwork_score','initiative_score','attendance_score'];
var labels = ['Insufficiente','Scarso','Sufficiente','Discreto','Buono','Buono','Ottimo','Ottimo','Eccellente','Eccellente'];
function updateOverall(){
    var sum = 0;
    fields.forEach(function(f){ sum += parseInt(document.getElementById(f).value)||0; });
    var avg = sum/fields.length;
    document.getElementById('overall_score_display').textContent = avg.toFixed(1);
    document.getElementById('overall_bar').style.width = (avg*10)+'%';
    document.getElementById('overall_bar').className = 'progress-bar ' + (avg>=7?'bg-success':(avg>=5?'bg-warning':'bg-danger'));
    document.getElementById('score_label').textContent = labels[Math.round(avg)-1] || '';
}
fields.forEach(function(f){ document.getElementById(f).addEventListener('input', updateOverall); });
updateOverall();
</script>
@endpush
