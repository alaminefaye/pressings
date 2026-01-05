@extends('layouts.admin')

@section('title', 'Modifier le Type de Vêtement')

@section('content')
<div class="row">
    <div class="col-md-8 col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Modifier le Type de Vêtement</h5>
                <a href="{{ route('admin.clothing-types.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-arrow-back"></i> Retour
                </a>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ route('admin.clothing-types.update', $clothingType->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom du type <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $clothingType->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="icon" class="form-label">Icône (emoji)</label>
                        <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                               id="icon" name="icon" value="{{ old('icon', $clothingType->icon) }}" maxlength="10" placeholder="👕">
                        <div class="form-text">Ajoutez un emoji pour représenter ce type de vêtement (ex: 👔 👖 👗 🤵 🧥)</div>
                        @error('icon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description', $clothingType->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                   {{ old('is_active', $clothingType->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Actif</label>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> Mettre à jour
                        </button>
                        <a href="{{ route('admin.clothing-types.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">💡 Suggestions d'icônes</h6>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm icon-suggestion" onclick="document.getElementById('icon').value = this.textContent">👔</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm icon-suggestion" onclick="document.getElementById('icon').value = this.textContent">👖</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm icon-suggestion" onclick="document.getElementById('icon').value = this.textContent">👗</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm icon-suggestion" onclick="document.getElementById('icon').value = this.textContent">🤵</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm icon-suggestion" onclick="document.getElementById('icon').value = this.textContent">🧥</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm icon-suggestion" onclick="document.getElementById('icon').value = this.textContent">👘</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm icon-suggestion" onclick="document.getElementById('icon').value = this.textContent">👕</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm icon-suggestion" onclick="document.getElementById('icon').value = this.textContent">🧶</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm icon-suggestion" onclick="document.getElementById('icon').value = this.textContent">🛏️</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm icon-suggestion" onclick="document.getElementById('icon').value = this.textContent">🪟</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm icon-suggestion" onclick="document.getElementById('icon').value = this.textContent">🧦</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm icon-suggestion" onclick="document.getElementById('icon').value = this.textContent">👒</button>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h6 class="card-title">📊 Statistiques</h6>
                <p class="mb-2"><strong>Prix configurés:</strong> {{ $clothingType->prices()->count() }}</p>
                <p class="mb-0 text-muted small">
                    Ce type de vêtement a {{ $clothingType->prices()->count() }} prix configuré(s) pour différents services.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

