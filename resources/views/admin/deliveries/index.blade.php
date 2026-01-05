@extends('layouts.admin')

@section('title', 'Gestion des Livraisons')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Gestion des Livraisons</h5>
            </div>
            
            <div class="card-body">
                <form method="GET" action="{{ route('admin.deliveries.index') }}" class="row g-3 mb-4">
                    <div class="col-md-5">
                        <select name="status" class="form-select">
                            <option value="">Tous les statuts</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigné</option>
                            <option value="picked_up" {{ request('status') == 'picked_up' ? 'selected' : '' }}>Récupéré</option>
                            <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>En transit</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Livré</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Échoué</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <select name="driver_id" class="form-select">
                            <option value="">Tous les livreurs</option>
                            @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}" {{ request('driver_id') == $driver->id ? 'selected' : '' }}>
                                {{ $driver->full_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-search"></i> Filtrer
                        </button>
                    </div>
                </form>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>N° Commande</th>
                            <th>Client</th>
                            <th>Livreur</th>
                            <th>Adresse</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deliveries as $delivery)
                        <tr>
                            <td><strong>{{ $delivery->order->order_number }}</strong></td>
                            <td>{{ $delivery->order->client->full_name }}</td>
                            <td>
                                @if($delivery->driver)
                                    <span class="badge bg-label-success">{{ $delivery->driver->full_name }}</span>
                                @else
                                    <span class="badge bg-label-secondary">Non assigné</span>
                                @endif
                            </td>
                            <td>{{ Str::limit($delivery->order->deliveryAddress->address_line ?? 'N/A', 30) }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'secondary',
                                        'assigned' => 'info',
                                        'picked_up' => 'primary',
                                        'in_transit' => 'warning',
                                        'delivered' => 'success',
                                        'failed' => 'danger',
                                    ];
                                @endphp
                                <span class="badge bg-label-{{ $statusColors[$delivery->status] ?? 'secondary' }}">
                                    {{ $delivery->status }}
                                </span>
                            </td>
                            <td>{{ $delivery->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.deliveries.show', $delivery->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bx bx-show"></i> Détails
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Aucune livraison trouvée</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $deliveries->links() }}
            </div>
        </div>
    </div>
</div>
@endsection


