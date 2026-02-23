@extends('layouts.app')
@section('title', 'Cedolini - GestionaleHR')
@section('page-title', 'Cedolini Paga')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Cedolini Paga</h5>
    @if(in_array(auth()->user()->role, ['admin','hr']))
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#generateModal"><i class="bi bi-lightning me-2"></i>Genera Mese</button>
        <a href="{{ route('payrolls.create') }}" class="btn btn-primary"><i class="bi bi-plus me-2"></i>Nuovo Cedolino</a>
    </div>
    @endif
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-2">
                <select name="month" class="form-select">
                    <option value="">Tutti i mesi</option>
                    @for($m=1; $m<=12; $m++)
                    <option value="{{ $m }}" {{ request('month')==$m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::createFromDate(null,$m,1)->locale('it')->isoFormat('MMMM') }}
                    </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <select name="year" class="form-select">
                    <option value="">Tutti gli anni</option>
                    @for($y=date('Y'); $y>=2020; $y--)
                    <option value="{{ $y }}" {{ request('year')==$y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Tutti gli stati</option>
                    <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Bozza</option>
                    <option value="processed" {{ request('status')=='processed'?'selected':'' }}>Elaborato</option>
                    <option value="paid" {{ request('status')=='paid'?'selected':'' }}>Pagato</option>
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
                        <th>Periodo</th>
                        <th class="text-end">Lordo</th>
                        <th class="text-end">Ritenute</th>
                        <th class="text-end">Netto</th>
                        <th>Stato</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrolls as $payroll)
                    @php
                        $gross = $payroll->base_salary + $payroll->overtime_pay + $payroll->bonuses;
                        $deductions = $payroll->deductions + $payroll->tax + $payroll->inps_contribution;
                    @endphp
                    <tr>
                        @if(!auth()->user()->isEmployee())
                        <td>{{ $payroll->user->full_name }}</td>
                        @endif
                        <td class="fw-medium">
                            {{ \Carbon\Carbon::createFromDate($payroll->year, $payroll->month, 1)->locale('it')->isoFormat('MMMM') }}
                            {{ $payroll->year }}
                        </td>
                        <td class="text-end">€ {{ number_format($gross, 2, ',', '.') }}</td>
                        <td class="text-end text-muted">€ {{ number_format($deductions, 2, ',', '.') }}</td>
                        <td class="text-end fw-bold text-success">€ {{ number_format($payroll->net_salary, 2, ',', '.') }}</td>
                        <td>
                            @php $sc=['draft'=>'secondary','processed'=>'info','paid'=>'success']; $sl=['draft'=>'Bozza','processed'=>'Elaborato','paid'=>'Pagato']; @endphp
                            <span class="badge bg-{{ $sc[$payroll->status] ?? 'secondary' }}">{{ $sl[$payroll->status] ?? $payroll->status }}</span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('payrolls.show', $payroll) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                                @if(in_array(auth()->user()->role, ['admin','hr']))
                                @if($payroll->status === 'draft')
                                <a href="{{ route('payrolls.edit', $payroll) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('payrolls.process', $payroll) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-info" title="Elabora"><i class="bi bi-check"></i></button>
                                </form>
                                @elseif($payroll->status === 'processed')
                                <form action="{{ route('payrolls.pay', $payroll) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success" title="Segna come pagato"><i class="bi bi-wallet2"></i></button>
                                </form>
                                @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Nessun cedolino trovato.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($payrolls->hasPages())
    <div class="card-footer">{{ $payrolls->links() }}</div>
    @endif
</div>

{{-- Generate Modal --}}
@if(in_array(auth()->user()->role, ['admin','hr']))
<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form action="{{ route('payrolls.generate') }}" method="POST">
                @csrf
                <div class="modal-header"><h6 class="modal-title">Genera Cedolini Mensili</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Mese</label>
                        <select name="month" class="form-select" required>
                            @for($m=1; $m<=12; $m++)
                            <option value="{{ $m }}" {{ date('n')==$m ? 'selected' : '' }}>{{ \Carbon\Carbon::createFromDate(null,$m,1)->locale('it')->isoFormat('MMMM') }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Anno</label>
                        <input type="number" name="year" class="form-control" value="{{ date('Y') }}" min="2020">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-primary btn-sm">Genera</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
