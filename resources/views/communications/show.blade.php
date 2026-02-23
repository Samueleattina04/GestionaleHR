@extends('layouts.app')
@section('title', 'Comunicazione - GestionaleHR')
@section('page-title', 'Comunicazione')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Comunicazione</h5>
    <div class="d-flex gap-2">
        @if(in_array(auth()->user()->role, ['admin','hr']) && !$communication->is_published)
        <form action="{{ route('communications.publish', $communication) }}" method="POST">
            @csrf
            <button class="btn btn-success"><i class="bi bi-send me-2"></i>Pubblica</button>
        </form>
        @endif
        @if(in_array(auth()->user()->role, ['admin','hr']))
        <form action="{{ route('communications.destroy', $communication) }}" method="POST">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger" onclick="return confirm('Eliminare questa comunicazione?')"><i class="bi bi-trash me-2"></i>Elimina</button>
        </form>
        @endif
        <a href="{{ route('communications.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Indietro</a>
    </div>
</div>

<div class="card" style="max-width:800px;">
    <div class="card-body">
        <div class="d-flex align-items-start gap-2 mb-3">
            @php
                $pc=['low'=>'secondary','normal'=>'primary','high'=>'warning','urgent'=>'danger'];
                $pl=['low'=>'Bassa','normal'=>'Normale','high'=>'Alta','urgent'=>'Urgente'];
            @endphp
            <span class="badge bg-{{ $pc[$communication->priority] ?? 'secondary' }}">{{ $pl[$communication->priority] ?? $communication->priority }}</span>
            @if(!$communication->is_published)
            <span class="badge bg-warning text-dark">Bozza</span>
            @endif
        </div>
        <h4 class="fw-bold mb-3">{{ $communication->title }}</h4>
        <div class="text-muted small mb-4">
            <i class="bi bi-person me-1"></i>{{ $communication->author->full_name ?? '—' }}
            &nbsp;·&nbsp;
            <i class="bi bi-clock me-1"></i>
            {{ $communication->is_published && $communication->published_at ? \Carbon\Carbon::parse($communication->published_at)->format('d/m/Y H:i') : 'Non pubblicata' }}
        </div>
        <div class="border-top pt-3">
            {!! nl2br(e($communication->body ?? $communication->content ?? '')) !!}
        </div>
    </div>
</div>
@endsection
