@extends('layouts.app')

@section('content')
    <div class="form-card">
        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-1"></i>
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif
        <div class="form-header">
            <i class="fas fa-exchange-alt"></i>
            <h2>{{ isset($transakcja) ? 'Edytuj Transakcję #' . $transakcja->transakcjaid : 'Dodaj Nową Transakcję' }}</h2>
        </div>

        <form action="{{ isset($transakcja) ? route('transakcja.update', $transakcja->transakcjaid) : route('transakcja.store') }}" method="POST">
            @csrf
            @if(isset($transakcja))
                @method('PUT')
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="klientid"><i class="fas fa-user me-1"></i> Klient</label>
                        <select name="klientid" id="klientid" class="form-control">
                            <option value="">Wybierz klienta</option>
                            @foreach($klienci as $klient)
                                <option value="{{ $klient->klientid }}" {{ isset($transakcja) && $transakcja->klientid == $klient->klientid ? 'selected' : '' }}>
                                    {{ $klient->imie }} {{ $klient->nazwisko }} ({{ $klient->typpozwolenia }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="pracownikid"><i class="fas fa-user-tie me-1"></i> Pracownik</label>
                        <select name="pracownikid" id="pracownikid" class="form-control">
                            <option value="">Wybierz pracownika</option>
                            @foreach($pracownicy as $pracownik)
                                <option value="{{ $pracownik->pracownikid }}" {{ isset($transakcja) && $transakcja->pracownikid == $pracownik->pracownikid ? 'selected' : '' }}>
                                    {{ $pracownik->imie }} {{ $pracownik->nazwisko }} ({{ $pracownik->typpozwolenia }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Wybrane produkty</h4>
                <button type="button" class="btn btn-sm btn-success" id="dodajProdukt">
                    <i class="fas fa-plus me-1"></i> Dodaj produkt
                </button>
            </div>

            <div id="produktyContainer">
                @if(isset($zamowioneProdukty) && count($zamowioneProdukty) > 0)
                    @foreach($zamowioneProdukty as $index => $zamowionyProdukt)
                        <div class="row mb-3 produkt-row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Produkt</label>
                                    <select name="produkty[]" class="form-control produkt-select">
                                        <option value="">Wybierz produkt</option>
                                        @foreach($produkty as $produkt)
                                            <option value="{{ $produkt->produktid }}"
                                                    data-price="{{ $produkt->cena }}"
                                                    data-amunicjaid="{{ $produkt->amunicjaid }}"
                                                    data-amunicja_cena="{{ $produkt->amunicja_cena ?? 0 }}"
                                                {{ $zamowionyProdukt->produktid == $produkt->produktid ? 'selected' : '' }}>
                                                {{ $produkt->nazwa }} ({{ number_format($produkt->cena, 2) }} zł)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group amunicja-container" style="{{ $zamowionyProdukt->amunicjaid ? 'display: block;' : 'display: none;' }}">
                                    <label>Ilość amunicji</label>
                                    <input type="number" name="iloscAmunicji[]" class="form-control amunicja-ilosc" min="0" value="{{ $zamowionyProdukt->ilosc_amunicji ?? 0 }}">
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-sm btn-danger remove-produkt mb-3">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="row mb-3 produkt-row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Produkt</label>
                                <select name="produkty[]" class="form-control produkt-select">
                                    <option value="">Wybierz produkt</option>
                                    @foreach($produkty as $produkt)
                                        @if($produkt->dostepnosc == 1)
                                            <option value="{{ $produkt->produktid }}"
                                                    data-price="{{ $produkt->cena }}"
                                                    data-amunicjaid="{{ $produkt->amunicjaid }}"
                                                    data-amunicja_cena="{{ $produkt->amunicja_cena ?? 0 }}">
                                                {{ $produkt->nazwa }} ({{ number_format($produkt->cena, 2) }} zł)
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group amunicja-container" style="display: none;">
                                <label>Ilość amunicji</label>
                                <input type="number" name="iloscAmunicji[]" class="form-control amunicja-ilosc" min="0" value="0">
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-sm btn-danger remove-produkt mb-3">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-info-circle me-1"></i> Wartość transakcji zostanie obliczona automatycznie.</span>
                        <span class="fw-bold">Suma: <span id="sumValue">{{ isset($transakcja) ? number_format($transakcja->wartosctransakcji, 2) : '0.00' }}</span> zł</span>
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <a href="{{ route('transakcja.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Anuluj
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i> Zapisz transakcję
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const produktyContainer = document.getElementById('produktyContainer');
            const dodajProduktBtn = document.getElementById('dodajProdukt');
            const sumValueSpan = document.getElementById('sumValue');

            function updateAvailableProducts() {
                const selectedProducts = new Set();
                document.querySelectorAll('.produkt-select').forEach(select => {
                    if (select.selectedIndex > 0) {
                        selectedProducts.add(select.value);
                    }
                });

                document.querySelectorAll('.produkt-select').forEach(select => {
                    const currentValue = select.value;

                    Array.from(select.options).forEach(option => {
                        if (option.value && option.value !== currentValue && selectedProducts.has(option.value)) {
                            option.disabled = true;
                            option.style.display = 'none';
                        } else {
                            option.disabled = false;
                            option.style.display = '';
                        }
                    });
                });
            }

            function toggleAmunicjaField(select) {
                const row = select.closest('.produkt-row');
                const amunicjaContainer = row.querySelector('.amunicja-container');
                const selectedOption = select.options[select.selectedIndex];

                if (selectedOption && selectedOption.dataset.amunicjaid) {
                    amunicjaContainer.style.display = 'block';
                } else {
                    amunicjaContainer.style.display = 'none';
                    const iloscInput = amunicjaContainer.querySelector('.amunicja-ilosc');
                    if (iloscInput) iloscInput.value = 0;
                }
            }

            document.querySelectorAll('.produkt-select').forEach(select => {
                toggleAmunicjaField(select);

                select.addEventListener('change', function() {
                    toggleAmunicjaField(this);
                    updateSum();
                    updateAvailableProducts();
                });
            });

            function updateSum() {
                let sum = 0;
                const rows = document.querySelectorAll('.produkt-row');

                rows.forEach(row => {
                    const select = row.querySelector('.produkt-select');
                    if (select.selectedIndex > 0) {
                        const option = select.options[select.selectedIndex];
                        const productPrice = parseFloat(option.dataset.price);

                        let rowValue = productPrice;

                        if (option.dataset.amunicjaid) {
                            const amunicjaIloscInput = row.querySelector('.amunicja-ilosc');
                            const amunicjaIlosc = parseInt(amunicjaIloscInput.value) || 0;
                            const amunicjaCena = parseFloat(option.dataset.amunicja_cena) || 0;

                            rowValue += (amunicjaCena * amunicjaIlosc);
                        }

                        sum += rowValue;
                    }
                });

                sumValueSpan.textContent = sum.toFixed(2);
            }

            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('amunicja-ilosc')) {
                    updateSum();
                }
            });

            dodajProduktBtn.addEventListener('click', function() {
                const template = document.querySelector('.produkt-row').cloneNode(true);
                const select = template.querySelector('select');
                const inputs = template.querySelectorAll('input');

                select.selectedIndex = 0;
                inputs.forEach(input => {
                    input.value = 0;
                });

                template.querySelector('.amunicja-container').style.display = 'none';

                template.querySelector('.remove-produkt').addEventListener('click', function() {
                    if (document.querySelectorAll('.produkt-row').length > 1) {
                        this.closest('.produkt-row').remove();
                        updateSum();
                        updateAvailableProducts();
                    }
                });

                template.querySelector('.produkt-select').addEventListener('change', function() {
                    toggleAmunicjaField(this);
                    updateSum();
                    updateAvailableProducts();
                });

                produktyContainer.appendChild(template);
                updateAvailableProducts();
            });

            document.querySelectorAll('.remove-produkt').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (document.querySelectorAll('.produkt-row').length > 1) {
                        this.closest('.produkt-row').remove();
                        updateSum();
                        updateAvailableProducts();
                    }
                });
            });

            updateSum();
            updateAvailableProducts();
        });
    </script>
@endsection
