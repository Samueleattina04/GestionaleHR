@extends('layouts.app')
@section('title', 'Dashboard - GestionaleHR')
@section('page-title', 'Dashboard')

@section('content')
{{-- Clock In/Out Widget --}}
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h6 class="fw-semibold mb-1">Presenza Oggi — <span id="current-time"></span></h6>
                    @if($data['todayAttendance'])
                        <span class="text-muted small">
                            Entrata: <strong>{{ $data['todayAttendance']->clock_in ? \Carbon\Carbon::parse($data['todayAttendance']->clock_in)->format('H:i') : '—' }}</strong>
                            @if($data['todayAttendance']->clock_out)
                                &nbsp;|&nbsp; Uscita: <strong>{{ \Carbon\Carbon::parse($data['todayAttendance']->clock_out)->format('H:i') }}</strong>
                                &nbsp;|&nbsp; Ore: <strong>{{ $data['todayAttendance']->hours_worked }}h</strong>
                            @endif
                        </span>
                    @else
                        <span class="text-muted small">Non hai ancora registrato la presenza oggi.</span>
                    @endif
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @if(!$data['todayAttendance'] || !$data['todayAttendance']->clock_in)
                        <form action="{{ route('attendance.clockIn') }}" method="POST">
                            @csrf
                            <button class="btn btn-success btn-sm"><i class="bi bi-box-arrow-in-right me-1"></i>Entrata</button>
                        </form>
                    @elseif(!$data['todayAttendance']->clock_out)
                        @if(!$data['todayAttendance']->break_start)
                            <form action="{{ route('attendance.breakStart') }}" method="POST">
                                @csrf
                                <button class="btn btn-warning btn-sm"><i class="bi bi-cup-hot me-1"></i>Pausa</button>
                            </form>
                        @elseif(!$data['todayAttendance']->break_end)
                            <form action="{{ route('attendance.breakEnd') }}" method="POST">
                                @csrf
                                <button class="btn btn-info btn-sm text-white"><i class="bi bi-play-circle me-1"></i>Fine Pausa</button>
                            </form>
                        @endif
                        <form action="{{ route('attendance.clockOut') }}" method="POST">
                            @csrf
                            <button class="btn btn-danger btn-sm"><i class="bi bi-box-arrow-right me-1"></i>Uscita</button>
                        </form>
                    @else
                        <span class="badge bg-success fs-6 py-2 px-3"><i class="bi bi-check-circle me-1"></i>Giornata completata</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Admin/HR Stats --}}
