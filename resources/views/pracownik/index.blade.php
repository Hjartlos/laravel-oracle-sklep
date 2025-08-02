@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Lista Pracowników</h1>
        <a href="{{ route('pracownik.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Dodaj Pracownika
        </a>
    </div>

    <div class="filter-card mb-4">
        <div class="row g-2">
            <div class="col-md-6">
                <a href="{{ route('pracownik.index') }}" class="btn btn-outline-secondary w-100 {{ !isset($filterActive) ? 'active' : '' }}">
                    <i class="fas fa-list me-2"></i> Wszyscy pracownicy
                </a>
            </div>
            <div class="col-md-6">
                <a href="{{ route('pracownik.active') }}" class="btn btn-outline-success w-100 {{ isset($filterActive) && $filterActive ? 'active' : '' }}">
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
                <th class="table-header">Stanowisko</th>
                <th class="table-header">Login</th>
                <th class="table-header">Typ Pozwolenia</th>
                <th class="table-header">Status</th>
                <th class="table-header text-center">Akcje</th>
            </tr>
            </thead>
            <tbody>
            @foreach($pracownicy as $pracownik)
                <tr class="table-row">
                    <td class="table-cell" data-label="ID">{{ $pracownik->pracownikid }}</td>
                    <td class="table-cell" data-label="Imię i Nazwisko">{{ $pracownik->imie }} {{ $pracownik->nazwisko }}</td>
                    <td class="table-cell" data-label="Stanowisko">{{ $pracownik->stanowisko }}</td>
                    <td class="table-cell" data-label="Login">{{ $pracownik->login }}</td>
                    <td class="table-cell" data-label="Typ pozwolenia">{{ $pracownik->typpozwolenia }}</td>
                    <td class="table-cell" data-label="Status">
                        @if($pracownik->statusaktywnosci == 1)
                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Aktywny</span>
                        @else
                            <span class="badge bg-secondary"><i class="fas fa-ban me-1"></i> Nieaktywny</span>
                        @endif
                    </td>
                    <td class="table-cell text-center" data-label="Akcje">
                        <div class="action-buttons">
                            <a href="{{ route('pracownik.edit', $pracownik->pracownikid) }}" class="btn btn-sm btn-primary me-2">
                                <i class="fas fa-edit"></i> Edytuj
                            </a>
                            <form action="{{ route('pracownik.delete', $pracownik->pracownikid) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć tego pracownika?')">
                                    <i class="fas fa-trash-alt"></i> Usuń
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            @if(count($pracownicy) == 0)
                <tr class="table-row">
                    <td colspan="7" class="table-empty">
                        <i class="fas fa-info-circle me-1"></i> Brak pracowników do wyświetlenia
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
@endsection
