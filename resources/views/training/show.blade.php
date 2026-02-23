@extends('layouts.app')
@section('title', 'Corso - GestionaleHR')
@section('page-title', 'Dettaglio Corso')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">{{ $training->title }}</h5>
    <div class="d-flex gap-2">
        @if(in_array(auth()->user()->role, ['admin','hr']))
        <a href="{{ route('training.edit', $training) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-2"></i>Modifica</a>
        @endif
        <a href="{{ route('training.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Indietro</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">Informazioni</div>
            <div class="list-group list-group-flush small">
                <div class="list-group-item d-flex justify-content-between"><span class="text-muted">Stato</span><span class="badge bg-{{ $training->is_active ? 'success' : 'secondary' }}">{{ $training->is_active ? 'Attivo' : 'Inattivo' }}</span></div>
                @if($training->is_mandatory)<div class="list-group-item d-flex justify-content-between"><span class="text-muted">Tipo</span><span class="badge bg-danger">Obbligatorio</span></div>@endif
                @if($training->provider)<div class="list-group-item d-flex justify-content-between"><span class="text-muted">Erogatore</span><span>{{ $training->provider }}</span></div>@endif
                <div class="list-group-item d-flex justify-content-between"><span class="text-muted">Inizio</span><span>{{ \Carbon\Carbon::parse($training->start_date)->format('d/m/Y') }}</span></div>
                @if($training->end_date)<div class="list-group-item d-flex justify-content-between"><span class="text-muted">Fine</span><span>{{ \Carbon\Carbon::parse($training->end_date)->format('d/m/Y') }}</span></div>@endif
                <div class="list-group-item d-flex justify-content-between"><span class="text-muted">Iscritti</span><span class="fw-bold">{{ $training->participants->count() }}{{ $training->max_participants ? '/'.$training->max_participants : '' }}</span></div>
            </div>
            @if($training->description)
            <div class="card-body">
                <p class="text-muted small mb-0">{{ $training->description }}</p>
            </div>
            @endif
        </div>

        {{-- Self enroll / Enroll --}}
        @php $isEnrolled = $training->participants->where('user_id', auth()->id())->first(); @endphp
        @if(!$isEnrolled && $training->is_active)
        <div class="card mb-3">
            <div class="card-body">
                @if(in_array(auth()->user()->role, ['admin','hr','manager']))
                <form action="{{ route('training.enroll', $training) }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <select name="user_id" class="form-select form-select-sm" required>
                            <option value="">Iscrivi dipendente...</option>
                            @foreach($availableEmployees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-person-plus me-1"></i>Iscrivi</button>
                </form>
                @else
                <form action="{{ route('training.selfEnroll', $training) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-mortarboard me-2"></i>Iscriviti al corso</button>
                </form>
                @endif
            </div>
        </div>
        @elseif($isEnrolled)
        <div class="card mb-3">
            <div class="card-body text-center">
                @php $sc=['enrolled'=>'info','completed'=>'success','failed'=>'danger','cancelled'=>'secondary']; $sl=['enrolled'=>'Iscritto','completed'=>'Completato','failed'=>'Non Superato','cancelled'=>'Cancellato']; @endphp
                <span class="badge bg-{{ $sc[$isEnrolled->status] ?? 'info' }} fs-6 py-2 px-3">{{ $sl[$isEnrolled->status] ?? $isEnrolled->status }}</span>
                @if($isEnrolled->score)<div class="mt-2 text-muted small">Punteggio: <strong>{{ $isEnrolled->score }}/100</strong></div>@endif
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-people me-2"></i>Partecipanti ({{ $training->participants->count() }})</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead><tr><th>Dipendente</th><th>Stato</th><th>Punteggio</th><th>Completato il</th>
                        @if(in_array(auth()->user()->role, ['admin','hr']))<th>Aggiorna</th>@endif
                        </tr></thead>
                        <tbody>
                            @forelse($training->participants as $participant)
                            <tr>
                                <td>{{ $participant->user->full_name }}</td>
                                <td>
                                    @php $sc=['enrolled'=>'info','completed'=>'success','failed'=>'danger','cancelled'=>'secondary']; $sl=['enrolled'=>'Iscritto','completed'=>'Completato','failed'=>'Non Superato','cancelled'=>'Cancellato']; @endphp
                                    <span class="badge bg-{{ $sc[$participant->status] ?? 'secondary' }}">{{ $sl[$participant->status] ?? $participant->status }}</span>
                                </td>
                                <td>{{ $participant->score ? $participant->score.'/100' : '—' }}</td>
                                <td class="text-muted small">{{ $participant->completion_date ? \Carbon\Carbon::parse($participant->completion_date)->format('d/m/Y') : '—' }}</td>
                                @if(in_array(auth()->user()->role, ['admin','hr']))
                                <td>
                                    <form action="{{ route('training.updateParticipant', $participant) }}" method="POST" class="d-flex gap-1">
                                        @csrf @method('PUT')
                                        <select name="status" class="form-select form-select-sm" style="width:120px;">
                                            <option value="enrolled" {{ $participant->status=='enrolled'?'selected':'' }}>Iscritto</option>
                                            <option value="completed" {{ $participant->status=='completed'?'selected':'' }}>Completato</option>
                                            <option value="failed" {{ $participant->status=='failed'?'selected':'' }}>Non Superato</option>
                                            <option value="cancelled" {{ $participant->status=='cancelled'?'selected':'' }}>Cancellato</option>
                                        </select>
                                        <input type="number" name="score" class="form-control form-control-sm" style="width:70px;" placeholder="Voto" value="{{ $participant->score }}" min="0" max="100">
                                        <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-check"></i></button>
                                    </form>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">Nessun partecipante iscritto.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
