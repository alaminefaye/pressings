@extends('layouts.admin')

@section('title', 'Gestion des Services')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Gestion des Services</h5>
                <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus"></i> Nouveau Service
                </a>
            </div>
            
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Durée</th>
                            <th>Prix configurés</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                        <tr>
                            <td><strong>{{ $service->name }}</strong></td>
                            <td>{{ Str::limit($service->description, 50) }}</td>
                            <td>{{ $service->duration_hours }}h</td>
                            <td>
                                <span class="badge bg-label-info">{{ $service->prices_count }} prix</span>
                            </td>
                            <td>
                                @if($service->is_active)
                                    <span class="badge bg-label-success">Actif</span>
                                @else
                                    <span class="badge bg-label-secondary">Inactif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.services.edit', $service->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.services.destroy', $service->id) }}" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr?')">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Aucun service trouvé</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


