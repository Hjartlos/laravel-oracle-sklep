@extends('layouts.app')

@section('content')
    <div class="form-card">
        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-1"></i>
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif
        <div class="form-header">
            <i class="fas fa-bullseye"></i>
            <h2>{{ isset($amunicja) ? 'Edytuj Amunicję' : 'Dodaj Nową Amunicję' }}</h2>
        </div>

        <form action="{{ isset($amunicja) && isset($amunicja->amunicjaid) ? route('amunicja.update', $amunicja->amunicjaid) : route('amunicja.store') }}" method="POST">
            @csrf
            @if(isset($amunicja))
                @method('PUT')
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="nazwa"><i class="fas fa-tag me-1"></i> Nazwa</label>
                        <input type="text" name="nazwa" id="nazwa" class="form-control" value="{{ $amunicja->nazwa ?? '' }}" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="cena"><i class="fas fa-money-bill me-1"></i> Cena (zł)</label>
                        <input type="text" step="0.01" min="0.01" name="cena" id="cena" class="form-control" value="{{ $amunicja->cena ?? '' }}" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="ilosc"><i class="fas fa-cubes me-1"></i> Ilość</label>
                        <input type="number" min="0" name="ilosc" id="ilosc" class="form-control" value="{{ $amunicja->ilosc ?? '' }}" required>
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <a href="{{ route('amunicja.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Anuluj
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-{{ isset($amunicja) ? 'save' : 'plus' }} me-1"></i>
                    {{ isset($amunicja) ? 'Zapisz zmiany' : 'Dodaj amunicję' }}
                </button>
            </div>
        </form>
    </div>
@endsection
