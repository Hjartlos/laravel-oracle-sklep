@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Lista Adresów</h1>
        <a href="{{ route('adres.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Dodaj Adres
        </a>
    </div>

    <div class="content-card mb-4">
        <form action="{{ route('adres.filter') }}" method="GET" class="d-flex gap-2">
            <div class="flex-grow-1">
                <input type="text" name="miasto" class="form-control" placeholder="Filtruj po mieście..." value="{{ $miasto ?? '' }}">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search me-1"></i> Szukaj
            </button>
            @if(isset($miasto) && !empty($miasto))
                <a href="{{ route('adres.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i> Wyczyść
                </a>
            @endif
        </form>
    </div>

    <div class="table-responsive content-card p-0">
        <table class="table table-dark mb-0">
            <thead>
            <tr>
                <th class="table-header">ID</th>
                <th class="table-header">Ulica</th>
                <th class="table-header">Numer</th>
                <th class="table-header">Kod Pocztowy</th>
                <th class="table-header">Miejscowość</th>
                <th class="table-header text-center">Akcje</th>
            </tr>
            </thead>
            <tbody>
            @foreach($adresy as $adres)
                <tr class="table-row">
                    <td class="table-cell" data-label="ID">{{ $adres->adresid }}</td>
                    <td class="table-cell" data-label="Ulica">{{ $adres->ulica }}</td>
                    <td class="table-cell" data-label="Numer">
                        {{ $adres->numerdomu }}{{ $adres->numermieszkania ? '/' . $adres->numermieszkania : '' }}
                    </td>
                    <td class="table-cell" data-label="Kod Pocztowy">{{ $adres->kodpocztowy }}</td>
                    <td class="table-cell" data-label="Miejscowość">{{ $adres->miejscowosc }}</td>
                    <td class="table-cell text-center" data-label="Akcje">
                        <div class="action-buttons">
                            <a href="{{ route('adres.edit', $adres->adresid) }}" class="btn btn-sm btn-primary me-2">
                                <i class="fas fa-edit"></i> Edytuj
                            </a>
                            <form action="{{ route('adres.delete', $adres->adresid) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć ten adres?')">
                                    <i class="fas fa-trash-alt"></i> Usuń
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            @if(count($adresy) == 0)
                <tr class="table-row">
                    <td colspan="6" class="table-empty">
                        <i class="fas fa-info-circle me-1"></i> Brak adresów do wyświetlenia
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
@endsection
