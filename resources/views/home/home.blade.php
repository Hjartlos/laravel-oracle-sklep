@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Panel statystyk</h1>

        <div class="content-card">
            <h2>Statystyki transakcji</h2>

            <div class="row stats-row mb-4">
                @foreach ($averageTransaction as $stat)
                    <div class="col-md-3 mb-3">
                        <div class="content-card stat-card">
                            <div class="text-center stat-content">
                                <h5>Liczba transakcji</h5>
                                <p class="stat-value">{{ $stat->liczba_transakcji }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="content-card stat-card">
                            <div class="text-center stat-content">
                                <h5>Suma transakcji</h5>
                                <p class="stat-value">{{ number_format($stat->suma_transakcji, 2) }} zł</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="content-card stat-card">
                            <div class="text-center stat-content">
                                <h5>Średnia wartość</h5>
                                <p class="stat-value">{{ number_format($stat->srednia_wartosc, 2) }} zł</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="content-card stat-card">
                            <div class="text-center stat-content">
                                <h5>Zakres wartości</h5>
                                <div class="value-range">
                                    <div class="min-value">{{ number_format($stat->najmniejsza_transakcja, 2) }} zł</div>
                                    <div class="range-bar">
                                        <div class="range-indicator" style="left: {{ ($stat->najmniejsza_transakcja / $stat->najwieksza_transakcja) * 100 }}%"></div>
                                        <div class="range-indicator" style="left: {{ ($stat->srednia_wartosc / $stat->najwieksza_transakcja) * 100 }}%"></div>
                                    </div>
                                    <div class="max-value">{{ number_format($stat->najwieksza_transakcja, 2) }} zł</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="content-card chart-card">
                        <div class="chart-container">
                            <canvas id="transactionStatsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-card">
            <h2>Popularne miasta</h2>
            <div class="row stats-row">
                @forelse ($popularCities as $city)
                    <div class="col-md-4 mb-3">
                        <div class="content-card stat-card city-card">
                            <div class="text-center stat-content">
                                <h5>{{ $city->miejscowosc }}</h5>
                                <div class="city-stats">
                                    <div class="stat-item">
                                        <i class="fas fa-users me-2"></i>
                                        <span>Klienci: {{ $city->liczba_klientow }}</span>
                                    </div>
                                    <div class="stat-item">
                                        <i class="fas fa-exchange-alt me-2"></i>
                                        <span>Transakcje: {{ $city->liczba_transakcji }}</span>
                                    </div>
                                    <div class="stat-value mt-2">
                                        {{ number_format($city->laczna_wartosc_zakupow, 2) }} zł
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-center">Brak danych o popularnych miastach</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="content-card">
            <h2>Najlepsi pracownicy</h2>
            <div class="row stats-row">
                @forelse ($bestWorkers as $index => $worker)
                    <div class="col-md-4 mb-3">
                        <div class="content-card stat-card worker-card">
                            <div class="stat-content">
                                <div class="worker-header">
                                    <span class="worker-rank">{{ $index + 1 }}</span>
                                    <h5>{{ $worker->imie }} {{ $worker->nazwisko }}</h5>
                                </div>
                                <div class="worker-stats">
                                    <div class="stat-item">
                                        <i class="fas fa-exchange-alt me-2"></i>
                                        <span>Liczba transakcji: {{ $worker->liczba_transakcji }}</span>
                                    </div>
                                    <div class="stat-value mt-2">
                                        {{ number_format($worker->wartosc_sprzedazy, 2) }} zł
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($index == 2)
                        @break
                    @endif
                @empty
                    <div class="col-12">
                        <p class="text-center">Brak danych o pracownikach</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="content-card">
            <h2>Najlepsi klienci</h2>
            <div class="table-responsive">
                <table class="table table-dark mb-0">
                    <thead>
                    <tr>
                        <th class="table-header">#</th>
                        <th class="table-header">Imię i nazwisko</th>
                        <th class="table-header">Liczba transakcji</th>
                        <th class="table-header">Suma wydatków</th>
                        <th class="table-header text-center">Szczegóły</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($bestClients as $index => $client)
                        <tr class="table-row">
                            <td class="table-cell" data-label="#">{{ $index + 1 }}</td>
                            <td class="table-cell" data-label="Imię i nazwisko">{{ $client->imie }} {{ $client->nazwisko }}</td>
                            <td class="table-cell" data-label="Liczba transakcji">{{ $client->liczba_transakcji }}</td>
                            <td class="table-cell" data-label="Suma wydatków">{{ number_format($client->suma_wydatkow, 2) }} zł</td>
                            <td class="table-cell text-center" data-label="Szczegóły">
                                <a href="{{ route('klient.show', $client->klientid) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye me-1"></i> Podgląd
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr class="table-row">
                            <td colspan="5" class="table-empty">
                                <i class="fas fa-info-circle me-2"></i>Brak danych o najlepszych klientach
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="content-card">
            <h2>Najczęściej zamawiane produkty</h2>
            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="chart-card">
                        <canvas id="weaponOrdersChart"></canvas>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="chart-card">
                        <canvas id="ammoSalesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dpr = window.devicePixelRatio || 1;

            const commonChartOptions = {
                responsive: true,
                devicePixelRatio: dpr,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 10,
                        top: 10,
                        bottom: 30
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: '#eeeeee',
                            font: {
                                size: 12,
                                family: "'Poppins', sans-serif"
                            },
                            padding: 15
                        }
                    },
                    title: {
                        display: true,
                        color: '#ffffff',
                        font: {
                            size: 16,
                            weight: 'bold',
                            family: "'Poppins', sans-serif"
                        },
                        padding: {
                            bottom: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#eeeeee',
                        padding: 12,
                        cornerRadius: 4,
                        displayColors: true,
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255, 255, 255, 0.1)' },
                        ticks: {
                            color: '#eeeeee',
                            font: {
                                size: 12,
                                family: "'Poppins', sans-serif"
                            },
                            padding: 10
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#eeeeee',
                            font: {
                                size: 14,
                                weight: 'bold',
                                family: "'Poppins', sans-serif"
                            },
                            padding: 20
                        }
                    }
                }
            };

            function initChartCanvas(canvasId) {
                const canvas = document.getElementById(canvasId);
                if (!canvas) return null;

                const container = canvas.parentNode;
                const containerStyle = getComputedStyle(container);
                const width = parseInt(containerStyle.width, 10);
                const height = parseInt(containerStyle.height, 10);

                canvas.width = width * dpr;
                canvas.height = height * dpr;
                canvas.style.width = width + 'px';
                canvas.style.height = height + 'px';

                const context = canvas.getContext('2d');
                context.scale(dpr, dpr);

                return canvas;
            }

            const ctx = initChartCanvas('transactionStatsChart');
            if (ctx) {
                @foreach ($averageTransaction as $stat)
                const minVal = {{ $stat->najmniejsza_transakcja ?? 0 }};
                const avgVal = {{ $stat->srednia_wartosc ?? 0 }};
                const maxVal = {{ $stat->najwieksza_transakcja ?? 0 }};

                const transactionData = {
                    labels: ['Minimalna', 'Średnia', 'Maksymalna'],
                    datasets: [{
                        label: 'Wartość transakcji (zł)',
                        data: [minVal, avgVal, maxVal],
                        backgroundColor: [
                            'rgba(231, 76, 60, 0.7)',
                            'rgba(46, 204, 113, 0.7)',
                            'rgba(52, 152, 219, 0.7)'
                        ],
                        borderColor: [
                            'rgba(231, 76, 60, 1)',
                            'rgba(46, 204, 113, 1)',
                            'rgba(52, 152, 219, 1)'
                        ],
                        borderWidth: 1
                    }]
                };
                @endforeach

                if (window.transactionChart instanceof Chart) {
                    window.transactionChart.destroy();
                }

                window.transactionChart = new Chart(ctx, {
                    type: 'bar',
                    data: transactionData,
                    options: {
                        ...commonChartOptions,
                        plugins: {
                            ...commonChartOptions.plugins,
                            legend: {
                                display: false
                            },
                            title: {
                                ...commonChartOptions.plugins.title,
                                text: 'Porównanie wartości transakcji'
                            }
                        },
                        scales: {
                            ...commonChartOptions.scales,
                            x: {
                                ...commonChartOptions.scales.x,
                                ticks: {
                                    ...commonChartOptions.scales.x.ticks,
                                    maxRotation: 0,
                                    minRotation: 0,
                                    autoSkip: false,
                                    align: 'center'
                                }
                            }
                        },
                        datasets: {
                            bar: {
                                barThickness: 160,
                                maxBarThickness: 160,
                                barPercentage: 0.7,
                                categoryPercentage: 0.7
                            }
                        }
                    }
                });
            }

            const ordersCtx = initChartCanvas('weaponOrdersChart');
            if (ordersCtx) {
                const ordersData = {
                    labels: [
                        @foreach ($popularProducts as $product)
                            '{{ $product->nazwa_broni }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Liczba zamówień broni',
                        data: [
                            @foreach ($popularProducts as $product)
                                {{ $product->liczba_zamowien }},
                            @endforeach
                        ],
                        backgroundColor: 'rgba(46, 204, 113, 0.7)',
                        borderColor: 'rgba(46, 204, 113, 1)',
                        borderWidth: 1
                    }]
                };

                if (window.ordersChart instanceof Chart) {
                    window.ordersChart.destroy();
                }

                window.ordersChart = new Chart(ordersCtx, {
                    type: 'bar',
                    data: ordersData,
                    options: {
                        ...commonChartOptions,
                        plugins: {
                            ...commonChartOptions.plugins,
                            title: {
                                ...commonChartOptions.plugins.title,
                                text: 'Liczba zamówień broni'
                            }
                        },
                        scales: {
                            ...commonChartOptions.scales,
                            y: {
                                ...commonChartOptions.scales.y,
                                ticks: {
                                    ...commonChartOptions.scales.y.ticks,
                                    stepSize: 1,
                                    precision: 0,
                                    callback: function(value) {
                                        if (Math.floor(value) === value) {
                                            return value;
                                        }
                                    }
                                }
                            },
                            x: {
                                ...commonChartOptions.scales.x,
                                ticks: {
                                    ...commonChartOptions.scales.x.ticks,
                                    maxRotation: 0,
                                    minRotation: 0
                                }
                            }
                        },
                        barPercentage: 0.6,
                        categoryPercentage: 0.8
                    }
                });
            }

            const ammoCtx = initChartCanvas('ammoSalesChart');
            if (ammoCtx) {
                const ammoData = {
                    labels: [
                        @foreach ($popularProducts as $product)
                            '{{ $product->nazwa_broni }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Ilość sprzedanej amunicji',
                        data: [
                            @foreach ($popularProducts as $product)
                                {{ $product->laczna_ilosc }},
                            @endforeach
                        ],
                        backgroundColor: 'rgba(52, 152, 219, 0.7)',
                        borderColor: 'rgba(52, 152, 219, 1)',
                        borderWidth: 1
                    }]
                };

                if (window.ammoChart instanceof Chart) {
                    window.ammoChart.destroy();
                }

                window.ammoChart = new Chart(ammoCtx, {
                    type: 'bar',
                    data: ammoData,
                    options: {
                        ...commonChartOptions,
                        plugins: {
                            ...commonChartOptions.plugins,
                            title: {
                                ...commonChartOptions.plugins.title,
                                text: 'Ilość sprzedanej amunicji'
                            }
                        },
                        scales: {
                            ...commonChartOptions.scales,
                            x: {
                                ...commonChartOptions.scales.x,
                                ticks: {
                                    ...commonChartOptions.scales.x.ticks,
                                    maxRotation: 0,
                                    minRotation: 0
                                }
                            }
                        },
                        barPercentage: 0.6,
                        categoryPercentage: 0.8
                    }
                });
            }
        });
    </script>
@endpush
