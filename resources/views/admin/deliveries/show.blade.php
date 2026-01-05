@extends('layouts.admin')
@section('title', 'Détails Livraison')
@section('content')
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header"><h5>Livraison #{{ $delivery->id }} - Commande {{ $delivery->order->order_number }}</h5></div>
            <div class="card-body">
                <h6>Informations commande</h6>
                <p><strong>Client:</strong> {{ $delivery->order->client->full_name }}</p>
                <p><strong>Téléphone:</strong> {{ $delivery->order->client->phone }}</p>
                <p><strong>Adresse:</strong> {{ $delivery->order->deliveryAddress->address_line ?? 'N/A' }}</p>
                <p><strong>Montant:</strong> {{ number_format($delivery->order->total, 0, ',', ' ') }} F</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header"><h6>Assigner un livreur</h6></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.deliveries.assign', $delivery->id) }}">
                    @csrf
                    <select name="driver_id" class="form-select mb-3" required>
                        @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}" {{ $delivery->driver_id == $driver->id ? 'selected' : '' }}>{{ $driver->full_name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary w-100">Assigner</button>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h6>Changer le statut</h6></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.deliveries.update-status', $delivery->id) }}">
                    @csrf
                    <select name="status" class="form-select mb-3" required>
                        <option value="pending" {{ $delivery->status == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="assigned" {{ $delivery->status == 'assigned' ? 'selected' : '' }}>Assigné</option>
                        <option value="picked_up" {{ $delivery->status == 'picked_up' ? 'selected' : '' }}>Récupéré</option>
                        <option value="in_transit" {{ $delivery->status == 'in_transit' ? 'selected' : '' }}>En transit</option>
                        <option value="delivered" {{ $delivery->status == 'delivered' ? 'selected' : '' }}>Livré</option>
                        <option value="failed" {{ $delivery->status == 'failed' ? 'selected' : '' }}>Échoué</option>
                    </select>
                    <button type="submit" class="btn btn-primary w-100">Mettre à jour</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
