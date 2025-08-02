<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sklep Z Bronią</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
<div class="navbar-wrapper">
    <nav class="navbar navbar-expand-lg navbar-light mb-0">
        <div class="container-fluid">
            <a class="navbar-brand" href="/"><i class="fas fa-shield-alt me-2"></i>Gigachad Gunshop</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('adres.index') }}"><i class="fas fa-map-marker-alt me-1"></i> Adresy</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ Route::has('klient.index') ? route('klient.index') : '#' }}"><i class="fas fa-users me-1"></i> Klienci</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ Route::has('pracownik.index') ? route('pracownik.index') : '#' }}"><i class="fas fa-user-tie me-1"></i> Pracownicy</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ Route::has('kategoria.index') ? route('kategoria.index') : '#' }}"><i class="fas fa-tags me-1"></i> Kategorie</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ Route::has('amunicja.index') ? route('amunicja.index') : '#' }}"><i class="fas fa-bullseye me-1"></i> Amunicja</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ Route::has('produkt.index') ? route('produkt.index') : '#' }}"><i class="fas fa-box me-1"></i> Produkty</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ Route::has('transakcja.index') ? route('transakcja.index') : '#' }}"><i class="fas fa-exchange-alt me-1"></i> Transakcje</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>

<main>
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success mb-4">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger mb-4">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning mb-4">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('warning') }}
            </div>
        @endif

        <div class="content-card">
            @yield('content')
        </div>
    </div>
</main>

<footer>
    <div class="container">
        <p class="mb-0">© {{ date('Y') }} System Zarządzania. Wszystkie prawa zastrzeżone.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
