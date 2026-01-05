@extends('layouts.admin')

@section('title', 'Rapports et Statistiques')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Filtrer les Rapports</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Date de début</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Date de fin</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date', now()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-search"></i> Filtrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="fw-semibold d-block mb-1">Total Commandes</span>
                        <h3 class="card-title mb-2">{{ $stats['total_orders'] }}</h3>
                    </div>
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="bx bx-cart-alt"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="fw-semibold d-block mb-1">Revenu Total</span>
                        <h3 class="card-title mb-2">{{ number_format($stats['total_revenue'], 0, ',', ' ') }} F</h3>
                    </div>
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-success">
                            <i class="bx bx-dollar"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="fw-semibold d-block mb-1">Payé</span>
                        <h3 class="card-title mb-2">{{ number_format($stats['total_paid'], 0, ',', ' ') }} F</h3>
                    </div>
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-info">
                            <i class="bx bx-check-circle"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="fw-semibold d-block mb-1">Nouveaux Clients</span>
                        <h3 class="card-title mb-2">{{ $stats['new_customers'] }}</h3>
                    </div>
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-warning">
                            <i class="bx bx-user-plus"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Services -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <h5 class="card-header">Top Services</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Quantité</th>
                            <th>Revenu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topServices as $service)
                        <tr>
                            <td>{{ $service->name }}</td>
                            <td><span class="badge bg-label-primary">{{ $service->total_quantity }}</span></td>
                            <td><strong>{{ number_format($service->total_revenue, 0, ',', ' ') }} F</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Customers -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <h5 class="card-header">Top Clients</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Commandes</th>
                            <th>Dépensé</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topCustomers as $customer)
                        <tr>
                            <td>{{ $customer->full_name }}</td>
                            <td><span class="badge bg-label-info">{{ $customer->order_count }}</span></td>
                            <td><strong>{{ number_format($customer->total_spent, 0, ',', ' ') }} F</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


