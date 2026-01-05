@extends('layouts.admin')

@section('title', 'Paramètres')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Paramètres de l'Application</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf
                    
                    @foreach($settings as $type => $groupSettings)
                    <div class="mb-4">
                        <h6 class="text-primary">{{ ucfirst($type) }}</h6>
                        <hr>
                        @foreach($groupSettings as $setting)
                        <div class="row mb-3">
                            <label class="col-md-3 col-form-label">{{ $setting->description ?? $setting->key }}</label>
                            <div class="col-md-9">
                                @if($setting->type === 'boolean')
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="settings[{{ $setting->key }}]" value="1" {{ $setting->value ? 'checked' : '' }}>
                                    </div>
                                @elseif($setting->type === 'text')
                                    <textarea name="settings[{{ $setting->key }}]" class="form-control" rows="3">{{ $setting->value }}</textarea>
                                @else
                                    <input type="{{ $setting->type }}" name="settings[{{ $setting->key }}]" class="form-control" value="{{ $setting->value }}">
                                @endif
                            </div>
                        </div>
                        @endforeach
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


