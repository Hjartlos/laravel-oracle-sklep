@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Lista Klientów</h1>
        <a href="{{ route('klient.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Dodaj Klienta
        </a>
    </div>

    <div class="filter-card mb-4">
        <div class="row g-2">
            <div class="col-md-6">
                <a href="{{ route('klient.index') }}" class="btn btn-outline-secondary w-100 {{ !isset($filterActive) ? 'active' : '' }}">
                    <i class="fas fa-list me-2"></i> Wszyscy klienci
                </a>
            </div>
            <div class="col-md-6">
                <a href="{{ route('klient.active') }}" class="btn btn-outline-success w-100 {{ isset($filterActive) && $filterActive ? 'active' : '' }}">
                    <i class="fas fa-check-circle me-2"></i> Tylko aktywni
                </a>
            </div>
        </div>
    </div>

    <div class="table-responsive content-card p-0">
        <table class="table table-dark mb-0">
            <thead>
            <tr>
                <th class="table-header">ID</th>
                <th class="table-header">Imię i Nazwisko</th>
                <th class="table-header">Numer Pozwolenia</th>
                <th class="table-header">Typ</th>
                <th class="table-header">Data Ważności</th>
                <th class="table-header">Kontakt</th>
                <th class="table-header">Status</th>
                <th class="table-header text-center">Akcje</th>
            </tr>
            </thead>
            <tbody>
            @forelse($klienci as $klient)
                <tr class="table-row">
                    <td class="table-cell" data-label="ID">{{ $klient->klientid }}</td>
                    <td class="table-cell" data-label="Imię i Nazwisko">{{ $klient->imie }} {{ $klient->nazwisko }}</td>
                    <td class="table-cell" data-label="Numer Pozwolenia">{{ $klient->numerpozwolenia }}</td>
                    <td class="table-cell" data-label="Typ">{{ $klient->typpozwolenia }}</td>
                    <td class="table-cell" data-label="Data Ważności">{{ date('d.m.Y', strtotime($klient->datawaznoscipozwolenia)) }}</td>
                    <td class="table-cell" data-label="Kontakt">
                        <span class="d-block"><i class="fas fa-envelope me-1"></i> {{ $klient->email }}</span>
                        <span class="d-block"><i class="fas fa-phone me-1"></i> {{ $klient->telefon }}</span>
                    </td>
                    <td class="table-cell" data-label="Status">
                        @if($klient->statusaktywnosci == 1)
                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Aktywny</span>
                        @else
                            <span class="badge bg-secondary"><i class="fas fa-ban me-1"></i> Nieaktywny</span>
                        @endif
                    </td>
                    <td class="table-cell text-center" data-label="Akcje">
                        <div class="action-buttons">
                            <a href="{{ route('klient.edit', $klient->klientid) }}" class="btn btn-sm btn-primary me-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('klient.delete', $klient->klientid) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć tego klienta?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="table-empty">Brak klientów w bazie danych</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
