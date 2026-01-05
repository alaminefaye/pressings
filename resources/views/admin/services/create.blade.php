@extends('layouts.admin')

@section('title', 'Nouveau Service')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Créer un nouveau service</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.services.store') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom du service *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="duration_hours" class="form-label">Durée (en heures) *</label>
                        <input type="number" class="form-control @error('duration_hours') is-invalid @enderror" 
                               id="duration_hours" name="duration_hours" value="{{ old('duration_hours', 24) }}" 
                               min="1" required>
                        @error('duration_hours')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" 
                                   id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Service actif
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back"></i> Retour
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> Créer le service
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