@if($user->isAdmin() || $user->isHr())
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-people-fill"></i></div>
                <span class="badge bg-primary bg-opacity-10 text-primary">Totale</span>
            </div>
            <div class="stat-value">{{ $data['totalEmployees'] ?? 0 }}</div>
            <div class="stat-label">Dipendenti Attivi</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-clock-fill"></i></div>
                <span class="badge bg-success bg-opacity-10 text-success">Oggi</span>
            </div>
            <div class="stat-value">{{ $data['presentToday'] ?? 0 }}</div>
            <div class="stat-label">Presenti Oggi</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-hourglass-split"></i></div>
                @if(($data['pendingApprovals'] ?? 0) > 0)
                    <a href="{{ route('employees.pending') }}" class="badge bg-warning bg-opacity-10 text-warning text-decoration-none">Revisiona</a>
                @endif
            </div>
            <div class="stat-value">{{ $data['pendingApprovals'] ?? 0 }}</div>
            <div class="stat-label">Approvazioni in attesa</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-calendar-event-fill"></i></div>
                @if(($data['pendingLeaveRequests'] ?? 0) > 0)
                    <a href="{{ route('leaves.index') }}?status=pending" class="badge bg-info bg-opacity-10 text-info text-decoration-none">Revisiona</a>
                @endif
            </div>
            <div class="stat-value">{{ $data['pendingLeaveRequests'] ?? 0 }}</div>
            <div class="stat-label">Permessi in attesa</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-receipt"></i></div>
                @if(($data['pendingExpenses'] ?? 0) > 0)
                    <a href="{{ route('expenses.index') }}?status=pending" class="badge bg-danger bg-opacity-10 text-danger text-decoration-none">Revisiona</a>
                @endif
            </div>
            <div class="stat-value">{{ $data['pendingExpenses'] ?? 0 }}</div>
            <div class="stat-label">Note Spese in attesa</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-wallet2"></i></div>
            </div>
            <div class="stat-value">€ {{ number_format($data['monthlyPayroll'] ?? 0, 0, ',', '.') }}</div>
            <div class="stat-label">Payroll Mese Corrente</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-building me-2"></i>Dipartimenti</span>
                <a href="{{ route('departments.index') }}" class="btn btn-sm btn-outline-primary">Vedi tutti</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Dipartimento</th><th class="text-center">Dipendenti</th></tr></thead>
                        <tbody>
                            @forelse($data['departmentStats'] ?? [] as $dept)
                            <tr>
                                <td>{{ $dept->name }}</td>
                                <td class="text-center"><span class="badge bg-primary">{{ $dept->active_employees_count }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center text-muted py-3">Nessun dipartimento</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-person-plus me-2"></i>Ultime Assunzioni</span>
                <a href="{{ route('employees.index') }}" class="btn btn-sm btn-outline-primary">Vedi tutti</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Dipendente</th><th>Ruolo</th><th>Data</th></tr></thead>
                        <tbody>
                            @forelse($data['recentHires'] ?? [] as $hire)
                            <tr>
                                <td><a href="{{ route('employees.show', $hire) }}" class="text-decoration-none">{{ $hire->full_name }}</a></td>
                                <td><span class="badge bg-secondary">{{ ucfirst($hire->role) }}</span></td>
                                <td class="text-muted small">{{ $hire->hire_date ? \Carbon\Carbon::parse($hire->hire_date)->format('d/m/Y') : '—' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">Nessun dipendente</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Manager Stats --}}
@if($user->isManager())
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-primary bg-opacity-10 text-primary mb-2"><i class="bi bi-people-fill"></i></div>
            <div class="stat-value">{{ $data['departmentEmployees'] ?? 0 }}</div>
            <div class="stat-label">Dipendenti nel Team</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-success bg-opacity-10 text-success mb-2"><i class="bi bi-clock-fill"></i></div>
            <div class="stat-value">{{ $data['departmentPresent'] ?? 0 }}</div>
            <div class="stat-label">Presenti Oggi</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-warning bg-opacity-10 text-warning mb-2"><i class="bi bi-calendar-event"></i></div>
            <div class="stat-value">{{ $data['departmentLeaveRequests'] ?? 0 }}</div>
            <div class="stat-label">Permessi in Attesa</div>
        </div>
    </div>
</div>
@endif

{{-- Personal Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-primary bg-opacity-10 text-primary mb-2"><i class="bi bi-calendar-check"></i></div>
            <div class="stat-value">{{ $data['myAttendanceThisMonth'] }}</div>
            <div class="stat-label">Presenze questo mese</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-warning bg-opacity-10 text-warning mb-2"><i class="bi bi-hourglass"></i></div>
            <div class="stat-value">{{ $data['pendingLeaves'] }}</div>
            <div class="stat-label">Richieste Ferie/Permessi in attesa</div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Leave Balance --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar2-heart me-2"></i>Saldo Ferie & Permessi</span>
                <a href="{{ route('leaves.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus me-1"></i>Richiedi</a>
            </div>
            <div class="card-body">
                @forelse($data['myLeaveBalance'] as $balance)
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-medium">{{ $balance['type'] }}</span>
                        <span class="small text-muted">{{ $balance['used'] }}/{{ $balance['total'] }} giorni</span>
                    </div>
                    <div class="progress" style="height:8px;">
                        @php $pct = $balance['total'] > 0 ? min(100, ($balance['used']/$balance['total'])*100) : 0; @endphp
                        <div class="progress-bar {{ $pct > 80 ? 'bg-danger' : ($pct > 50 ? 'bg-warning' : 'bg-success') }}" style="width:{{ $pct }}%"></div>
                    </div>
                    <div class="text-muted small mt-1">Rimanenti: <strong>{{ $balance['remaining'] }}</strong> giorni</div>
                </div>
                @empty
                <p class="text-muted mb-0">Nessun tipo di congedo configurato.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Communications --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-megaphone me-2"></i>Comunicazioni Recenti</span>
                <a href="{{ route('communications.index') }}" class="btn btn-sm btn-outline-primary">Tutte</a>
            </div>
            <div class="list-group list-group-flush">
                @forelse($data['communications'] as $comm)
                <a href="{{ route('communications.show', $comm) }}" class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between">
                        <span class="fw-medium small">{{ Str::limit($comm->title, 50) }}</span>
                        <span class="text-muted small">{{ $comm->published_at ? \Carbon\Carbon::parse($comm->published_at)->diffForHumans() : '' }}</span>
                    </div>
                    <div class="text-muted small">{{ Str::limit(strip_tags($comm->content), 80) }}</div>
                </a>
                @empty
                <div class="list-group-item text-center text-muted py-3">Nessuna comunicazione recente.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateTime(){
    const now = new Date();
    document.getElementById('current-time').textContent = now.toLocaleTimeString('it-IT',{hour:'2-digit',minute:'2-digit',second:'2-digit'});
}
updateTime();
setInterval(updateTime, 1000);
</script>
@endpush
