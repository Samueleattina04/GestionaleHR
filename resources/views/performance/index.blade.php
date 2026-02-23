@extends('layouts.app')
@section('title', 'Valutazioni - GestionaleHR')
@section('page-title', 'Valutazioni Performance')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Valutazioni Performance</h5>
    @if(in_array(auth()->user()->role, ['admin','hr','manager']))
    <a href="{{ route('performance.create') }}" class="btn btn-primary"><i class="bi bi-plus me-2"></i>Nuova Valutazione</a>
    @endif
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-2">
                <select name="year" class="form-select">
                    <option value="">Tutti gli anni</option>
                    @for($y=date('Y'); $y>=2020; $y--)
                    <option value="{{ $y }}" {{ request('year')==$y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
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
                        <th>Anno</th>
                        <th>Periodo</th>
                        <th>Valutatore</th>
                        <th class="text-center">Punteggio</th>
                        <th>Stato</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                    <tr>
                        <td class="fw-medium">{{ $review->user->full_name }}</td>
                        <td>{{ $review->year }}</td>
                        <td>{{ $review->period }}</td>
                        <td class="text-muted small">{{ $review->reviewer->full_name ?? '—' }}</td>
                        <td class="text-center">
                            @php
                                $score = $review->overall_score;
                                $color = $score >= 7 ? 'success' : ($score >= 5 ? 'warning' : 'danger');
                            @endphp
                            <span class="badge bg-{{ $color }} fs-6">{{ $score }}/10</span>
                        </td>
                        <td>
                            @php $sc=['draft'=>'secondary','submitted'=>'info','acknowledged'=>'success'];
                            $sl=['draft'=>'Bozza','submitted'=>'Inviata','acknowledged'=>'Confermata'];@endphp
                            <span class="badge bg-{{ $sc[$review->status] ?? 'secondary' }}">{{ $sl[$review->status] ?? $review->status }}</span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('performance.show', $review) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                                @if(in_array(auth()->user()->role, ['admin','hr','manager']) && $review->status !== 'acknowledged')
                                <a href="{{ route('performance.edit', $review) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                @if($review->status === 'draft')
                                <form action="{{ route('performance.submit', $review) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-info" title="Invia al dipendente"><i class="bi bi-send"></i></button>
                                </form>
                                @endif
                                @endif
                                @if($review->user_id === auth()->id() && $review->status === 'submitted')
                                <form action="{{ route('performance.acknowledge', $review) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-success" title="Prendi in carico"><i class="bi bi-check-circle"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Nessuna valutazione trovata.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($reviews->hasPages())
    <div class="card-footer">{{ $reviews->links() }}</div>
    @endif
</div>
@endsection
