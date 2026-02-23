@extends('layouts.app')
@section('title', 'Dipendenti - GestionaleHR')
@section('page-title', 'Dipendenti')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Gestione Dipendenti</h5>
    @can('admin,hr')
    <a href="{{ route('employees.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-2"></i>Nuovo Dipendente</a>
    @endcan
    @if(in_array(auth()->user()->role, ['admin','hr']))
    <a href="{{ route('employees.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-2"></i>Nuovo Dipendente</a>
    @endif
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cerca per nome, email, CF..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="department" class="form-select">
                    <option value="">Tutti i dipartimenti</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Tutti gli stati</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Attivi</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>In attesa</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Sospesi</option>
                    <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Terminati</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="role" class="form-select">
                    <option value="">Tutti i ruoli</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="hr" {{ request('role') == 'hr' ? 'selected' : '' }}>HR</option>
                    <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                    <option value="employee" {{ request('role') == 'employee' ? 'selected' : '' }}>Dipendente</option>
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
                        <th>Dipendente</th>
                        <th>Dipartimento</th>
                        <th>Ruolo</th>
                        <th>Stato</th>
                        <th>Data Assunzione</th>
                        <th class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;font-size:.75rem;">
                                    {{ strtoupper(substr($emp->name,0,1)) }}{{ strtoupper(substr($emp->surname,0,1)) }}
                                </div>
                                <div>
                                    <div class="fw-medium">{{ $emp->full_name }}</div>
                                    <div class="text-muted small">{{ $emp->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $emp->department->name ?? '—' }}</td>
                        <td>
                            @php
                                $roleColors = ['admin'=>'danger','hr'=>'info','manager'=>'warning','employee'=>'secondary'];
                                $roleColor = $roleColors[$emp->role] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $roleColor }}">{{ ucfirst($emp->role) }}</span>
                        </td>
                        <td>
                            @php
                                $statusColors = ['active'=>'success','pending'=>'warning','suspended'=>'secondary','terminated'=>'danger'];
                                $statusLabels = ['active'=>'Attivo','pending'=>'In attesa','suspended'=>'Sospeso','terminated'=>'Terminato'];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$emp->status] ?? 'secondary' }}">{{ $statusLabels[$emp->status] ?? $emp->status }}</span>
                        </td>
                        <td class="text-muted small">{{ $emp->hire_date ? \Carbon\Carbon::parse($emp->hire_date)->format('d/m/Y') : '—' }}</td>
                        <td class="text-end">
                            <a href="{{ route('employees.show', $emp) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            @if(in_array(auth()->user()->role, ['admin','hr']))
                            <a href="{{ route('employees.edit', $emp) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Nessun dipendente trovato.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($employees->hasPages())
    <div class="card-footer">
        {{ $employees->links() }}
    </div>
    @endif
</div>
@endsection
