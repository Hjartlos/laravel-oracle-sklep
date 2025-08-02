@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Lista Kategorii Produktów</h1>
        <a href="{{ route('kategoria.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Dodaj Kategorię
        </a>
    </div>

    <div class="content-card mb-4">
        <form action="{{ route('kategoria.filter.uprawnienia') }}" method="GET" class="d-flex gap-2">
            <div class="flex-grow-1">
                <input type="text" name="wymaganeuprawnienia" class="form-control" placeholder="Filtruj po uprawnieniach..." value="{{ $wymaganeuprawnienia ?? '' }}">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search me-1"></i> Szukaj
            </button>
            @if(isset($wymaganeuprawnienia) && !empty($wymaganeuprawnienia))
                <a href="{{ route('kategoria.index') }}" class="btn btn-secondary">
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
                <th class="table-header">Nazwa kategorii</th>
                <th class="table-header">Opis</th>
                <th class="table-header">Wymagane uprawnienia</th>
                <th class="table-header text-center">Akcje</th>
            </tr>
            </thead>
            <tbody>
            @foreach($kategorie as $kategoria)
                <tr class="table-row">
                    <td class="table-cell" data-label="ID">{{ $kategoria->kategoriaid }}</td>
                    <td class="table-cell" data-label="Nazwa kategorii">{{ $kategoria->nazwakategorii }}</td>
                    <td class="table-cell" data-label="Opis">{{ Str::limit($kategoria->opis, 100) }}</td>
                    <td class="table-cell" data-label="Wymagane uprawnienia">{{ $kategoria->wymaganeuprawnienia ?? 'Brak' }}</td>
                    <td class="table-cell text-center" data-label="Akcje">
                        <div class="action-buttons">
                            <a href="{{ route('kategoria.edit', $kategoria->kategoriaid) }}" class="btn btn-sm btn-primary me-2">
                                <i class="fas fa-edit"></i> Edytuj
                            </a>
                            <form action="{{ route('kategoria.delete', $kategoria->kategoriaid) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć tę kategorię?')">
                                    <i class="fas fa-trash-alt"></i> Usuń
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            @if(count($kategorie) == 0)
                <tr class="table-row">
                    <td colspan="5" class="table-empty">
                        <i class="fas fa-info-circle me-1"></i> Brak kategorii do wyświetlenia
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
@endsection
