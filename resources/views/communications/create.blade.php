@extends('layouts.app')
@section('title', 'Nuova Comunicazione - GestionaleHR')
@section('page-title', 'Nuova Comunicazione')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Crea Comunicazione</h5>
    <a href="{{ route('communications.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Indietro</a>
</div>

<div class="card" style="max-width:700px;">
    <div class="card-body">
        <form action="{{ route('communications.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-medium">Titolo *</label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Contenuto *</label>
                <textarea name="body" class="form-control @error('body') is-invalid @enderror" rows="8" required>{{ old('body') }}</textarea>
                @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Priorità *</label>
                    <select name="priority" class="form-select">
                        <option value="low" {{ old('priority')=='low'?'selected':'' }}>Bassa</option>
                        <option value="normal" {{ old('priority','normal')=='normal'?'selected':'' }}>Normale</option>
                        <option value="high" {{ old('priority')=='high'?'selected':'' }}>Alta</option>
                        <option value="urgent" {{ old('priority')=='urgent'?'selected':'' }}>Urgente</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Destinatari *</label>
                    <select name="target" class="form-select" id="target_select" required>
                        <option value="all" {{ old('target','all')=='all'?'selected':'' }}>Tutti</option>
                        <option value="department" {{ old('target')=='department'?'selected':'' }}>Dipartimento</option>
                        <option value="role" {{ old('target')=='role'?'selected':'' }}>Ruolo</option>
                        <option value="individual" {{ old('target')=='individual'?'selected':'' }}>Individuale</option>
                    </select>
                </div>
            </div>

            <div id="dept_select" class="mb-3 d-none">
                <label class="form-label fw-medium">Seleziona Dipartimento</label>
                <select name="target_department_id" class="form-select">
                    <option value="">Seleziona...</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ old('target_department_id')==$dept->id?'selected':'' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div id="role_select" class="mb-3 d-none">
                <label class="form-label fw-medium">Seleziona Ruolo</label>
                <select name="target_role" class="form-select">
                    <option value="">Seleziona...</option>
                    <option value="employee" {{ old('target_role')=='employee'?'selected':'' }}>Dipendenti</option>
                    <option value="manager" {{ old('target_role')=='manager'?'selected':'' }}>Manager</option>
                    <option value="hr" {{ old('target_role')=='hr'?'selected':'' }}>HR</option>
                </select>
            </div>

            <div id="user_select" class="mb-3 d-none">
                <label class="form-label fw-medium">Seleziona Destinatario</label>
                <select name="target_user_id" class="form-select">
                    <option value="">Seleziona...</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ old('target_user_id')==$emp->id?'selected':'' }}>{{ $emp->full_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="publish_now" id="publish_now" value="1" {{ old('publish_now') ? 'checked' : '' }}>
                    <label class="form-check-label" for="publish_now">Pubblica subito</label>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-2"></i>Salva</button>
                <a href="{{ route('communications.index') }}" class="btn btn-outline-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
var sel = document.getElementById('target_select');
function updateTarget(){
    document.getElementById('dept_select').classList.toggle('d-none', sel.value !== 'department');
    document.getElementById('role_select').classList.toggle('d-none', sel.value !== 'role');
    document.getElementById('user_select').classList.toggle('d-none', sel.value !== 'individual');
}
sel.addEventListener('change', updateTarget);
updateTarget();
</script>
@endpush
