@extends('layouts.app')
@section('title', 'Calendario Ferie - GestionaleHR')
@section('page-title', 'Calendario Ferie & Permessi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Calendario</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('leaves.index') }}" class="btn btn-outline-primary"><i class="bi bi-list me-2"></i>Lista</a>
        <a href="{{ route('leaves.create') }}" class="btn btn-primary"><i class="bi bi-plus me-2"></i>Nuova Richiesta</a>
    </div>
</div>

@php
    $currentMonth = \Carbon\Carbon::createFromDate($year, $month, 1);
    $prevMonth = $currentMonth->copy()->subMonth();
    $nextMonth = $currentMonth->copy()->addMonth();
    $daysInMonth = $currentMonth->daysInMonth;
    $firstDow = $currentMonth->copy()->startOfMonth()->dayOfWeekIso; // 1=Mon, 7=Sun

    // Map leaves to days
    $leavesByDay = [];
    foreach($leaves as $leave){
        $s = \Carbon\Carbon::parse($leave->start_date);
        $e = \Carbon\Carbon::parse($leave->end_date);
        for($d = $s->copy(); $d->lte($e); $d->addDay()){
            if($d->month == $month && $d->year == $year){
                $leavesByDay[$d->day][] = $leave;
            }
        }
    }
@endphp

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <select name="month" class="form-select">
                    @for($m=1; $m<=12; $m++)
                    <option value="{{ $m }}" {{ $month==$m ? 'selected' : '' }}>{{ $currentMonth->copy()->month($m)->locale('it')->isoFormat('MMMM') }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <select name="year" class="form-select">
                    @for($y=date('Y')+1; $y>=2020; $y--)
                    <option value="{{ $y }}" {{ $year==$y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary">Vai</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <a href="{{ route('leaves.calendar', ['month'=>$prevMonth->month,'year'=>$prevMonth->year]) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-chevron-left"></i></a>
        <strong>{{ ucfirst($currentMonth->locale('it')->isoFormat('MMMM YYYY')) }}</strong>
        <a href="{{ route('leaves.calendar', ['month'=>$nextMonth->month,'year'=>$nextMonth->year]) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-chevron-right"></i></a>
    </div>
    <div class="card-body p-2">
        <div class="row g-0" style="display:grid;grid-template-columns:repeat(7,1fr);">
            @foreach(['Lun','Mar','Mer','Gio','Ven','Sab','Dom'] as $day)
            <div class="text-center fw-semibold small py-2 border-bottom">{{ $day }}</div>
            @endforeach
            @for($i=1; $i<$firstDow; $i++)
            <div class="p-1" style="min-height:90px;"></div>
            @endfor
            @for($d=1; $d<=$daysInMonth; $d++)
            @php
                $date = \Carbon\Carbon::createFromDate($year, $month, $d);
                $isToday = $date->isToday();
                $isWeekend = $date->isWeekend();
                $dayLeaves = $leavesByDay[$d] ?? [];
            @endphp
            <div class="p-1 border {{ $isToday ? 'bg-primary bg-opacity-10' : '' }} {{ $isWeekend ? 'bg-light' : '' }}" style="min-height:90px;">
                <div class="fw-semibold small {{ $isToday ? 'text-primary' : ($isWeekend ? 'text-muted' : '') }}">{{ $d }}</div>
                @foreach($dayLeaves as $leave)
                <div class="badge bg-success w-100 text-start text-truncate mb-1" style="font-size:.65rem;" title="{{ $leave->user->full_name }}">
                    {{ $leave->user->full_name }}
                </div>
                @endforeach
            </div>
            @endfor
        </div>
    </div>
</div>

@if($leaves->count() > 0)
<div class="card mt-3">
    <div class="card-header">Ferie Approvate nel Mese</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead><tr><th>Dipendente</th><th>Dal</th><th>Al</th><th>Giorni</th></tr></thead>
                <tbody>
                    @foreach($leaves as $leave)
                    <tr>
                        <td>{{ $leave->user->full_name }}</td>
                        <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('d/m/Y') }}</td>
                        <td>{{ $leave->total_days }}gg</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
