@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Lista Transakcji</h1>
        <a href="{{ route('transakcja.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Dodaj Transakcję
        </a>
    </div>
    <div class="filter-card mb-4">
        <form action="{{ route('transakcja.filter') }}" method="GET" id="filterForm">
            <div class="row g-2">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">Od</span>
                        <input type="date" name="dataOd" class="form-control" value="{{ $dataOd ?? '' }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">Do</span>
                        <input type="date" name="dataDo" class="form-control" value="{{ $dataDo ?? '' }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i> Filtruj
                    </button>
                </div>
                <div class="col-md-1">
                    @if(isset($filterActive) && $filterActive)
                        <a href="{{ route('transakcja.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>

        @if(isset($filterActive) && $filterActive)
            <div class="mt-2 text-muted">
                <small>
                    <i class="fas fa-info-circle me-1"></i>
                    Filtr aktywny:
                    @if(!empty($dataOd)) Od: {{ \Carbon\Carbon::parse($dataOd)->format('d.m.Y') }} @endif
                    @if(!empty($dataDo)) Do: {{ \Carbon\Carbon::parse($dataDo)->format('d.m.Y') }} @endif
                </small>
            </div>
        @endif
    </div>
    <div class="table-responsive content-card p-0">
        <table class="table table-dark mb-0">
            <thead>
            <tr>
                <th class="table-header">ID</th>
                <th class="table-header">Klient</th>
                <th class="table-header">Pracownik</th>
                <th class="table-header">Data</th>
                <th class="table-header">Wartość</th>
                <th class="table-header text-center">Akcje</th>
            </tr>
            </thead>
            <tbody>
            @forelse($transakcje as $transakcja)
                <tr class="table-row">
                    <td class="table-cell" data-label="ID">{{ $transakcja->transakcjaid }}</td>
                    <td class="table-cell" data-label="Klient">{{ $transakcja->klient_nazwa }}</td>
                    <td class="table-cell" data-label="Pracownik">{{ $transakcja->pracownik_nazwa }}</td>
                    <td class="table-cell" data-label="Data">
                        @if(isset($transakcja->datatransakcji) && !empty($transakcja->datatransakcji))
                            {{ \Carbon\Carbon::parse($transakcja->datatransakcji)->format('d.m.Y') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="table-cell" data-label="Wartość">{{ number_format($transakcja->wartosctransakcji, 2) }} zł</td>
                    <td class="table-cell text-center" data-label="Akcje">
                        <div class="action-buttons">
                            <a href="{{ route('transakcja.show', $transakcja->transakcjaid) }}" class="btn btn-sm btn-info me-2">
                                <i class="fas fa-eye"></i> Szczegóły
                            </a>
                            <a href="{{ route('transakcja.edit', $transakcja->transakcjaid) }}" class="btn btn-sm btn-primary me-2">
                                <i class="fas fa-edit"></i> Edytuj
                            </a>
                            <form action="{{ route('transakcja.delete', $transakcja->transakcjaid) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć tę transakcję?')">
                                    <i class="fas fa-trash-alt"></i> Usuń
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr class="table-row">
                    <td colspan="6" class="table-empty">
                        <i class="fas fa-info-circle me-1"></i> Brak transakcji do wyświetlenia
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
