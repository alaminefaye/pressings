@extends('layouts.admin')

@section('title', 'Gestion des Employés')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Gestion des Employés</h5>
                <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus"></i> Nouvel Employé
                </a>
            </div>
            
            <div class="card-body">
                <form method="GET" action="{{ route('admin.employees.index') }}" class="row g-3 mb-4">
                    <div class="col-md-9">
                        <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bx bx-search"></i> Rechercher
                        </button>
                        <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-reset"></i>
                        </a>
                    </div>
                </form>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Employé</th>
                            <th>Téléphone</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Commandes</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            {{ strtoupper(substr($employee->first_name, 0, 1)) }}{{ strtoupper(substr($employee->last_name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <strong>{{ $employee->full_name }}</strong>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $employee->phone }}</td>
                            <td>{{ $employee->email }}</td>
                            <td>
                                <span class="badge bg-label-{{ $employee->role === 'admin' ? 'danger' : 'info' }}">
                                    {{ ucfirst($employee->role) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-label-primary">{{ $employee->assigned_orders_count }} commandes</span>
                            </td>
                            <td>
                                @if($employee->is_active)
                                    <span class="badge bg-label-success">Actif</span>
                                @else
                                    <span class="badge bg-label-secondary">Inactif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bx bx-edit"></i>
                                </a>
                                @if($employee->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.employees.destroy', $employee->id) }}" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr?')">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Aucun employé trouvé</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $employees->links() }}
            </div>
        </div>
    </div>
</div>
@endsection


