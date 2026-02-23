@extends('layouts.app')
@section('title', 'Ferie & Permessi - GestionaleHR')
@section('page-title', 'Ferie & Permessi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Ferie & Permessi</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('leaves.calendar') }}" class="btn btn-outline-primary"><i class="bi bi-calendar3 me-2"></i>Calendario</a>
        <a href="{{ route('leaves.create') }}" class="btn btn-primary"><i class="bi bi-plus me-2"></i>Nuova Richiesta</a>
    </div>
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
                    <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Cancellate</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-search"></i></button>
            </div>
            @if(request()->filled('status'))
            <div class="col-md-1">
                <a href="{{ route('leaves.index') }}" class="btn btn-outline-secondary w-100"><i class="bi bi-x"></i></a>
            </div>
            @endif
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
                        <th>Tipo</th>
                        <th>Dal</th>
                        <th>Al</th>
                        <th>Giorni</th>
                        <th>Stato</th>
                        <th>Richiesta il</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaves as $leave)
                    <tr>
                        @if(!auth()->user()->isEmployee())
                        <td class="fw-medium">{{ $leave->user->full_name }}</td>
                        @endif
                        <td>{{ $leave->leaveType->name ?? '—' }}</td>
                        <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('d/m/Y') }}</td>
                        <td><span class="badge bg-secondary">{{ $leave->total_days }}gg</span></td>
                        <td>
                            @php
                                $sc=['pending'=>'warning','approved'=>'success','rejected'=>'danger','cancelled'=>'secondary'];
                                $sl=['pending'=>'In attesa','approved'=>'Approvato','rejected'=>'Rifiutato','cancelled'=>'Cancellato'];
                            @endphp
                            <span class="badge bg-{{ $sc[$leave->status] ?? 'secondary' }}">{{ $sl[$leave->status] ?? $leave->status }}</span>
                        </td>
                        <td class="text-muted small">{{ $leave->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('leaves.show', $leave) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                                @if(in_array(auth()->user()->role, ['admin','hr','manager']) && $leave->status === 'pending')
                                <form action="{{ route('leaves.approve', $leave) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-success" title="Approva"><i class="bi bi-check-lg"></i></button>
                                </form>
                                <button class="btn btn-sm btn-danger" title="Rifiuta" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $leave->id }}">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                                @endif
                                @if($leave->user_id === auth()->id() && $leave->status === 'pending')
                                <form action="{{ route('leaves.cancel', $leave) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-secondary" onclick="return confirm('Cancellare la richiesta?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    {{-- Reject Modal --}}
                    @if(in_array(auth()->user()->role, ['admin','hr','manager']))
                    <div class="modal fade" id="rejectModal{{ $leave->id }}" tabindex="-1">
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
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Nessuna richiesta trovata.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($leaves->hasPages())
    <div class="card-footer">{{ $leaves->links() }}</div>
    @endif
</div>
@endsection
