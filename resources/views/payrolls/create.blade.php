@extends('layouts.app')
@section('title', 'Nuovo Cedolino - GestionaleHR')
@section('page-title', 'Nuovo Cedolino')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Crea Nuovo Cedolino</h5>
    <a href="{{ route('payrolls.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Indietro</a>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-body">
        <form action="{{ route('payrolls.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-medium">Dipendente *</label>
                <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                    <option value="">Seleziona dipendente...</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" data-salary="{{ $emp->salary }}" {{ old('user_id') == $emp->id ? 'selected' : '' }}>
                        {{ $emp->full_name }}
                    </option>
                    @endforeach
                </select>
                @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Mese *</label>
                    <select name="month" class="form-select" required>
                        @for($m=1; $m<=12; $m++)
                        <option value="{{ $m }}" {{ old('month', date('n')) == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(null,$m,1)->locale('it')->isoFormat('MMMM') }}
                        </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Anno *</label>
                    <input type="number" name="year" class="form-control" value="{{ old('year', date('Y')) }}" min="2020">
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Stipendio Base (€) *</label>
                    <input type="number" name="base_salary" step="0.01" class="form-control @error('base_salary') is-invalid @enderror" value="{{ old('base_salary') }}" required id="base_salary">
                    @error('base_salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Straordinari (€)</label>
                    <input type="number" name="overtime_pay" step="0.01" class="form-control" value="{{ old('overtime_pay', 0) }}" id="overtime_pay">
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Bonus (€)</label>
                    <input type="number" name="bonuses" step="0.01" class="form-control" value="{{ old('bonuses', 0) }}" id="bonuses">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Altre Trattenute (€)</label>
                    <input type="number" name="deductions" step="0.01" class="form-control" value="{{ old('deductions', 0) }}" id="deductions">
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">IRPEF (€)</label>
                    <input type="number" name="tax" step="0.01" class="form-control" value="{{ old('tax', 0) }}" id="tax">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Contributi INPS (€)</label>
                    <input type="number" name="inps_contribution" step="0.01" class="form-control" value="{{ old('inps_contribution', 0) }}" id="inps">
                </div>
            </div>
            <div class="mb-3 p-3 bg-success bg-opacity-10 rounded">
                <div class="d-flex justify-content-between">
                    <span class="fw-semibold">Netto Stimato:</span>
                    <span class="fw-bold text-success fs-5" id="net_preview">€ 0,00</span>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Note</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-2"></i>Crea Cedolino</button>
                <a href="{{ route('payrolls.index') }}" class="btn btn-outline-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateNet(){
    var base = parseFloat(document.getElementById('base_salary').value)||0;
    var ot = parseFloat(document.getElementById('overtime_pay').value)||0;
    var bon = parseFloat(document.getElementById('bonuses').value)||0;
    var ded = parseFloat(document.getElementById('deductions').value)||0;
    var tax = parseFloat(document.getElementById('tax').value)||0;
    var inps = parseFloat(document.getElementById('inps').value)||0;
    var net = (base + ot + bon) - ded - tax - inps;
    document.getElementById('net_preview').textContent = '€ ' + net.toFixed(2).replace('.',',').replace(/\B(?=(\d{3})+(?!\d))/g,'.');
}
['base_salary','overtime_pay','bonuses','deductions','tax','inps'].forEach(function(id){
    document.getElementById(id).addEventListener('input', updateNet);
});

document.querySelector('select[name=user_id]').addEventListener('change', function(){
    var salary = this.options[this.selectedIndex].dataset.salary;
    if(salary){
        document.getElementById('base_salary').value = salary;
        var tax = Math.round(salary*0.23*100)/100;
        var inps = Math.round(salary*0.0919*100)/100;
        document.getElementById('tax').value = tax;
        document.getElementById('inps').value = inps;
        updateNet();
    }
});
updateNet();
</script>
@endpush
