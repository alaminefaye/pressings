@extends('layouts.admin')

@section('title', 'Profil Client - ' . $customer->full_name)

@section('content')
<div class="row">
    <!-- Customer Profile -->
    <div class="col-lg-4 mb-4">
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="avatar avatar-xl mx-auto mb-3">
                    <span class="avatar-initial rounded-circle bg-label-primary fs-2">
                        {{ strtoupper(substr($customer->first_name, 0, 1)) }}{{ strtoupper(substr($customer->last_name, 0, 1)) }}
                    </span>
                </div>
                <h5 class="mb-1">{{ $customer->full_name }}</h5>
                <span class="badge bg-label-primary mb-3">Client</span>
                
                <div class="d-flex justify-content-around my-4 py-3 border-top border-bottom">
                    <div>
                        <h4 class="mb-1">{{ $customer->orders->count() }}</h4>
                        <span class="text-muted">Commandes</span>
                    </div>
                    <div>
                        <h4 class="mb-1">{{ number_format($customer->orders->sum('total'), 0) }} F</h4>
                        <span class="text-muted">Total dépensé</span>
                    </div>
                </div>

                <div class="text-start mb-2">
                    <p class="mb-2">
                        <i class="bx bx-phone me-2"></i>
                        <span>{{ $customer->phone }}</span>
                    </p>
                    @if($customer->email)
                    <p class="mb-2">
                        <i class="bx bx-envelope me-2"></i>
                        <span>{{ $customer->email }}</span>
                    </p>
                    @endif
                    <p class="mb-0">
                        <i class="bx bx-calendar me-2"></i>
                        <span>Client depuis {{ $customer->created_at->format('d/m/Y') }}</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Loyalty Points -->
        @if($customer->loyaltyPoints)
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Points de Fidélité</h6>
                <h3 class="text-primary">{{ $customer->loyaltyPoints->points }} points</h3>
                <small class="text-muted">
                    Valeur: {{ number_format($customer->loyaltyPoints->points * 10, 0) }} FCFA
                </small>
            </div>
        </div>
        @endif
    </div>

    <!-- Orders History -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Historique des Commandes</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>N° Commande</th>
                            <th>Date</th>
                            <th>Articles</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                        <tr>
                            <td><strong>{{ $order->order_number }}</strong></td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
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
                                @endphp
                                <span class="badge bg-label-{{ $statusColors[$order->status] }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bx bx-show"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Aucune commande</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


