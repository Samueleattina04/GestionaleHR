@extends('layouts.app')
@section('title', 'Dipartimenti - GestionaleHR')
@section('page-title', 'Dipartimenti')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Gestione Dipartimenti</h5>
    <a href="{{ route('departments.create') }}" class="btn btn-primary"><i class="bi bi-plus me-2"></i>Nuovo Dipartimento</a>
</div>

<div class="row g-3">
    @forelse($departments as $dept)
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="fw-bold mb-1">{{ $dept->name }}</h6>
                        <span class="badge bg-secondary">{{ $dept->code }}</span>
                    </div>
                    @if(!$dept->is_active)
                    <span class="badge bg-danger">Inattivo</span>
                    @endif
                </div>
                @if($dept->description)
                <p class="text-muted small mb-3">{{ Str::limit($dept->description, 100) }}</p>
                @endif
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="text-center">
                        <div class="fw-bold fs-4 text-primary">{{ $dept->active_employees_count }}</div>
                        <div class="text-muted small">Dipendenti</div>
                    </div>
                    @if($dept->manager)
                    <div>
                        <div class="text-muted small">Manager</div>
                        <div class="fw-medium small">{{ $dept->manager->full_name }}</div>
                    </div>
                    @endif
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('departments.show', $dept) }}" class="btn btn-sm btn-outline-primary flex-fill"><i class="bi bi-eye me-1"></i>Dettagli</a>
                    <a href="{{ route('departments.edit', $dept) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                    <form action="{{ route('departments.destroy', $dept) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Disattivare questo dipartimento?')">
                            <i class="bi bi-archive"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-building display-4 d-block mb-3"></i>
                Nessun dipartimento creato.
                <div class="mt-3"><a href="{{ route('departments.create') }}" class="btn btn-primary">Crea il primo dipartimento</a></div>
            </div>
        </div>
    </div>
    @endforelse
</div>
@endsection
