@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Lista Produktów</h1>
        <a href="{{ route('produkt.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Dodaj Produkt
        </a>
    </div>

    <div class="filter-card mb-4">
        <div class="row g-2">
            <div class="col-md-6">
                <a href="{{ route('produkt.index') }}" class="btn btn-outline-secondary w-100 {{ !isset($filterActive) ? 'active' : '' }}">
                    <i class="fas fa-list me-2"></i> Wszystkie produkty
                </a>
            </div>
            <div class="col-md-6">
                <a href="{{ route('produkt.active') }}" class="btn btn-outline-success w-100 {{ isset($filterActive) && $filterActive ? 'active' : '' }}">
                    <i class="fas fa-check-circle me-2"></i> Tylko dostępne
                </a>
            </div>
        </div>
    </div>

    <div class="table-responsive content-card p-0">
        <table class="table table-dark mb-0">
            <thead>
            <tr>
                <th class="table-header">ID</th>
                <th class="table-header">Nazwa</th>
                <th class="table-header">Kategoria</th>
                <th class="table-header">Typ</th>
                <th class="table-header">Numer Seryjny</th>
                <th class="table-header">Cena</th>
                <th class="table-header">Status</th>
                <th class="table-header text-center">Akcje</th>
            </tr>
            </thead>
            <tbody>
            @forelse($produkty as $produkt)
                <tr class="table-row">
                    <td class="table-cell" data-label="ID">{{ $produkt->produktid }}</td>
                    <td class="table-cell" data-label="Nazwa">{{ $produkt->nazwa }}</td>
                    <td class="table-cell" data-label="Kategoria">{{ $produkt->kategoria_nazwa ?? 'Brak kategorii' }}</td>
                    <td class="table-cell" data-label="Typ">{{ $produkt->wymaganeuprawnienia ?? 'Brak danych' }}</td>
                    <td class="table-cell" data-label="Numer Seryjny">{{ $produkt->numerseryjny }}</td>
                    <td class="table-cell" data-label="Cena">{{ number_format($produkt->cena, 2) }} zł</td>
                    <td class="table-cell" data-label="Status">
                        @if($produkt->dostepnosc == 1)
                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Dostępny</span>
                        @else
                            <span class="badge bg-secondary"><i class="fas fa-ban me-1"></i> Niedostępny</span>
                        @endif
                    </td>
                    <td class="table-cell text-center" data-label="Akcje">
                        <div class="action-buttons">
                            <a href="{{ route('produkt.edit', $produkt->produktid) }}" class="btn btn-sm btn-primary me-2">
                                <i class="fas fa-edit"></i> Edytuj
                            </a>
                            <form action="{{ route('produkt.delete', $produkt->produktid) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć ten produkt?')">
                                    <i class="fas fa-trash-alt"></i> Usuń
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr class="table-row">
                    <td colspan="7" class="table-empty">
                        <i class="fas fa-info-circle me-1"></i> Brak produktów do wyświetlenia
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
