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
            <i class="fas fa-user-tie"></i>
            <h2>{{ isset($pracownik) ? 'Edytuj Pracownika' : 'Dodaj Nowego Pracownika' }}</h2>
        </div>

        <form action="{{ isset($pracownik) && isset($pracownik->pracownikid) ? route('pracownik.update', $pracownik->pracownikid) : route('pracownik.store') }}" method="POST">
            @csrf
            @if(isset($pracownik))
                @method('PUT')
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="imie"><i class="fas fa-user me-1"></i> Imię</label>
                        <input type="text" name="imie" id="imie" class="form-control" value="{{ $pracownik->imie ?? '' }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nazwisko"><i class="fas fa-user me-1"></i> Nazwisko</label>
                        <input type="text" name="nazwisko" id="nazwisko" class="form-control" value="{{ $pracownik->nazwisko ?? '' }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="stanowisko"><i class="fas fa-briefcase me-1"></i> Stanowisko</label>
                        <input type="text" name="stanowisko" id="stanowisko" class="form-control" value="{{ $pracownik->stanowisko ?? old('stanowisko') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="typPozwolenia"><i class="fas fa-certificate me-1"></i> Typ Pozwolenia</label>
                        <input type="text" name="typPozwolenia" id="typPozwolenia" class="form-control" value="{{ $pracownik->typpozwolenia ?? old('typPozwolenia') }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="login"><i class="fas fa-user-circle me-1"></i> Login</label>
                        <input type="text" name="login" id="login" class="form-control" value="{{ $pracownik->login ?? old('login') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="haslo"><i class="fas fa-key me-1"></i> Hasło</label>
                        <input type="text" name="haslo" id="haslo" class="form-control" {{ isset($pracownik) ?? '' }}>
                        @if(isset($pracownik))
                            <small class="form-text text-muted">Pozostaw puste, aby nie zmieniać hasła</small>
                        @endif
                    </div>
                </div>
            </div>

            @if(isset($pracownik))
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="statusAktywnosci"><i class="fas fa-toggle-on me-1"></i> Status aktywności</label>
                            <select name="statusAktywnosci" id="statusAktywnosci" class="form-control">
                                <option value="1" {{ (isset($pracownik) && $pracownik->statusaktywnosci == 1) ? 'selected' : '' }}>Aktywny</option>
                                <option value="0" {{ (isset($pracownik) && $pracownik->statusaktywnosci == 0) ? 'selected' : '' }}>Nieaktywny</option>
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
                        <input type="text" name="ulica" id="ulica" class="form-control" value="{{ $adres->ulica ?? '' }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="numerDomu"><i class="fas fa-home me-1"></i> Numer Domu</label>
                        <input type="text" name="numerDomu" id="numerDomu" class="form-control" value="{{ $adres->numerdomu ?? '' }}">
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
                        <input type="text" name="kodPocztowy" id="kodPocztowy" class="form-control" value="{{ $adres->kodpocztowy ?? '' }}">
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="miejscowosc"><i class="fas fa-city me-1"></i> Miejscowość</label>
                        <input type="text" name="miejscowosc" id="miejscowosc" class="form-control" value="{{ $adres->miejscowosc ?? '' }}">
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <a href="{{ route('pracownik.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Anuluj
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-{{ isset($pracownik) ? 'save' : 'plus' }} me-1"></i>
                    {{ isset($pracownik) ? 'Zapisz zmiany' : 'Dodaj pracownika' }}
                </button>
            </div>
        </form>
    </div>
@endsection
