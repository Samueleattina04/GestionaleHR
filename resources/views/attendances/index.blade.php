@extends('layouts.app')
@section('title', 'Presenze - GestionaleHR')
@section('page-title', 'Registro Presenze')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Registro Presenze</h5>
    <div class="d-flex gap-2">
        @if(in_array(auth()->user()->role, ['admin','hr','manager']))
        <a href="{{ route('attendances.report') }}" class="btn btn-outline-primary"><i class="bi bi-file-earmark-bar-graph me-2"></i>Report</a>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAttendanceModal"><i class="bi bi-plus me-2"></i>Aggiungi</button>
        @endif
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            @if(!auth()->user()->isEmployee())
            <div class="col-md-3">
                <select name="user_id" class="form-select">
                    <option value="">Tutti i dipendenti</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('user_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="Dal">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="Al">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Tutti gli stati</option>
                    <option value="present" {{ request('status')=='present'?'selected':'' }}>Presente</option>
                    <option value="absent" {{ request('status')=='absent'?'selected':'' }}>Assente</option>
                    <option value="late" {{ request('status')=='late'?'selected':'' }}>Ritardo</option>
                    <option value="remote" {{ request('status')=='remote'?'selected':'' }}>Smart Working</option>
                    <option value="on_leave" {{ request('status')=='on_leave'?'selected':'' }}>Permesso</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-search"></i></button>
            </div>
            @if(request()->hasAny(['user_id','date_from','date_to','status']))
            <div class="col-md-1">
                <a href="{{ route('attendances.index') }}" class="btn btn-outline-secondary w-100"><i class="bi bi-x"></i></a>
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
                        <th>Data</th>
                        @if(!auth()->user()->isEmployee())<th>Dipendente</th>@endif
                        <th>Entrata</th>
                        <th>Pausa</th>
                        <th>Uscita</th>
                        <th>Ore Lavorate</th>
                        <th>Straordinari</th>
                        <th>Stato</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $att)
                    <tr>
                        <td class="fw-medium">{{ \Carbon\Carbon::parse($att->date)->format('d/m/Y') }}
                            <div class="text-muted small">{{ \Carbon\Carbon::parse($att->date)->locale('it')->isoFormat('dddd') }}</div>
                        </td>
                        @if(!auth()->user()->isEmployee())
                        <td>
                            <a href="{{ route('employees.show', $att->user) }}" class="text-decoration-none fw-medium">{{ $att->user->full_name }}</a>
                        </td>
                        @endif
                        <td>{{ $att->clock_in ? substr($att->clock_in,0,5) : '—' }}</td>
                        <td class="text-muted small">
                            {{ $att->break_start ? substr($att->break_start,0,5) : '—' }}
                            {{ $att->break_end ? ' – '.substr($att->break_end,0,5) : '' }}
                        </td>
                        <td>{{ $att->clock_out ? substr($att->clock_out,0,5) : '—' }}</td>
                        <td>{{ $att->hours_worked ? $att->hours_worked.'h' : '—' }}</td>
                        <td>{{ $att->overtime_hours > 0 ? '+'.$att->overtime_hours.'h' : '—' }}</td>
                        <td>
                            @php
                                $sc=['present'=>'success','absent'=>'danger','late'=>'warning','half_day'=>'info','remote'=>'primary','on_leave'=>'secondary'];
                                $sl=['present'=>'Presente','absent'=>'Assente','late'=>'Ritardo','half_day'=>'Mezza Giornata','remote'=>'Smart Working','on_leave'=>'Permesso'];
                            @endphp
                            <span class="badge bg-{{ $sc[$att->status] ?? 'secondary' }}">{{ $sl[$att->status] ?? $att->status }}</span>
                        </td>
                        <td class="text-muted small">{{ Str::limit($att->notes, 40) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">Nessuna presenza trovata.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($attendances->hasPages())
    <div class="card-footer">{{ $attendances->links() }}</div>
    @endif
</div>

{{-- Add Attendance Modal --}}
@if(in_array(auth()->user()->role, ['admin','hr','manager']))
<div class="modal fade" id="addAttendanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('attendances.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Aggiungi Presenza</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Dipendente *</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Seleziona...</option>
                            @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Data *</label>
                        <input type="date" name="date" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-medium">Entrata</label>
                            <input type="time" name="clock_in" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-medium">Uscita</label>
                            <input type="time" name="clock_out" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3 mt-2">
                        <label class="form-label fw-medium">Stato *</label>
                        <select name="status" class="form-select" required>
                            <option value="present">Presente</option>
                            <option value="absent">Assente</option>
                            <option value="late">Ritardo</option>
                            <option value="half_day">Mezza Giornata</option>
                            <option value="remote">Smart Working</option>
                            <option value="on_leave">Permesso</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Note</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary">Salva</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
