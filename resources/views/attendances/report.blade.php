@extends('layouts.app')
@section('title', 'Report Presenze - GestionaleHR')
@section('page-title', 'Report Presenze')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Report Mensile Presenze</h5>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-medium small">Mese</label>
                <select name="month" class="form-select">
                    @for($m=1; $m<=12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::createFromDate(null, $m, 1)->locale('it')->isoFormat('MMMM') }}
                    </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-medium small">Anno</label>
                <select name="year" class="form-select">
                    @for($y=date('Y'); $y>=2020; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Aggiorna</button>
            </div>
        </form>
    </div>
</div>

@php
    $daysInMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->daysInMonth;
    $monthLabel = \Carbon\Carbon::createFromDate($year, $month, 1)->locale('it')->isoFormat('MMMM YYYY');
@endphp

<div class="card">
    <div class="card-header">
        <strong>{{ ucfirst($monthLabel) }}</strong> — {{ $employees->count() }} dipendenti
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0" style="font-size:.75rem;">
                <thead class="table-light">
                    <tr>
                        <th style="min-width:150px;">Dipendente</th>
                        @for($d=1; $d<=$daysInMonth; $d++)
                        @php $date = \Carbon\Carbon::createFromDate($year, $month, $d); @endphp
                        <th class="text-center {{ $date->isWeekend() ? 'table-secondary' : '' }}" style="min-width:36px;">
                            <div>{{ $d }}</div>
                            <div class="text-muted">{{ $date->locale('it')->isoFormat('dd') }}</div>
                        </th>
                        @endfor
                        <th class="text-center">Ore Tot.</th>
                        <th class="text-center">Presenze</th>
                        <th class="text-center">Assenze</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                    @php
                        $attMap = $emp->attendances->keyBy(fn($a) => \Carbon\Carbon::parse($a->date)->day);
                        $totalHours = $emp->attendances->sum('hours_worked');
                        $totalPresent = $emp->attendances->whereIn('status',['present','late','half_day','remote'])->count();
                        $totalAbsent = $emp->attendances->whereIn('status',['absent'])->count();
                    @endphp
                    <tr>
                        <td class="fw-medium">{{ $emp->full_name }}</td>
                        @for($d=1; $d<=$daysInMonth; $d++)
                        @php
                            $date = \Carbon\Carbon::createFromDate($year, $month, $d);
                            $att = $attMap->get($d);
                            $cellClass = '';
                            $cellText = '';
                            if($date->isWeekend()){$cellClass='table-secondary';}
                            elseif($att){
                                $sc=['present'=>'table-success','absent'=>'table-danger','late'=>'table-warning','remote'=>'table-info','on_leave'=>'table-secondary','half_day'=>'table-warning'];
                                $st=['present'=>'P','absent'=>'A','late'=>'R','remote'=>'SW','on_leave'=>'FE','half_day'=>'MP'];
                                $cellClass=$sc[$att->status]??'';
                                $cellText=$st[$att->status]??'?';
                            }
                        @endphp
                        <td class="text-center {{ $cellClass }}">{{ $cellText }}</td>
                        @endfor
                        <td class="text-center fw-medium">{{ number_format($totalHours,1) }}</td>
                        <td class="text-center text-success fw-medium">{{ $totalPresent }}</td>
                        <td class="text-center text-danger fw-medium">{{ $totalAbsent }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="{{ $daysInMonth + 4 }}" class="text-center text-muted py-3">Nessun dipendente trovato.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer small text-muted">
        <strong>Legenda:</strong>
        P=Presente &nbsp; A=Assente &nbsp; R=Ritardo &nbsp; SW=Smart Working &nbsp; FE=Ferie/Permesso &nbsp; MP=Mezza Giornata
    </div>
</div>
@endsection
