@extends('layouts.admin')

@section('title', 'Gestion des Types de Vêtements')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Gestion des Types de Vêtements</h5>
                <a href="{{ route('admin.clothing-types.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus"></i> Nouveau Type de Vêtement
                </a>
            </div>
            
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Icône</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Prix configurés</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clothingTypes as $type)
                        <tr>
                            <td><span style="font-size: 24px;">{{ $type->icon ?? '👕' }}</span></td>
                            <td><strong>{{ $type->name }}</strong></td>
                            <td>{{ Str::limit($type->description, 50) }}</td>
                            <td>
                                <span class="badge bg-label-info">{{ $type->prices_count }} prix</span>
                            </td>
                            <td>
                                @if($type->is_active)
                                    <span class="badge bg-label-success">Actif</span>
                                @else
                                    <span class="badge bg-label-secondary">Inactif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.clothing-types.edit', $type->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.clothing-types.destroy', $type->id) }}" style="display:inline-block;">
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
                            <td colspan="6" class="text-center py-4">
                                <p class="text-muted mb-3">Aucun type de vêtement trouvé</p>
                                <a href="{{ route('admin.clothing-types.create') }}" class="btn btn-primary">
                                    <i class="bx bx-plus"></i> Créer le premier type
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if(count($clothingTypes) == 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-primary">
            <div class="card-body">
                <h5 class="card-title text-primary">🚀 Commencez rapidement</h5>
                <p class="card-text">Ajoutez les types de vêtements de base pour votre pressing :</p>
                <p class="text-muted mb-3">
                    Exemples : Chemise 👔, Pantalon 👖, Robe 👗, Costume 🤵, Veste 🧥, Jupe 👘, T-shirt 👕, Pull 🧶, Manteau 🧥, Jean 👖
                </p>
                <a href="{{ route('admin.clothing-types.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus"></i> Ajouter un type de vêtement
                </a>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

