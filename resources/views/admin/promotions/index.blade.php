@extends('layouts.admin')

@section('title', 'Gestion des Promotions')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Gestion des Promotions</h5>
                <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus"></i> Nouvelle Promotion
                </a>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Valeur</th>
                            <th>Utilisations</th>
                            <th>Période</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($promotions as $promotion)
                        <tr>
                            <td><strong>{{ $promotion->code }}</strong></td>
                            <td>{{ Str::limit($promotion->description, 30) }}</td>
                            <td>
                                <span class="badge bg-label-{{ $promotion->type === 'percentage' ? 'info' : 'success' }}">
                                    {{ $promotion->type === 'percentage' ? 'Pourcentage' : 'Montant fixe' }}
                                </span>
                            </td>
                            <td>
                                <strong>
                                    {{ $promotion->type === 'percentage' ? $promotion->value . '%' : number_format($promotion->value, 0, ',', ' ') . ' F' }}
                                </strong>
                            </td>
                            <td>
                                <span class="badge bg-label-primary">
                                    {{ $promotion->usages_count }} / {{ $promotion->max_uses ?? '∞' }}
                                </span>
                            </td>
                            <td>
                                <small>
                                    {{ \Carbon\Carbon::parse($promotion->start_date)->format('d/m/Y') }} - 
                                    {{ \Carbon\Carbon::parse($promotion->end_date)->format('d/m/Y') }}
                                </small>
                            </td>
                            <td>
                                @if($promotion->is_active && now()->between($promotion->start_date, $promotion->end_date))
                                    <span class="badge bg-label-success">Active</span>
                                @else
                                    <span class="badge bg-label-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.promotions.edit', $promotion->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.promotions.destroy', $promotion->id) }}" style="display:inline-block;">
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
                            <td colspan="8" class="text-center">Aucune promotion trouvée</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $promotions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection


