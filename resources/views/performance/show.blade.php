@extends('layouts.app')
@section('title', 'Valutazione - GestionaleHR')
@section('page-title', 'Dettaglio Valutazione')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Valutazione Performance</h5>
    <div class="d-flex gap-2">
        @if(in_array(auth()->user()->role, ['admin','hr','manager']) && $performance->status !== 'acknowledged')
        <a href="{{ route('performance.edit', $performance) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-2"></i>Modifica</a>
        @if($performance->status === 'draft')
        <form action="{{ route('performance.submit', $performance) }}" method="POST">
            @csrf
            <button class="btn btn-info text-white"><i class="bi bi-send me-2"></i>Invia al Dipendente</button>
        </form>
        @endif
        @endif
        @if($performance->user_id === auth()->id() && $performance->status === 'submitted')
        <form action="{{ route('performance.acknowledge', $performance) }}" method="POST">
            @csrf
            <button class="btn btn-success"><i class="bi bi-check-circle me-2"></i>Confermo di aver preso visione</button>
        </form>
        @endif
        <a href="{{ route('performance.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Indietro</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-body text-center">
                @php
                    $score = $performance->overall_score;
                    $color = $score >= 7 ? 'success' : ($score >= 5 ? 'warning' : 'danger');
                @endphp
                <div class="display-3 fw-bold text-{{ $color }}">{{ $score }}</div>
                <div class="text-muted mb-3">Punteggio Complessivo / 10</div>
                <div class="progress mb-3" style="height:10px;">
                    <div class="progress-bar bg-{{ $color }}" style="width:{{ $score*10 }}%"></div>
                </div>
                @php $sc=['draft'=>'secondary','submitted'=>'info','acknowledged'=>'success']; $sl=['draft'=>'Bozza','submitted'=>'Inviata','acknowledged'=>'Confermata'];@endphp
                <span class="badge bg-{{ $sc[$performance->status] ?? 'secondary' }} fs-6 py-2 px-3">
                    {{ $sl[$performance->status] ?? $performance->status }}
                </span>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Informazioni</div>
            <div class="list-group list-group-flush small">
                <div class="list-group-item d-flex justify-content-between"><span class="text-muted">Dipendente</span><span class="fw-medium">{{ $performance->user->full_name }}</span></div>
                <div class="list-group-item d-flex justify-content-between"><span class="text-muted">Anno</span><span>{{ $performance->year }}</span></div>
                <div class="list-group-item d-flex justify-content-between"><span class="text-muted">Periodo</span><span>{{ $performance->period }}</span></div>
                <div class="list-group-item d-flex justify-content-between"><span class="text-muted">Valutatore</span><span>{{ $performance->reviewer->full_name ?? '—' }}</span></div>
                <div class="list-group-item d-flex justify-content-between"><span class="text-muted">Creata il</span><span>{{ $performance->created_at->format('d/m/Y') }}</span></div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">Punteggi Dettagliati</div>
            <div class="card-body">
                @foreach([
                    'productivity_score' => 'Produttività',
                    'quality_score' => 'Qualità del Lavoro',
                    'teamwork_score' => 'Lavoro di Squadra',
                    'initiative_score' => 'Iniziativa e Proattività',
                    'attendance_score' => 'Presenze e Puntualità',
                ] as $field => $label)
                @php $s = $performance->$field; $c = $s>=7?'success':($s>=5?'warning':'danger'); @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-medium">{{ $label }}</span>
                        <span class="badge bg-{{ $c }}">{{ $s }}/10</span>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar bg-{{ $c }}" style="width:{{ $s*10 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        @if($performance->strengths || $performance->improvements || $performance->goals || $performance->comments)
        <div class="card">
            <div class="card-header">Note e Commenti</div>
            <div class="card-body">
                @if($performance->strengths)
                <h6 class="fw-semibold text-success"><i class="bi bi-plus-circle me-2"></i>Punti di Forza</h6>
                <p class="mb-3">{{ $performance->strengths }}</p>
                @endif
                @if($performance->improvements)
                <h6 class="fw-semibold text-warning"><i class="bi bi-arrow-up-circle me-2"></i>Aree di Miglioramento</h6>
                <p class="mb-3">{{ $performance->improvements }}</p>
                @endif
                @if($performance->goals)
                <h6 class="fw-semibold text-info"><i class="bi bi-target me-2"></i>Obiettivi</h6>
                <p class="mb-3">{{ $performance->goals }}</p>
                @endif
                @if($performance->comments)
                <h6 class="fw-semibold"><i class="bi bi-chat me-2"></i>Commento Finale</h6>
                <p class="mb-0">{{ $performance->comments }}</p>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
