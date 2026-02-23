@extends('layouts.app')
@section('title', 'Note Spese - GestionaleHR')
@section('page-title', 'Note Spese')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Note Spese</h5>
    <a href="{{ route('expenses.create') }}" class="btn btn-primary"><i class="bi bi-plus me-2"></i>Nuova Nota Spese</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Tutti gli stati</option>
                    <option value="pending" {{ request('status')=='pending'?'selected':'' }}>In attesa</option>
                    <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approvate</option>
                    <option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>Rifiutate</option>
                    <option value="reimbursed" {{ request('status')=='reimbursed'?'selected':'' }}>Rimborsate</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        @if(!auth()->user()->isEmployee())<th>Dipendente</th>@endif
                        <th>Titolo</th>
                        <th>Categoria</th>
                        <th>Data</th>
                        <th class="text-end">Importo</th>
                        <th>Stato</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                    <tr>
                        @if(!auth()->user()->isEmployee())
                        <td>{{ $expense->user->full_name }}</td>
                        @endif
                        <td class="fw-medium">{{ $expense->title }}</td>
                        <td><span class="badge bg-light text-dark">{{ $expense->category }}</span></td>
                        <td class="text-muted small">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y') }}</td>
                        <td class="text-end fw-bold">€ {{ number_format($expense->amount, 2, ',', '.') }}</td>
                        <td>
                            @php
                                $sc=['pending'=>'warning','approved'=>'success','rejected'=>'danger','reimbursed'=>'info'];
                                $sl=['pending'=>'In attesa','approved'=>'Approvata','rejected'=>'Rifiutata','reimbursed'=>'Rimborsata'];
                            @endphp
                            <span class="badge bg-{{ $sc[$expense->status] ?? 'secondary' }}">{{ $sl[$expense->status] ?? $expense->status }}</span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('expenses.show', $expense) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                                @if(in_array(auth()->user()->role, ['admin','hr','manager']) && $expense->status === 'pending')
                                <form action="{{ route('expenses.approve', $expense) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-success" title="Approva"><i class="bi bi-check-lg"></i></button>
                                </form>
                                @endif
                                @if(in_array(auth()->user()->role, ['admin','hr']) && $expense->status === 'approved')
                                <form action="{{ route('expenses.reimburse', $expense) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-info" title="Rimborsa"><i class="bi bi-wallet2"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Nessuna nota spese trovata.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($expenses->hasPages())
    <div class="card-footer">{{ $expenses->links() }}</div>
    @endif
</div>
@endsection
