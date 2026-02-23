@extends('layouts.app')
@section('title', 'Formazione - GestionaleHR')
@section('page-title', 'Corsi di Formazione')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Corsi di Formazione</h5>
    @if(in_array(auth()->user()->role, ['admin','hr']))
    <a href="{{ route('training.create') }}" class="btn btn-primary"><i class="bi bi-plus me-2"></i>Nuovo Corso</a>
    @endif
</div>

<div class="row g-3">
    @forelse($courses as $course)
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 {{ !$course->is_active ? 'opacity-75' : '' }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="fw-bold mb-0">{{ $course->title }}</h6>
                    @if($course->is_mandatory)
                    <span class="badge bg-danger ms-2">Obbligatorio</span>
                    @endif
                </div>
                @if($course->provider)
                <div class="text-muted small mb-2"><i class="bi bi-building me-1"></i>{{ $course->provider }}</div>
                @endif
                @if($course->description)
                <p class="text-muted small mb-3">{{ Str::limit($course->description, 100) }}</p>
                @endif
                <div class="d-flex gap-3 mb-3 small">
                    <div>
                        <div class="text-muted">Inizio</div>
                        <div class="fw-medium">{{ \Carbon\Carbon::parse($course->start_date)->format('d/m/Y') }}</div>
                    </div>
                    @if($course->end_date)
                    <div>
                        <div class="text-muted">Fine</div>
                        <div class="fw-medium">{{ \Carbon\Carbon::parse($course->end_date)->format('d/m/Y') }}</div>
                    </div>
                    @endif
                    <div>
                        <div class="text-muted">Iscritti</div>
                        <div class="fw-medium">
                            {{ $course->participants_count }}
                            @if($course->max_participants)/ {{ $course->max_participants }}@endif
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('training.show', $course) }}" class="btn btn-sm btn-outline-primary flex-fill"><i class="bi bi-eye me-1"></i>Dettagli</a>
                    @if(in_array(auth()->user()->role, ['admin','hr']))
                    <a href="{{ route('training.edit', $course) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-mortarboard display-4 d-block mb-3"></i>
                Nessun corso di formazione disponibile.
                @if(in_array(auth()->user()->role, ['admin','hr']))
                <div class="mt-3"><a href="{{ route('training.create') }}" class="btn btn-primary">Crea il primo corso</a></div>
                @endif
            </div>
        </div>
    </div>
    @endforelse
</div>
@if($courses->hasPages())
<div class="mt-3">{{ $courses->links() }}</div>
@endif
@endsection
