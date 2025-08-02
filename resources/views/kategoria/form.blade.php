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
            <i class="fas fa-tags"></i>
            <h2>{{ isset($kategoria) ? 'Edytuj Kategorię' : 'Dodaj Nową Kategorię' }}</h2>
        </div>

        <form action="{{ isset($kategoria) && isset($kategoria->kategoriaid) ? route('kategoria.update', $kategoria->kategoriaid) : route('kategoria.store') }}" method="POST">
            @csrf
            @if(isset($kategoria))
                @method('PUT')
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="nazwakategorii"><i class="fas fa-tag me-1"></i> Nazwa kategorii</label>
                        <input type="text" name="nazwakategorii" id="nazwakategorii" class="form-control" value="{{ $kategoria->nazwakategorii ?? '' }}" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="opis"><i class="fas fa-info-circle me-1"></i> Opis</label>
                        <textarea name="opis" id="opis" class="form-control" rows="4">{{ $kategoria->opis ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="wymaganeuprawnienia"><i class="fas fa-lock me-1"></i> Wymagane uprawnienia</label>
                        <input type="text" name="wymaganeuprawnienia" id="wymaganeuprawnienia" class="form-control" value="{{ $kategoria->wymaganeuprawnienia ?? '' }}">
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <a href="{{ route('kategoria.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Anuluj
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-{{ isset($kategoria) ? 'save' : 'plus' }} me-1"></i>
                    {{ isset($kategoria) ? 'Zapisz zmiany' : 'Dodaj kategorię' }}
                </button>
            </div>
        </form>
    </div>
@endsection
