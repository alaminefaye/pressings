@extends('layouts.admin')

@section('title', 'Gestion des Commandes')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Gestion des Commandes</h5>
            </div>
            
            <!-- Filters -->
            <div class="card-body">
                <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">Tous les statuts</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Reçu</option>
                            <option value="washing" {{ request('status') == 'washing' ? 'selected' : '' }}>Lavage</option>
                            <option value="ironing" {{ request('status') == 'ironing' ? 'selected' : '' }}>Repassage</option>
                            <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Prêt</option>
                            <option value="in_delivery" {{ request('status') == 'in_delivery' ? 'selected' : '' }}>En livraison</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Livré</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bx bx-search"></i> Filtrer
                        </button>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-reset"></i> Réinitialiser
                        </a>
                    </div>
                </form>
            </div>

            <!-- Orders Table -->
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>N° Commande</th>
                            <th>Client</th>
                            <th>Articles</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Employé</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td><strong>{{ $order->order_number }}</strong></td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">{{ $order->client->full_name }}</span>
                                    <small class="text-muted">{{ $order->client->phone }}</small>
                                </div>
                            </td>
                            <td>{{ $order->items->count() }} article(s)</td>
                            <td><strong>{{ number_format($order->total, 0, ',', ' ') }} F</strong></td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'secondary',
                                        'received' => 'info',
                                        'washing' => 'primary',
                                        'ironing' => 'primary',
                                        'ready' => 'success',
                                        'in_delivery' => 'warning',
                                        'delivered' => 'success',
                                        'cancelled' => 'danger',
                                    ];
                                    $statusLabels = [
                                        'pending' => 'En attente',
                                        'received' => 'Reçu',
                                        'washing' => 'Lavage',
                                        'ironing' => 'Repassage',
                                        'ready' => 'Prêt',
                                        'in_delivery' => 'En livraison',
                                        'delivered' => 'Livré',
                                        'cancelled' => 'Annulé',
                                    ];
                                @endphp
                                <span class="badge bg-label-{{ $statusColors[$order->status] }}">
                                    {{ $statusLabels[$order->status] }}
                                </span>
                            </td>
                            <td>
                                @if($order->employee)
                                    <span class="badge bg-label-info">{{ $order->employee->full_name }}</span>
                                @else
                                    <span class="badge bg-label-secondary">Non assigné</span>
                                @endif
                            </td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bx bx-show"></i> Détails
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Aucune commande trouvée</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="card-footer">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection


