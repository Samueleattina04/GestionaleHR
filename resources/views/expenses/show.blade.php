@extends('layouts.app')
@section('title', 'Nota Spese - GestionaleHR')
@section('page-title', 'Dettaglio Nota Spese')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Nota Spese #{{ $expense->id }}</h5>
    <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Indietro</a>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Dettagli</div>
            <div class="card-body">
                @php
                    $sc=['pending'=>'warning','approved'=>'success','rejected'=>'danger','reimbursed'=>'info'];
                    $sl=['pending'=>'In attesa','approved'=>'Approvata','rejected'=>'Rifiutata','reimbursed'=>'Rimborsata'];
                @endphp
                <div class="text-center mb-4">
                    <div class="display-5 fw-bold text-primary mb-1">€ {{ number_format($expense->amount, 2, ',', '.') }}</div>
                    <span class="badge bg-{{ $sc[$expense->status] ?? 'secondary' }} fs-6 py-2 px-3">{{ $sl[$expense->status] ?? $expense->status }}</span>
                </div>
                <dl class="row">
                    <dt class="col-5 text-muted">Dipendente</dt><dd class="col-7">{{ $expense->user->full_name }}</dd>
                    <dt class="col-5 text-muted">Titolo</dt><dd class="col-7">{{ $expense->title }}</dd>
                    <dt class="col-5 text-muted">Categoria</dt><dd class="col-7"><span class="badge bg-secondary">{{ $expense->category }}</span></dd>
                    <dt class="col-5 text-muted">Data Spesa</dt><dd class="col-7">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y') }}</dd>
                    <dt class="col-5 text-muted">Inviata il</dt><dd class="col-7">{{ $expense->created_at->format('d/m/Y H:i') }}</dd>
                    @if($expense->description)
                    <dt class="col-5 text-muted">Descrizione</dt><dd class="col-7">{{ $expense->description }}</dd>
                    @endif
                </dl>
                @if($expense->receipt_path)
                <div class="mt-3">
                    <a href="{{ asset('storage/'.$expense->receipt_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-receipt me-2"></i>Visualizza Scontrino
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    @if($expense->status !== 'pending')
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Esito</div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-5 text-muted">Revisore</dt><dd class="col-7">{{ $expense->reviewer->full_name ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Data</dt><dd class="col-7">{{ $expense->reviewed_at ? \Carbon\Carbon::parse($expense->reviewed_at)->format('d/m/Y H:i') : '—' }}</dd>
                    @if($expense->review_notes)
                    <dt class="col-5 text-muted">Note</dt><dd class="col-7">{{ $expense->review_notes }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>
    @endif
</div>

@if(in_array(auth()->user()->role, ['admin','hr','manager']) && $expense->status === 'pending')
<div class="mt-3 d-flex gap-2">
    <form action="{{ route('expenses.approve', $expense) }}" method="POST">
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
            <form action="{{ route('expenses.reject', $expense) }}" method="POST">
                @csrf
                <div class="modal-header"><h6 class="modal-title">Motivo Rifiuto</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body"><textarea name="review_notes" class="form-control" rows="3"></textarea></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-danger btn-sm">Rifiuta</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if(in_array(auth()->user()->role, ['admin','hr']) && $expense->status === 'approved')
<div class="mt-3">
    <form action="{{ route('expenses.reimburse', $expense) }}" method="POST">
        @csrf
        <button class="btn btn-info text-white"><i class="bi bi-wallet2 me-2"></i>Segna come Rimborsata</button>
    </form>
</div>
@endif
@endsection
