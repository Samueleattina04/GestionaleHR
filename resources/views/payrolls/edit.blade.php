@extends('layouts.app')
@section('title', 'Modifica Cedolino - GestionaleHR')
@section('page-title', 'Modifica Cedolino')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Modifica Cedolino</h5>
    <a href="{{ route('payrolls.show', $payroll) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Indietro</a>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-header">
        {{ $payroll->user->full_name }} —
        {{ ucfirst(\Carbon\Carbon::createFromDate($payroll->year,$payroll->month,1)->locale('it')->isoFormat('MMMM YYYY')) }}
    </div>
    <div class="card-body">
        <form action="{{ route('payrolls.update', $payroll) }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Stipendio Base (€) *</label>
                    <input type="number" name="base_salary" step="0.01" class="form-control" value="{{ old('base_salary', $payroll->base_salary) }}" required id="base_salary">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Straordinari (€)</label>
                    <input type="number" name="overtime_pay" step="0.01" class="form-control" value="{{ old('overtime_pay', $payroll->overtime_pay) }}" id="overtime_pay">
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Bonus (€)</label>
                    <input type="number" name="bonuses" step="0.01" class="form-control" value="{{ old('bonuses', $payroll->bonuses) }}" id="bonuses">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Altre Trattenute (€)</label>
                    <input type="number" name="deductions" step="0.01" class="form-control" value="{{ old('deductions', $payroll->deductions) }}" id="deductions">
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">IRPEF (€)</label>
                    <input type="number" name="tax" step="0.01" class="form-control" value="{{ old('tax', $payroll->tax) }}" id="tax">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Contributi INPS (€)</label>
                    <input type="number" name="inps_contribution" step="0.01" class="form-control" value="{{ old('inps_contribution', $payroll->inps_contribution) }}" id="inps">
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
                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $payroll->notes) }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-2"></i>Salva Modifiche</button>
                <a href="{{ route('payrolls.show', $payroll) }}" class="btn btn-outline-secondary">Annulla</a>
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
updateNet();
</script>
@endpush
