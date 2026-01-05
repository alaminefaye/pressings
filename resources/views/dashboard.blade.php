@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-12 mb-4 order-0">
        <div class="card">
            <div class="d-flex align-items-end row">
                <div class="col-sm-7">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Bienvenue {{ auth()->user()->first_name }} ! 🎉</h5>
                        <p class="mb-4">
                            Vous avez <span class="fw-bold">{{ $stats['orders']['pending'] }} commandes en attente</span> aujourd'hui.
                        </p>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">Voir les commandes</a>
                    </div>
                </div>
                <div class="col-sm-5 text-center text-sm-left">
                    <div class="card-body pb-0 px-0 px-md-4">
                        <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" height="140" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <!-- Today's Orders -->
    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="bx bx-cart-alt"></i>
                        </span>
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1">Commandes du jour</span>
                <h3 class="card-title mb-2">{{ $stats['orders']['today'] }}</h3>
                <small class="text-success fw-semibold">
                    <i class="bx bx-up-arrow-alt"></i> Aujourd'hui
                </small>
            </div>
        </div>
    </div>

    <!-- Revenue Today -->
    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-success">
                            <i class="bx bx-dollar"></i>
                        </span>
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1">Revenu du jour</span>
                <h3 class="card-title mb-2">{{ number_format($stats['revenue']['today'], 0, ',', ' ') }} F</h3>
                <small class="text-success fw-semibold">
                    <i class="bx bx-up-arrow-alt"></i> Aujourd'hui
                </small>
            </div>
        </div>
    </div>

    <!-- Pending Orders -->
    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-warning">
                            <i class="bx bx-time"></i>
                        </span>
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1">En attente</span>
                <h3 class="card-title mb-2">{{ $stats['orders']['pending'] }}</h3>
                <small class="text-danger fw-semibold">
                    <i class="bx bx-down-arrow-alt"></i> À traiter
                </small>
            </div>
        </div>
    </div>

    <!-- Total Customers -->
    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-info">
                            <i class="bx bx-user"></i>
                        </span>
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1">Clients</span>
                <h3 class="card-title mb-2">{{ $stats['customers']['total'] }}</h3>
                <small class="text-success fw-semibold">
                    <i class="bx bx-up-arrow-alt"></i> +{{ $stats['customers']['new_today'] }} aujourd'hui
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <h5 class="card-header">Commandes récentes</h5>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>N° Commande</th>
                            <th>Client</th>
                            <th>Articles</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($recentOrders as $order)
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
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bx bx-show"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Aucune commande récente</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endpush
