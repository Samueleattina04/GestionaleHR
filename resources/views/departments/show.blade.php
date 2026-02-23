@extends('layouts.app')
@section('title', 'Dipartimento - GestionaleHR')
@section('page-title', 'Dettaglio Dipartimento')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">{{ $department->name }}</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('departments.edit', $department) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-2"></i>Modifica</a>
        <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Indietro</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Informazioni</h6>
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">Nome</dt><dd class="col-7">{{ $department->name }}</dd>
                    <dt class="col-5 text-muted">Codice</dt><dd class="col-7"><span class="badge bg-secondary">{{ $department->code }}</span></dd>
                    <dt class="col-5 text-muted">Stato</dt><dd class="col-7"><span class="badge bg-{{ $department->is_active ? 'success' : 'danger' }}">{{ $department->is_active ? 'Attivo' : 'Inattivo' }}</span></dd>
                    <dt class="col-5 text-muted">Responsabile</dt><dd class="col-7">{{ $department->manager ? $department->manager->full_name : '—' }}</dd>
                    <dt class="col-5 text-muted">Dipendenti</dt><dd class="col-7 fw-bold text-primary">{{ $department->employees->count() }}</dd>
                </dl>
                @if($department->description)
                <hr>
                <p class="text-muted small mb-0">{{ $department->description }}</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-people me-2"></i>Dipendenti del Dipartimento</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead><tr><th>Dipendente</th><th>Ruolo</th><th>Mansione</th><th>Azioni</th></tr></thead>
                        <tbody>
                            @forelse($department->employees as $emp)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:.7rem;">
                                            {{ strtoupper(substr($emp->name,0,1)) }}{{ strtoupper(substr($emp->surname,0,1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $emp->full_name }}</div>
                                            <div class="text-muted small">{{ $emp->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-secondary">{{ ucfirst($emp->role) }}</span></td>
                                <td class="text-muted small">{{ $emp->job_title ?? '—' }}</td>
                                <td><a href="{{ route('employees.show', $emp) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Nessun dipendente assegnato.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
