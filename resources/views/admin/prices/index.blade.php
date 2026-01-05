@extends('layouts.admin')

@section('title', 'Gestion des Tarifs')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Matrice des Tarifs</h5>
                <p class="text-muted mb-0">Modifiez les prix directement dans le tableau</p>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ route('admin.prices.update') }}">
                    @csrf
                    
                    @foreach($services as $service)
                    <div class="mb-4">
                        <h6 class="text-primary">{{ $service->name }}</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Type de vêtement</th>
                                        <th width="150">Prix (FCFA)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($prices[$service->id]))
                                        @foreach($prices[$service->id] as $price)
                                        <tr>
                                            <td>{{ $price->clothingType->name }}</td>
                                            <td>
                                                <input type="hidden" name="prices[{{ $loop->parent->index }}_{{ $loop->index }}][id]" value="{{ $price->id }}">
                                                <input type="number" 
                                                       name="prices[{{ $loop->parent->index }}_{{ $loop->index }}][price]" 
                                                       class="form-control form-control-sm" 
                                                       value="{{ $price->price }}" 
                                                       min="0" 
                                                       step="100">
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="2" class="text-center text-muted">Aucun prix configuré</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endforeach

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


