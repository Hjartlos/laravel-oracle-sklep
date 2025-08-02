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
            <i class="fas fa-map-marked-alt"></i>
            <h2>{{ isset($adres) ? 'Edytuj Adres' : 'Dodaj Nowy Adres' }}</h2>
        </div>

            <form action="{{ isset($adres) && isset($adres->adresid) ? route('adres.update', $adres->adresid) : route('adres.store') }}" method="POST">
            @csrf
            @if(isset($adres))
                @method('PUT')
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="ulica"><i class="fas fa-road me-1"></i> Ulica</label>
                        <input type="text" name="ulica" id="ulica" class="form-control" value="{{ $adres->ulica ?? '' }}" required>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="numerDomu"><i class="fas fa-home me-1"></i> Numer Domu</label>
                        <input type="text" name="numerDomu" id="numerDomu" class="form-control" value="{{ $adres->numerdomu ?? '' }}" required>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="numerMieszkania"><i class="fas fa-door-closed me-1"></i> Numer Mieszkania</label>
                        <input type="text" name="numerMieszkania" id="numerMieszkania" class="form-control" value="{{ $adres->numermieszkania ?? '' }}">
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="kodPocztowy"><i class="fas fa-mail-bulk me-1"></i> Kod Pocztowy</label>
                        <input type="text" name="kodPocztowy" id="kodPocztowy" class="form-control" value="{{ $adres->kodpocztowy ?? '' }}" required pattern="[0-9]{2}-[0-9]{3}" placeholder="XX-XXX">
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="form-group">
                        <label for="miejscowosc"><i class="fas fa-city me-1"></i> Miejscowość</label>
                        <input type="text" name="miejscowosc" id="miejscowosc" class="form-control" value="{{ $adres->miejscowosc ?? '' }}" required>
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <a href="{{ route('adres.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Anuluj
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-{{ isset($adres) ? 'save' : 'plus' }} me-1"></i>
                    {{ isset($adres) ? 'Zapisz zmiany' : 'Dodaj adres' }}
                </button>
            </div>
        </form>
    </div>
@endsection
