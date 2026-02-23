@extends('layouts.app')
@section('title', 'Modifica Valutazione - GestionaleHR')
@section('page-title', 'Modifica Valutazione')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Modifica Valutazione</h5>
    <a href="{{ route('performance.show', $performance) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Indietro</a>
</div>

<form action="{{ route('performance.update', $performance) }}" method="POST">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">Dipendente: {{ $performance->user->full_name }} — {{ $performance->period }} {{ $performance->year }}</div>
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
                            <span class="badge bg-primary" id="{{ $field }}_display">{{ old($field, $performance->$field) }}</span>
                        </label>
                        <input type="range" name="{{ $field }}" class="form-range" min="1" max="10" value="{{ old($field, $performance->$field) }}" id="{{ $field }}"
                            oninput="document.getElementById('{{ $field }}_display').textContent=this.value;updateOverall();">
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="card">
                <div class="card-header">Note e Commenti</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Punti di Forza</label>
                        <textarea name="strengths" class="form-control" rows="3">{{ old('strengths', $performance->strengths) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Aree di Miglioramento</label>
                        <textarea name="improvements" class="form-control" rows="3">{{ old('improvements', $performance->improvements) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Obiettivi</label>
                        <textarea name="goals" class="form-control" rows="3">{{ old('goals', $performance->goals) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Commento Finale</label>
                        <textarea name="comments" class="form-control" rows="3">{{ old('comments', $performance->comments) }}</textarea>
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
                </div>
            </div>
        </div>
    </div>
    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-2"></i>Salva Modifiche</button>
        <a href="{{ route('performance.show', $performance) }}" class="btn btn-outline-secondary">Annulla</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
var fields = ['productivity_score','quality_score','teamwork_score','initiative_score','attendance_score'];
function updateOverall(){
    var sum=0; fields.forEach(function(f){ sum+=parseInt(document.getElementById(f).value)||0; });
    var avg=sum/fields.length;
    document.getElementById('overall_score_display').textContent=avg.toFixed(1);
    document.getElementById('overall_bar').style.width=(avg*10)+'%';
    document.getElementById('overall_bar').className='progress-bar '+(avg>=7?'bg-success':(avg>=5?'bg-warning':'bg-danger'));
}
fields.forEach(function(f){ document.getElementById(f).addEventListener('input', updateOverall); });
updateOverall();
</script>
@endpush
