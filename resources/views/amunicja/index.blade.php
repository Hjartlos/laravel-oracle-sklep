@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Lista Amunicji</h1>
        <a href="{{ route('amunicja.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Dodaj Amunicję
        </a>
    </div>

    <div class="content-card mb-4">
        <form action="{{ route('amunicja.filter') }}" method="GET" class="d-flex gap-2">
            <div class="flex-grow-1">
                <input type="text" name="nazwa" class="form-control" placeholder="Filtruj po nazwie..." value="{{ $nazwa ?? '' }}">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search me-1"></i> Szukaj
            </button>
            @if(isset($nazwa) && !empty($nazwa))
                <a href="{{ route('amunicja.index') }}" class="btn btn-secondary">
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
                <th class="table-header">Nazwa</th>
                <th class="table-header">Cena (zł)</th>
                <th class="table-header">Ilość</th>
                <th class="table-header text-center">Akcje</th>
            </tr>
            </thead>
            <tbody>
            @foreach($amunicje as $amunicja)
                <tr class="table-row">
                    <td class="table-cell" data-label="ID">{{ $amunicja->amunicjaid }}</td>
                    <td class="table-cell" data-label="Nazwa">{{ $amunicja->nazwa }}</td>
                    <td class="table-cell" data-label="Cena">{{ number_format($amunicja->cena, 2) }} zł</td>
                    <td class="table-cell" data-label="Ilość">{{ $amunicja->ilosc }}</td>
                    <td class="table-cell text-center" data-label="Akcje">
                        <div class="action-buttons">
                            <a href="{{ route('amunicja.edit', $amunicja->amunicjaid) }}" class="btn btn-sm btn-primary me-2">
                                <i class="fas fa-edit"></i> Edytuj
                            </a>
                            <form action="{{ route('amunicja.delete', $amunicja->amunicjaid) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć tę amunicję?')">
                                    <i class="fas fa-trash-alt"></i> Usuń
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            @if(count($amunicje) == 0)
                <tr class="table-row">
                    <td colspan="5" class="table-empty">
                        <i class="fas fa-info-circle me-1"></i> Brak amunicji do wyświetlenia
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
@endsection
