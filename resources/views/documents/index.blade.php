@extends('layouts.app')
@section('title', 'Documenti - GestionaleHR')
@section('page-title', 'Documenti')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Archivio Documenti</h5>
    @if(in_array(auth()->user()->role, ['admin','hr']))
    <a href="{{ route('documents.create') }}" class="btn btn-primary"><i class="bi bi-upload me-2"></i>Carica Documento</a>
    @endif
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="type" class="form-select">
                    <option value="">Tutti i tipi</option>
                    @foreach(['contract'=>'Contratto','payslip'=>'Cedolino','certificate'=>'Certificato','id_document'=>'Documento Identità','tax_document'=>'Documento Fiscale','medical'=>'Medico','training'=>'Formazione','other'=>'Altro'] as $val=>$label)
                    <option value="{{ $val }}" {{ request('type')==$val?'selected':'' }}>{{ $label }}</option>
                    @endforeach
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
                        @if(!auth()->user()->isEmployee())<th>Dipendente</th>@endif
                        <th>Titolo</th>
                        <th>Tipo</th>
                        <th>Dimensione</th>
                        <th>Scadenza</th>
                        <th>Caricato il</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                    <tr>
                        @if(!auth()->user()->isEmployee())
                        <td>{{ $doc->user->full_name }}</td>
                        @endif
                        <td>
                            <div class="fw-medium">{{ $doc->title }}</div>
                            @if($doc->description)<div class="text-muted small">{{ Str::limit($doc->description, 50) }}</div>@endif
                        </td>
                        <td>
                            @php
                                $types=['contract'=>'Contratto','payslip'=>'Cedolino','certificate'=>'Certificato','id_document'=>'Doc. Identità','tax_document'=>'Doc. Fiscale','medical'=>'Medico','training'=>'Formazione','other'=>'Altro'];
                                $icons=['contract'=>'file-text','payslip'=>'receipt','certificate'=>'award','id_document'=>'person-badge','tax_document'=>'calculator','medical'=>'heart-pulse','training'=>'mortarboard','other'=>'file'];
                            @endphp
                            <span class="badge bg-light text-dark">
                                <i class="bi bi-{{ $icons[$doc->type] ?? 'file' }} me-1"></i>
                                {{ $types[$doc->type] ?? $doc->type }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $doc->file_size ? number_format($doc->file_size/1024, 1).' KB' : '—' }}</td>
                        <td>
                            @if($doc->expiry_date)
                            @php $exp = \Carbon\Carbon::parse($doc->expiry_date); @endphp
                            <span class="{{ $exp->isPast() ? 'text-danger' : ($exp->diffInDays() < 30 ? 'text-warning' : 'text-muted') }} small">
                                {{ $exp->format('d/m/Y') }}
                                @if($exp->isPast()) <i class="bi bi-exclamation-triangle"></i>@endif
                            </span>
                            @else
                            <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $doc->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('documents.download', $doc) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i></a>
                                @if(in_array(auth()->user()->role, ['admin','hr']))
                                <form action="{{ route('documents.destroy', $doc) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Eliminare questo documento?')"><i class="bi bi-trash"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Nessun documento trovato.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($documents->hasPages())
    <div class="card-footer">{{ $documents->links() }}</div>
    @endif
</div>
@endsection
