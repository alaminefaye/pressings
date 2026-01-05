@extends('layouts.admin')
@section('title', 'Nouvel Employé')
@section('content')
<div class="card">
    <div class="card-header"><h5>Créer un nouvel employé</h5></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.employees.store') }}">
            @csrf
            <div class="row mb-3"><div class="col-md-6"><label class="form-label">Prénom *</label><input type="text" name="first_name" class="form-control" required></div><div class="col-md-6"><label class="form-label">Nom *</label><input type="text" name="last_name" class="form-control" required></div></div>
            <div class="row mb-3"><div class="col-md-6"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" required></div><div class="col-md-6"><label class="form-label">Téléphone *</label><input type="text" name="phone" class="form-control" required></div></div>
            <div class="row mb-3"><div class="col-md-6"><label class="form-label">Mot de passe *</label><input type="password" name="password" class="form-control" required></div><div class="col-md-6"><label class="form-label">Rôle *</label><select name="role" class="form-select" required><option value="employee">Employé</option><option value="admin">Admin</option></select></div></div>
            <div class="d-flex justify-content-between"><a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary"><i class="bx bx-arrow-back"></i> Retour</a><button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Créer</button></div>
        </form>
    </div>
</div>
@endsection
