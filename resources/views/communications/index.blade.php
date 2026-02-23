@extends('layouts.app')
@section('title', 'Comunicazioni - GestionaleHR')
@section('page-title', 'Comunicazioni')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Comunicazioni Aziendali</h5>
    @if(in_array(auth()->user()->role, ['admin','hr']))
    <a href="{{ route('communications.create') }}" class="btn btn-primary"><i class="bi bi-plus me-2"></i>Nuova Comunicazione</a>
    @endif
</div>

<div class="row g-3">
    @forelse($communications as $comm)
    <div class="col-12">
        <div class="card {{ !$comm->is_published ? 'border-warning' : '' }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            @php
                                $pc=['low'=>'secondary','normal'=>'primary','high'=>'warning','urgent'=>'danger'];
                                $pl=['low'=>'Bassa','normal'=>'Normale','high'=>'Alta','urgent'=>'Urgente'];
                            @endphp
                            <span class="badge bg-{{ $pc[$comm->priority] ?? 'secondary' }}">{{ $pl[$comm->priority] ?? $comm->priority }}</span>
                            @if(!$comm->is_published)
                            <span class="badge bg-warning text-dark">Bozza</span>
                            @endif
                            <span class="badge bg-light text-dark">
                                @php
                                    $tl=['all'=>'Tutti','department'=>'Dipartimento','role'=>'Ruolo','individual'=>'Individuale'];
                                @endphp
                                {{ $tl[$comm->target] ?? $comm->target }}
                            </span>
                        </div>
                        <h6 class="fw-bold mb-1">
                            <a href="{{ route('communications.show', $comm) }}" class="text-decoration-none text-dark">{{ $comm->title }}</a>
                        </h6>
                        <p class="text-muted small mb-2">{{ Str::limit(strip_tags($comm->body ?? $comm->content ?? ''), 150) }}</p>
                        <div class="text-muted small">
                            <i class="bi bi-person me-1"></i>{{ $comm->author->full_name ?? '—' }}
                            &nbsp;·&nbsp;
                            <i class="bi bi-clock me-1"></i>
                            {{ $comm->is_published ? ($comm->published_at ? \Carbon\Carbon::parse($comm->published_at)->diffForHumans() : $comm->created_at->diffForHumans()) : 'Non pubblicata' }}
                        </div>
                    </div>
                    <div class="d-flex gap-1 ms-3">
                        <a href="{{ route('communications.show', $comm) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                        @if(in_array(auth()->user()->role, ['admin','hr']))
                        @if(!$comm->is_published)
                        <form action="{{ route('communications.publish', $comm) }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-outline-success" title="Pubblica"><i class="bi bi-send"></i></button>
                        </form>
                        @endif
                        <form action="{{ route('communications.destroy', $comm) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Eliminare questa comunicazione?')"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-megaphone display-4 d-block mb-3"></i>
                Nessuna comunicazione disponibile.
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($communications->hasPages())
<div class="mt-3">{{ $communications->links() }}</div>
@endif
@endsection
