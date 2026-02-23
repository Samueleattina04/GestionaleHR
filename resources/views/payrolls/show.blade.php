@extends('layouts.app')
@section('title', 'Cedolino - GestionaleHR')
@section('page-title', 'Dettaglio Cedolino')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Cedolino Paga</h5>
    <div class="d-flex gap-2">
        @if(in_array(auth()->user()->role, ['admin','hr']) && $payroll->status !== 'paid')
        <a href="{{ route('payrolls.edit', $payroll) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-2"></i>Modifica</a>
        @endif
        <button onclick="window.print()" class="btn btn-outline-secondary"><i class="bi bi-printer me-2"></i>Stampa</button>
        <a href="{{ route('payrolls.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Indietro</a>
    </div>
</div>

<div class="card" style="max-width:700px;">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h4 class="fw-bold">GestionaleHR</h4>
                <p class="text-muted mb-0">Busta Paga</p>
            </div>
            @php $sc=['draft'=>'secondary','processed'=>'info','paid'=>'success']; $sl=['draft'=>'Bozza','processed'=>'Elaborato','paid'=>'Pagato']; @endphp
            <span class="badge bg-{{ $sc[$payroll->status] ?? 'secondary' }} fs-6 py-2 px-3">{{ $sl[$payroll->status] ?? $payroll->status }}</span>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <h6 class="fw-semibold mb-2">Dipendente</h6>
                <div class="fw-medium">{{ $payroll->user->full_name }}</div>
                <div class="text-muted small">{{ $payroll->user->email }}</div>
                @if($payroll->user->job_title)<div class="text-muted small">{{ $payroll->user->job_title }}</div>@endif
                @if($payroll->user->department)<div class="text-muted small">{{ $payroll->user->department->name }}</div>@endif
            </div>
            <div class="col-6 text-end">
                <h6 class="fw-semibold mb-2">Periodo</h6>
                <div class="fw-bold fs-5">
                    {{ ucfirst(\Carbon\Carbon::createFromDate($payroll->year,$payroll->month,1)->locale('it')->isoFormat('MMMM YYYY')) }}
                </div>
                @if($payroll->payment_date)
                <div class="text-muted small">Pagato il: {{ \Carbon\Carbon::parse($payroll->payment_date)->format('d/m/Y') }}</div>
                @endif
            </div>
        </div>

        <hr>
        <h6 class="fw-semibold mb-3">Voci di Retribuzione</h6>
        <table class="table table-borderless mb-0">
            <tbody>
                <tr>
                    <td>Stipendio Base</td>
                    <td class="text-end fw-medium">€ {{ number_format($payroll->base_salary, 2, ',', '.') }}</td>
                </tr>
                @if($payroll->overtime_pay > 0)
                <tr>
                    <td>Straordinari</td>
                    <td class="text-end text-success">+ € {{ number_format($payroll->overtime_pay, 2, ',', '.') }}</td>
                </tr>
                @endif
                @if($payroll->bonuses > 0)
                <tr>
                    <td>Bonus / Premi</td>
                    <td class="text-end text-success">+ € {{ number_format($payroll->bonuses, 2, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="border-top fw-bold">
                    <td>Totale Lordo</td>
                    <td class="text-end">€ {{ number_format($payroll->base_salary + $payroll->overtime_pay + $payroll->bonuses, 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
        <hr>
        <h6 class="fw-semibold mb-3">Detrazioni</h6>
        <table class="table table-borderless mb-0">
            <tbody>
                @if($payroll->tax > 0)
                <tr>
                    <td>IRPEF (Tasse)</td>
                    <td class="text-end text-danger">- € {{ number_format($payroll->tax, 2, ',', '.') }}</td>
                </tr>
                @endif
                @if($payroll->inps_contribution > 0)
                <tr>
                    <td>Contributi INPS</td>
                    <td class="text-end text-danger">- € {{ number_format($payroll->inps_contribution, 2, ',', '.') }}</td>
                </tr>
                @endif
                @if($payroll->deductions > 0)
                <tr>
                    <td>Altre Trattenute</td>
                    <td class="text-end text-danger">- € {{ number_format($payroll->deductions, 2, ',', '.') }}</td>
                </tr>
                @endif
            </tbody>
        </table>
        <hr>
        <div class="d-flex justify-content-between align-items-center py-2 bg-success bg-opacity-10 rounded px-3">
            <span class="fw-bold fs-5">NETTO IN BUSTA</span>
            <span class="fw-bold fs-4 text-success">€ {{ number_format($payroll->net_salary, 2, ',', '.') }}</span>
        </div>
        @if($payroll->notes)
        <div class="mt-3 alert alert-light small">
            <strong>Note:</strong> {{ $payroll->notes }}
        </div>
        @endif
    </div>
    @if(in_array(auth()->user()->role, ['admin','hr']))
    <div class="card-footer d-flex gap-2">
        @if($payroll->status === 'draft')
        <form action="{{ route('payrolls.process', $payroll) }}" method="POST">
            @csrf
            <button class="btn btn-info btn-sm text-white"><i class="bi bi-check me-1"></i>Segna come Elaborato</button>
        </form>
        @elseif($payroll->status === 'processed')
        <form action="{{ route('payrolls.pay', $payroll) }}" method="POST">
            @csrf
            <button class="btn btn-success btn-sm"><i class="bi bi-wallet2 me-1"></i>Segna come Pagato</button>
        </form>
        @endif
    </div>
    @endif
</div>
@endsection
