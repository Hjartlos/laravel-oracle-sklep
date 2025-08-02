@extends('layouts.app')

@section('content')
    <div class="form-card">
        <h2><i class="fas fa-{{ isset($produkt) ? 'edit' : 'plus-circle' }} me-2"></i>{{ isset($produkt) ? 'Edytuj' : 'Dodaj' }} Produkt</h2>

        @if($errors->has('db_error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ $errors->first('db_error') }}
            </div>
        @endif

        <form action="{{ isset($produkt) ? route('produkt.update', $produkt->produktid) : route('produkt.store') }}" method="POST">
            @csrf
            @if(isset($produkt))
                @method('PUT')
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nazwa"><i class="fas fa-tag me-1"></i> Nazwa produktu</label>
                        <input type="text" name="nazwa" id="nazwa" class="form-control" value="{{ $produkt->nazwa ?? old('nazwa') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="numerSeryjny"><i class="fas fa-barcode me-1"></i> Numer seryjny</label>
                        <input type="text" name="numerSeryjny" id="numerSeryjny" class="form-control" value="{{ $produkt->numerseryjny ?? old('numerSeryjny') }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="kategoriaId"><i class="fas fa-list me-1"></i> Kategoria</label>
                        <select name="kategoriaId" id="kategoriaId" class="form-control">
                            @foreach($kategorie as $kategoria)
                                <option value="{{ $kategoria->kategoriaid }}"
                                    {{ (isset($produkt) && $produkt->kategoriaid == $kategoria->kategoriaid) ? 'selected' : '' }}>
                                    {{ $kategoria->nazwakategorii }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="amunicjaId"><i class="fas fa-bullseye me-1"></i> Amunicja (opcjonalnie)</label>
                        <select name="amunicjaId" id="amunicjaId" class="form-control">
                            <option value="">Brak</option>
                            @foreach($amunicje as $amunicja)
                                <option value="{{ $amunicja->amunicjaid }}"
                                    {{ (isset($produkt) && $produkt->amunicjaid == $amunicja->amunicjaid) ? 'selected' : '' }}>
                                    {{ $amunicja->nazwa }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="cena"><i class="fas fa-money-bill-wave me-1"></i> Cena (zł)</label>
                        <input type="number" step="0.01" min="0.01" name="cena" id="cena" class="form-control" value="{{ $produkt->cena ?? old('cena') }}">
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <a href="{{ route('produkt.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Anuluj
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-{{ isset($produkt) ? 'save' : 'plus' }} me-1"></i>
                    {{ isset($produkt) ? 'Zapisz zmiany' : 'Dodaj produkt' }}
                </button>
            </div>
        </form>
    </div>
@endsection
