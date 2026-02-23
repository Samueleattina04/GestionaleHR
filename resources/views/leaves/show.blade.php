@extends('layouts.app')
@section('title', 'Richiesta Ferie - GestionaleHR')
@section('page-title', 'Dettaglio Richiesta')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Richiesta #{{ $leave->id }}</h5>
    <a href="{{ route('leaves.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Indietro</a>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Dettagli Richiesta</div>
            <div class="card-body">
                @php
                    $sc=['pending'=>'warning','approved'=>'success','rejected'=>'danger','cancelled'=>'secondary'];
                    $sl=['pending'=>'In attesa','approved'=>'Approvato','rejected'=>'Rifiutato','cancelled'=>'Cancellato'];
                @endphp
                <div class="mb-3 text-center">
                    <span class="badge bg-{{ $sc[$leave->status] ?? 'secondary' }} fs-5 py-2 px-4">
                        {{ $sl[$leave->status] ?? $leave->status }}
                    </span>
                </div>
                <dl class="row">
                    <dt class="col-5 text-muted">Dipendente</dt>
                    <dd class="col-7">{{ $leave->user->full_name }}</dd>
                    <dt class="col-5 text-muted">Tipo</dt>
                    <dd class="col-7">{{ $leave->leaveType->name ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Dal</dt>
                    <dd class="col-7">{{ \Carbon\Carbon::parse($leave->start_date)->format('d/m/Y') }}</dd>
                    <dt class="col-5 text-muted">Al</dt>
                    <dd class="col-7">{{ \Carbon\Carbon::parse($leave->end_date)->format('d/m/Y') }}</dd>
                    <dt class="col-5 text-muted">Giorni</dt>
                    <dd class="col-7"><strong>{{ $leave->total_days }}</strong></dd>
                    <dt class="col-5 text-muted">Inviata il</dt>
                    <dd class="col-7">{{ $leave->created_at->format('d/m/Y H:i') }}</dd>
                    @if($leave->reason)
                    <dt class="col-5 text-muted">Motivazione</dt>
                    <dd class="col-7">{{ $leave->reason }}</dd>
                    @endif
                </dl>
                @if($leave->document_path)
                <div class="mt-3">
                    <a href="{{ asset('storage/'.$leave->document_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-paperclip me-2"></i>Documento allegato
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    @if($leave->status !== 'pending')
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Esito Revisione</div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-5 text-muted">Revisore</dt>
                    <dd class="col-7">{{ $leave->reviewer->full_name ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Data Revisione</dt>
                    <dd class="col-7">{{ $leave->reviewed_at ? \Carbon\Carbon::parse($leave->reviewed_at)->format('d/m/Y H:i') : '—' }}</dd>
                    @if($leave->review_notes)
                    <dt class="col-5 text-muted">Note</dt>
                    <dd class="col-7">{{ $leave->review_notes }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>
    @endif
</div>

@if(in_array(auth()->user()->role, ['admin','hr','manager']) && $leave->status === 'pending')
<div class="mt-3 d-flex gap-2">
    <form action="{{ route('leaves.approve', $leave) }}" method="POST">
        @csrf
        <button class="btn btn-success"><i class="bi bi-check-lg me-2"></i>Approva</button>
    </form>
    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
        <i class="bi bi-x-lg me-2"></i>Rifiuta
    </button>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form action="{{ route('leaves.reject', $leave) }}" method="POST">
                @csrf
                <div class="modal-header"><h6 class="modal-title">Motivo Rifiuto</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <textarea name="review_notes" class="form-control" rows="3" placeholder="Motivazione..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-danger btn-sm">Rifiuta</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
