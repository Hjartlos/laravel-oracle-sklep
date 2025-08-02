@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Szczegóły Transakcji #{{ $transakcja->transakcjaid }}</h1>
        <a href="{{ route('transakcja.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Powrót do listy
        </a>
    </div>

    <div class="content-card mb-4">
        <div class="row">
            <div class="col-md-4">
                <h5><i class="fas fa-user me-2"></i> Klient</h5>
                <p>{{ $transakcja->klient_nazwa }} ({{ $transakcja->klient_typpozwolenia }})</p>
            </div>
            <div class="col-md-4">
                <h5><i class="fas fa-user-tie me-2"></i> Pracownik</h5>
                <p>{{ $transakcja->pracownik_nazwa }} ({{ $transakcja->pracownik_typpozwolenia }})</p>
            </div>
            <div class="col-md-4">
                <h5><i class="fas fa-calendar me-2"></i> Data Transakcji</h5>
                <p>{{ date('d.m.Y', strtotime($transakcja->datatransakcji)) }}</p>
            </div>
        </div>
    </div>

    <div class="content-card">
        <h4 class="mb-4"><i class="fas fa-receipt me-2"></i> Paragon</h4>

        <div class="table-responsive">
            <table class="table table-dark mb-0">
                <thead>
                <tr>
                    <th class="table-header">Produkt</th>
                    <th class="table-header">Cena produktu</th>
                    <th class="table-header">Amunicja</th>
                    <th class="table-header">Cena amunicji</th>
                    <th class="table-header text-center">Ilość amunicji</th>
                    <th class="table-header text-end">Wartość</th>
                </tr>
                </thead>
                <tbody>
                @forelse($produkty as $produkt)
                    <tr class="table-row">
                        <td>{{ $produkt->nazwa_produktu ?? '-' }}</td>
                        <td>{{ number_format($produkt->cena_jednostkowa ?? 0, 2) }} zł</td>
                        <td>{{ $produkt->nazwa_amunicji ?? '-' }}</td>
                        <td>{{ number_format($produkt->cena_amunicji ?? 0, 2) }} zł</td>
                        <td class="text-center">{{ $produkt->ilosc_amunicji ?? 0 }}</td>
                        <td class="text-end">
                            @php
                                $wartoscProduktu = $produkt->cena_jednostkowa ?? 0;
                                $wartoscAmunicji = ($produkt->cena_amunicji ?? 0) * ($produkt->ilosc_amunicji ?? 0);
                                $wartoscCalkowita = $wartoscProduktu + $wartoscAmunicji;
                            @endphp
                            {{ number_format($wartoscCalkowita, 2) }} zł
                        </td>
                    </tr>
                @empty
                    <tr class="table-row">
                        <td colspan="6" class="text-center">Brak produktów w tej transakcji</td>
                    </tr>
                @endforelse
                </tbody>
                <tfoot>
                <tr class="table-row fw-bold" style="border-top: 2px solid #404040">
                    <td colspan="5" class="text-end">Suma:</td>
                    <td class="text-end">{{ number_format($transakcja->wartosctransakcji, 2) }} zł</td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
