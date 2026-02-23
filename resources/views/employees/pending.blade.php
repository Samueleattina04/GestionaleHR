@extends('layouts.app')
@section('title', 'Approvazioni - GestionaleHR')
@section('page-title', 'Approvazioni Account')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Account in attesa di approvazione</h5>
    <span class="badge bg-warning fs-6">{{ $pendingUsers->total() }} in attesa</span>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Utente</th>
                        <th>Ruolo Richiesto</th>
                        <th>Registrato il</th>
                        <th class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingUsers as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;font-size:.75rem;">
                                    {{ strtoupper(substr($user->name,0,1)) }}{{ strtoupper(substr($user->surname,0,1)) }}
                                </div>
                                <div>
                                    <div class="fw-medium">{{ $user->full_name }}</div>
                                    <div class="text-muted small">{{ $user->email }}</div>
                                    @if($user->phone)<div class="text-muted small">{{ $user->phone }}</div>@endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @php $roleColors = ['admin'=>'danger','hr'=>'info','manager'=>'warning','employee'=>'secondary']; @endphp
                            <span class="badge bg-{{ $roleColors[$user->role] ?? 'secondary' }}">{{ ucfirst($user->role) }}</span>
                        </td>
                        <td class="text-muted small">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-end">
                            <form action="{{ route('employees.approve', $user) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-success" onclick="return confirm('Approvare l\'account di {{ $user->full_name }}?')">
                                    <i class="bi bi-check-lg me-1"></i>Approva
                                </button>
                            </form>
                            <form action="{{ route('employees.reject', $user) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Rifiutare l\'account di {{ $user->full_name }}?')">
                                    <i class="bi bi-x-lg me-1"></i>Rifiuta
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">
                            <i class="bi bi-check-circle display-6 d-block mb-2 text-success"></i>
                            Nessun account in attesa di approvazione.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($pendingUsers->hasPages())
    <div class="card-footer">{{ $pendingUsers->links() }}</div>
    @endif
</div>
@endsection
