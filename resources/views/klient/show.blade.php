@extends('layouts.app')

@section('content')
    <div class="content-card">
        <div class="form-header">
            <i class="fas fa-user"></i>
            <h2>Szczegóły klienta</h2>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-card">
                    <h4>Dane osobowe</h4>
                    <div class="mb-3">
                        <label class="form-label">Imię</label>
                        <p class="form-control">{{ $klient->imie }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nazwisko</label>
                        <p class="form-control">{{ $klient->nazwisko }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <p class="form-control">{{ $klient->email }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefon</label>
                        <p class="form-control">{{ $klient->telefon }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <p class="form-control">
                            @if($klient->statusaktywnosci == '1')
                                <span class="badge bg-success">Aktywny</span>
                            @else
                                <span class="badge bg-danger">Nieaktywny</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-card">
                    <h4>Dane pozwolenia</h4>
                    <div class="mb-3">
                        <label class="form-label">Numer pozwolenia</label>
                        <p class="form-control">{{ $klient->numerpozwolenia }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Typ pozwolenia</label>
                        <p class="form-control">{{ $klient->typpozwolenia }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Data ważności</label>
                        <p class="form-control">{{ $klient->datawaznoscipozwolenia }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="form-card">
                    <h4>Adres</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ulica</label>
                                <p class="form-control">{{ $adres->ulica }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Numer domu</label>
                                <p class="form-control">{{ $adres->numerdomu }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Numer mieszkania</label>
                                <p class="form-control">{{ $adres->numermieszkania ?: 'Brak' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kod pocztowy</label>
                                <p class="form-control">{{ $adres->kodpocztowy }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Miejscowość</label>
                                <p class="form-control">{{ $adres->miejscowosc }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-footer">
            <a href="{{ route('home') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Powrót
            </a>
        </div>
    </div>
@endsection
