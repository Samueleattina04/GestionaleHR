@extends('layouts.app')
@section('title', 'Dipendente - GestionaleHR')
@section('page-title', 'Scheda Dipendente')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">{{ $employee->full_name }}</h5>
    <div class="d-flex gap-2">
        @if(in_array(auth()->user()->role, ['admin','hr']))
        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-2"></i>Modifica</a>
        @endif
        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Indietro</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-body text-center">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width:80px;height:80px;font-size:1.5rem;">
                    {{ strtoupper(substr($employee->name,0,1)) }}{{ strtoupper(substr($employee->surname,0,1)) }}
                </div>
                <h5 class="fw-bold mb-1">{{ $employee->full_name }}</h5>
                <p class="text-muted mb-2">{{ $employee->job_title ?? ucfirst($employee->role) }}</p>
                @php
                    $statusColors = ['active'=>'success','pending'=>'warning','suspended'=>'secondary','terminated'=>'danger'];
                    $statusLabels = ['active'=>'Attivo','pending'=>'In attesa','suspended'=>'Sospeso','terminated'=>'Terminato'];
                @endphp
                <span class="badge bg-{{ $statusColors[$employee->status] ?? 'secondary' }} fs-6">{{ $statusLabels[$employee->status] ?? $employee->status }}</span>
            </div>
            <div class="list-group list-group-flush small">
                <div class="list-group-item d-flex justify-content-between">
                    <span class="text-muted"><i class="bi bi-envelope me-2"></i>Email</span>
                    <span>{{ $employee->email }}</span>
                </div>
                @if($employee->phone)
                <div class="list-group-item d-flex justify-content-between">
                    <span class="text-muted"><i class="bi bi-telephone me-2"></i>Telefono</span>
                    <span>{{ $employee->phone }}</span>
                </div>
                @endif
                @if($employee->department)
                <div class="list-group-item d-flex justify-content-between">
                    <span class="text-muted"><i class="bi bi-building me-2"></i>Dipartimento</span>
                    <span>{{ $employee->department->name }}</span>
                </div>
                @endif
                @if($employee->hire_date)
                <div class="list-group-item d-flex justify-content-between">
                    <span class="text-muted"><i class="bi bi-calendar me-2"></i>Assunto il</span>
                    <span>{{ \Carbon\Carbon::parse($employee->hire_date)->format('d/m/Y') }}</span>
                </div>
                @endif
                @if($employee->contract_type)
                <div class="list-group-item d-flex justify-content-between">
                    <span class="text-muted"><i class="bi bi-file-text me-2"></i>Contratto</span>
                    <span>{{ ucfirst($employee->contract_type) }}</span>
                </div>
                @endif
                @if(in_array(auth()->user()->role, ['admin','hr']) && $employee->salary)
                <div class="list-group-item d-flex justify-content-between">
                    <span class="text-muted"><i class="bi bi-wallet2 me-2"></i>Stipendio</span>
                    <span>€ {{ number_format($employee->salary, 2, ',', '.') }}</span>
                </div>
                @endif
                @if($employee->fiscal_code)
                <div class="list-group-item d-flex justify-content-between">
                    <span class="text-muted"><i class="bi bi-person-badge me-2"></i>Cod. Fiscale</span>
                    <span>{{ $employee->fiscal_code }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8">
        {{-- Recent Attendances --}}
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock me-2"></i>Presenze Recenti</span>
                <a href="{{ route('attendances.index') }}?user_id={{ $employee->id }}" class="btn btn-sm btn-outline-primary">Vedi tutte</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead><tr><th>Data</th><th>Entrata</th><th>Uscita</th><th>Ore</th><th>Stato</th></tr></thead>
                        <tbody>
                            @forelse($employee->attendances->take(10) as $att)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($att->date)->format('d/m/Y') }}</td>
                                <td>{{ $att->clock_in ? substr($att->clock_in,0,5) : '—' }}</td>
                                <td>{{ $att->clock_out ? substr($att->clock_out,0,5) : '—' }}</td>
                                <td>{{ $att->hours_worked ?? '—' }}</td>
                                <td>
                                    @php $sc=['present'=>'success','absent'=>'danger','late'=>'warning','remote'=>'info','on_leave'=>'secondary'];
                                    $sl=['present'=>'Presente','absent'=>'Assente','late'=>'Ritardo','remote'=>'Smart Working','on_leave'=>'Permesso'];@endphp
                                    <span class="badge bg-{{ $sc[$att->status] ?? 'secondary' }}">{{ $sl[$att->status] ?? $att->status }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">Nessuna presenza registrata.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Leave Requests --}}
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar-event me-2"></i>Ferie & Permessi</span>
                <a href="{{ route('leaves.index') }}" class="btn btn-sm btn-outline-primary">Vedi tutte</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead><tr><th>Tipo</th><th>Dal</th><th>Al</th><th>Giorni</th><th>Stato</th></tr></thead>
                        <tbody>
                            @forelse($employee->leaveRequests->take(5) as $leave)
                            <tr>
                                <td>{{ $leave->leaveType->name ?? '—' }}</td>
                                <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('d/m/Y') }}</td>
                                <td>{{ $leave->total_days }}</td>
                                <td>
                                    @php $sc=['pending'=>'warning','approved'=>'success','rejected'=>'danger','cancelled'=>'secondary'];
                                    $sl=['pending'=>'In attesa','approved'=>'Approvato','rejected'=>'Rifiutato','cancelled'=>'Cancellato'];@endphp
                                    <span class="badge bg-{{ $sc[$leave->status] ?? 'secondary' }}">{{ $sl[$leave->status] ?? $leave->status }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">Nessuna richiesta.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Performance Reviews --}}
        <div class="card">
            <div class="card-header"><i class="bi bi-graph-up me-2"></i>Valutazioni Performance</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead><tr><th>Anno</th><th>Periodo</th><th>Punteggio</th><th>Stato</th></tr></thead>
                        <tbody>
                            @forelse($employee->performanceReviews as $rev)
                            <tr>
                                <td>{{ $rev->year }}</td>
                                <td>{{ $rev->period }}</td>
                                <td>
                                    <span class="fw-medium {{ $rev->overall_score >= 7 ? 'text-success' : ($rev->overall_score >= 5 ? 'text-warning' : 'text-danger') }}">
                                        {{ $rev->overall_score }}/10
                                    </span>
                                </td>
                                <td><span class="badge bg-secondary">{{ ucfirst($rev->status) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Nessuna valutazione.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
