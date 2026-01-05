@extends('layouts.admin')
@section('title', 'Modifier Employé')
@section('content')
<div class="card">
    <div class="card-header"><h5>Modifier {{ $employee->full_name }}</h5></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.employees.update', $employee->id) }}">
            @csrf
            @method('PUT')
            <div class="row mb-3"><div class="col-md-6"><label class="form-label">Prénom *</label><input type="text" name="first_name" class="form-control" value="{{ $employee->first_name }}" required></div><div class="col-md-6"><label class="form-label">Nom *</label><input type="text" name="last_name" class="form-control" value="{{ $employee->last_name }}" required></div></div>
            <div class="row mb-3"><div class="col-md-6"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" value="{{ $employee->email }}" required></div><div class="col-md-6"><label class="form-label">Téléphone *</label><input type="text" name="phone" class="form-control" value="{{ $employee->phone }}" required></div></div>
            <div class="row mb-3"><div class="col-md-6"><label class="form-label">Nouveau mot de passe (optionnel)</label><input type="password" name="password" class="form-control"></div><div class="col-md-6"><label class="form-label">Rôle *</label><select name="role" class="form-select" required><option value="employee" {{ $employee->role == 'employee' ? 'selected' : '' }}>Employé</option><option value="admin" {{ $employee->role == 'admin' ? 'selected' : '' }}>Admin</option></select></div></div>
            <div class="mb-3"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $employee->is_active ? 'checked' : '' }}><label class="form-check-label">Actif</label></div></div>
            <div class="d-flex justify-content-between"><a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary"><i class="bx bx-arrow-back"></i> Retour</a><button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Enregistrer</button></div>
        </form>
    </div>
</div>
@endsection
