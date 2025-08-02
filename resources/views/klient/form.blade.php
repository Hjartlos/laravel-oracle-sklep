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
            <i class="fas fa-users"></i>
            <h2>{{ isset($klient) ? 'Edytuj Klienta' : 'Dodaj Nowego Klienta' }}</h2>
        </div>

        <form action="{{ isset($klient) && isset($klient->klientid) ? route('klient.update', $klient->klientid) : route('klient.store') }}" method="POST">
            @csrf
            @if(isset($klient))
                @method('PUT')
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="imie"><i class="fas fa-user me-1"></i> Imię</label>
                        <input type="text" name="imie" id="imie" class="form-control" value="{{ $klient->imie ?? old('imie') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nazwisko"><i class="fas fa-user me-1"></i> Nazwisko</label>
                        <input type="text" name="nazwisko" id="nazwisko" class="form-control" value="{{ $klient->nazwisko ?? old('nazwisko') }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="numerPozwolenia"><i class="fas fa-id-card me-1"></i> Numer Pozwolenia</label>
                        <input type="text" name="numerPozwolenia" id="numerPozwolenia" class="form-control" value="{{ $klient->numerpozwolenia ?? old('numerPozwolenia') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="typPozwolenia"><i class="fas fa-certificate me-1"></i> Typ Pozwolenia</label>
                        <input type="text" name="typPozwolenia" id="typPozwolenia" class="form-control" value="{{ $klient->typpozwolenia ?? old('typPozwolenia') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="dataWaznosci"><i class="fas fa-calendar me-1"></i> Data Ważności *</label>
                        <input type="date" name="dataWaznosci" id="dataWaznosci" class="form-control" value="{{ isset($klient) ? \Carbon\Carbon::parse($klient->datawaznoscipozwolenia)->format('Y-m-d') : old('dataWaznosci') }}" min="{{ date('Y-m-d') }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope me-1"></i> Email</label>
                        <input type="text" name="email" id="email" class="form-control" value="{{ $klient->email ?? old('email') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="telefon"><i class="fas fa-phone me-1"></i> Telefon</label>
                        <input type="text" name="telefon" id="telefon" class="form-control" value="{{ $klient->telefon ?? old('telefon') }}">
                    </div>
                </div>
            </div>

            @if(isset($klient))
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="statusAktywnosci"><i class="fas fa-toggle-on me-1"></i> Status aktywności</label>
                            <select name="statusAktywnosci" id="statusAktywnosci" class="form-control">
                                <option value="1" {{ (isset($klient) && $klient->statusaktywnosci == 1) ? 'selected' : '' }}>Aktywny</option>
                                <option value="0" {{ (isset($klient) && $klient->statusaktywnosci == 0) ? 'selected' : '' }}>Nieaktywny</option>
                            </select>
                        </div>
                    </div>
                </div>
            @else
                <input type="hidden" name="statusAktywnosci" value="1">
            @endif

            <hr>
            <h4><i class="fas fa-map-marker-alt me-1"></i> Dane adresowe</h4>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="ulica"><i class="fas fa-road me-1"></i> Ulica</label>
                        <input type="text" name="ulica" id="ulica" class="form-control" value="{{ $adres->ulica ?? old('ulica') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="numerDomu"><i class="fas fa-home me-1"></i> Numer Domu</label>
                        <input type="text" name="numerDomu" id="numerDomu" class="form-control" value="{{ $adres->numerdomu ?? old('numerDomu') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="numerMieszkania"><i class="fas fa-door-closed me-1"></i> Numer Mieszkania</label>
                        <input type="text" name="numerMieszkania" id="numerMieszkania" class="form-control" value="{{ $adres->numermieszkania ?? old('numerMieszkania') }}">
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="kodPocztowy"><i class="fas fa-mail-bulk me-1"></i> Kod Pocztowy</label>
                        <input type="text" name="kodPocztowy" id="kodPocztowy" class="form-control" value="{{ $adres->kodpocztowy ?? old('kodPocztowy') }}">
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="miejscowosc"><i class="fas fa-city me-1"></i> Miejscowość</label>
                        <input type="text" name="miejscowosc" id="miejscowosc" class="form-control" value="{{ $adres->miejscowosc ?? old('miejscowosc') }}">
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <a href="{{ route('klient.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Anuluj
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-{{ isset($klient) ? 'save' : 'plus' }} me-1"></i>
                    {{ isset($klient) ? 'Zapisz zmiany' : 'Dodaj klienta' }}
                </button>
            </div>
        </form>
    </div>
@endsection
