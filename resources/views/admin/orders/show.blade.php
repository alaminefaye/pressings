@extends('layouts.admin')

@section('title', 'Détails Commande #' . $order->order_number)

@section('content')
<div class="row">
    <!-- Order Details -->
    <div class="col-lg-8 mb-4">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Commande {{ $order->order_number }}</h5>
                @php
                    $statusColors = [
                        'pending' => 'secondary', 'received' => 'info', 'washing' => 'primary',
                        'ironing' => 'primary', 'ready' => 'success', 'in_delivery' => 'warning',
                        'delivered' => 'success', 'cancelled' => 'danger',
                    ];
                @endphp
                <span class="badge bg-{{ $statusColors[$order->status] }}">{{ $order->status }}</span>
            </div>
            <div class="card-body">
                <!-- Items -->
                <h6 class="text-muted">Articles</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Article</th>
                                <th>Qté</th>
                                <th>Prix Unit.</th>
                                <th>Sous-total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->service->name }}</td>
                                <td>{{ $item->clothingType->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->unit_price, 0, ',', ' ') }} F</td>
                                <td><strong>{{ number_format($item->subtotal, 0, ',', ' ') }} F</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Sous-total:</strong></td>
                                <td><strong>{{ number_format($order->subtotal, 0, ',', ' ') }} F</strong></td>
                            </tr>
                            @if($order->discount > 0)
                            <tr>
                                <td colspan="4" class="text-end">Réduction:</td>
                                <td class="text-success">-{{ number_format($order->discount, 0, ',', ' ') }} F</td>
                            </tr>
                            @endif
                            @if($order->delivery_fee > 0)
                            <tr>
                                <td colspan="4" class="text-end">Frais de livraison:</td>
                                <td>{{ number_format($order->delivery_fee, 0, ',', ' ') }} F</td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                <td><strong class="text-primary">{{ number_format($order->total, 0, ',', ' ') }} F</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($order->special_instructions)
                <div class="alert alert-info">
                    <strong>Instructions spéciales:</strong><br>
                    {{ $order->special_instructions }}
                </div>
                @endif

                <!-- Status History -->
                <h6 class="text-muted mt-4">Historique des statuts</h6>
                <div class="timeline">
                    @foreach($order->statusHistory as $history)
                    <div class="timeline-item mb-3">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <span class="badge bg-label-primary rounded-pill">
                                    {{ $history->created_at->format('H:i') }}
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $history->new_status }}</h6>
                                <p class="mb-0">Par {{ $history->user->full_name }}</p>
                                @if($history->comment)
                                <small class="text-muted">{{ $history->comment }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Client Info -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Informations Client</h6>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>{{ $order->client->full_name }}</strong></p>
                <p class="mb-2">
                    <i class="bx bx-phone"></i> {{ $order->client->phone }}
                </p>
                @if($order->client->email)
                <p class="mb-0">
                    <i class="bx bx-envelope"></i> {{ $order->client->email }}
                </p>
                @endif
            </div>
        </div>

        <!-- Change Status -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Changer le statut</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.orders.update-status', $order->id) }}">
                    @csrf
                    <div class="mb-3">
                        <select name="status" class="form-select" required>
                            <option value="">Sélectionner...</option>
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="received" {{ $order->status == 'received' ? 'selected' : '' }}>Reçu</option>
                            <option value="washing" {{ $order->status == 'washing' ? 'selected' : '' }}>Lavage</option>
                            <option value="ironing" {{ $order->status == 'ironing' ? 'selected' : '' }}>Repassage</option>
                            <option value="ready" {{ $order->status == 'ready' ? 'selected' : '' }}>Prêt</option>
                            <option value="in_delivery" {{ $order->status == 'in_delivery' ? 'selected' : '' }}>En livraison</option>
                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Livré</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <textarea name="comment" class="form-control" rows="3" placeholder="Commentaire (optionnel)"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-check"></i> Mettre à jour
                    </button>
                </form>
            </div>
        </div>

        <!-- Assign Employee -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Assigner un employé</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.orders.assign', $order->id) }}">
                    @csrf
                    <div class="mb-3">
                        <select name="employee_id" class="form-select" required>
                            <option value="">Sélectionner...</option>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ $order->employee_id == $employee->id ? 'selected' : '' }}>
                                {{ $employee->full_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-user-check"></i> Assigner
                    </button>
                </form>
            </div>
        </div>

        <!-- Payment Info -->
        @if($order->payment)
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Paiement</h6>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>Méthode:</strong> {{ $order->payment->payment_method }}
                </p>
                <p class="mb-2">
                    <strong>Montant:</strong> {{ number_format($order->payment->amount, 0, ',', ' ') }} F
                </p>
                <p class="mb-0">
                    <strong>Statut:</strong>
                    <span class="badge bg-label-{{ $order->payment->status == 'completed' ? 'success' : 'warning' }}">
                        {{ $order->payment->status }}
                    </span>
                </p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection


