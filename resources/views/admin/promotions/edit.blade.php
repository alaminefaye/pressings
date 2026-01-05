@extends('layouts.admin')
@section('title', 'Modifier Promotion')
@section('content')
<div class="card">
    <div class="card-header"><h5>Modifier la promotion {{ $promotion->code }}</h5></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.promotions.update', $promotion->id) }}">
            @csrf
            @method('PUT')
            <div class="row mb-3"><div class="col-md-6"><label class="form-label">Code *</label><input type="text" name="code" class="form-control" value="{{ $promotion->code }}" required></div><div class="col-md-6"><label class="form-label">Type *</label><select name="type" class="form-select" required><option value="percentage" {{ $promotion->type == 'percentage' ? 'selected' : '' }}>Pourcentage</option><option value="fixed" {{ $promotion->type == 'fixed' ? 'selected' : '' }}>Montant fixe</option></select></div></div>
            <div class="row mb-3"><div class="col-md-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2">{{ $promotion->description }}</textarea></div></div>
            <div class="row mb-3"><div class="col-md-4"><label class="form-label">Valeur *</label><input type="number" name="value" class="form-control" value="{{ $promotion->value }}" step="0.01" required></div><div class="col-md-4"><label class="form-label">Montant min commande</label><input type="number" name="min_order_amount" class="form-control" value="{{ $promotion->min_order_amount }}"></div><div class="col-md-4"><label class="form-label">Réduction max</label><input type="number" name="max_discount_amount" class="form-control" value="{{ $promotion->max_discount_amount }}"></div></div>
            <div class="row mb-3"><div class="col-md-4"><label class="form-label">Utilisations max</label><input type="number" name="max_uses" class="form-control" value="{{ $promotion->max_uses }}"></div><div class="col-md-4"><label class="form-label">Max par utilisateur</label><input type="number" name="max_uses_per_user" class="form-control" value="{{ $promotion->max_uses_per_user }}"></div><div class="col-md-4"><div class="form-check form-switch mt-4"><input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $promotion->is_active ? 'checked' : '' }}><label class="form-check-label">Active</label></div></div></div>
            <div class="row mb-3"><div class="col-md-6"><label class="form-label">Date début *</label><input type="date" name="start_date" class="form-control" value="{{ $promotion->start_date }}" required></div><div class="col-md-6"><label class="form-label">Date fin *</label><input type="date" name="end_date" class="form-control" value="{{ $promotion->end_date }}" required></div></div>
            <div class="d-flex justify-content-between"><a href="{{ route('admin.promotions.index') }}" class="btn btn-outline-secondary">Retour</a><button type="submit" class="btn btn-primary">Enregistrer</button></div>
        </form>
    </div>
</div>
@endsection
