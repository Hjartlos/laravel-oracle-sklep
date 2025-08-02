<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class StatisticController extends Controller
{
    private function getMethodData($method)
    {
        return $this->$method() ?? [];
    }

    public function index()
    {
        $averageTransaction = $this->getMethodData('getAverageTransaction');
        $popularCities = $this->getMethodData('getPopularCities');
        $bestWorkers = $this->getMethodData('getBestWorkers');
        $bestClients = $this->getMethodData('getBestClients');
        $popularProducts = $this->getMethodData('getPopularProducts');

        return view('home.home', compact(
            'averageTransaction',
            'popularCities',
            'bestWorkers',
            'bestClients',
            'popularProducts'
        ));
    }

    private function getAverageTransaction()
    {
        try {
            $result = DB::select("SELECT
                        LICZBA_TRANSAKCJI as liczba_transakcji,
                        SUMA_TRANSAKCJI as suma_transakcji,
                        SREDNIA_WARTOSC as srednia_wartosc,
                        NAJMNIEJSZA_TRANSAKCJA as najmniejsza_transakcja,
                        NAJWIEKSZA_TRANSAKCJA as najwieksza_transakcja
                      FROM TABLE(STATYSTYKA_PKG.STATISTICAVERAGETRANSACTION())");

            return $result;
        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania statystyk transakcji: ' . $e->getMessage());
            return [];
        }
    }

    private function getPopularCities()
    {
        try {
            $result = DB::select("SELECT
                        MIEJSCOWOSC as miejscowosc,
                        LICZBA_KLIENTOW as liczba_klientow,
                        LICZBA_TRANSAKCJI as liczba_transakcji,
                        LACZNA_WARTOSC_ZAKUPOW as laczna_wartosc_zakupow
                      FROM TABLE(STATYSTYKA_PKG.STATISTICPOPULARCITY())");

            return $result;
        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania popularnych miast: ' . $e->getMessage());
            return [];
        }
    }

    private function getBestWorkers()
    {
        try {
            $result = DB::select("SELECT
                        PRACOWNIKID as pracownikid,
                        IMIE as imie,
                        NAZWISKO as nazwisko,
                        LICZBA_TRANSAKCJI as liczba_transakcji,
                        WARTOSC_SPRZEDAZY as wartosc_sprzedazy
                      FROM TABLE(STATYSTYKA_PKG.STATISTICBESTWORKER())");

            return $result;
        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania najlepszych pracowników: ' . $e->getMessage());
            return [];
        }
    }

    private function getBestClients()
    {
        try {
            $result = DB::select("SELECT
                        KLIENTID as klientid,
                        IMIE as imie,
                        NAZWISKO as nazwisko,
                        LICZBA_TRANSAKCJI as liczba_transakcji,
                        SUMA_WYDATKOW as suma_wydatkow
                      FROM TABLE(STATYSTYKA_PKG.STATISTICBESTCLIENTS())
                      FETCH FIRST 5 ROWS ONLY");

            return $result;
        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania najlepszych klientów: ' . $e->getMessage());
            return [];
        }
    }

    private function getPopularProducts()
    {
        try {
            $result = DB::select("SELECT
                        NAZWA_BRONI as nazwa_broni,
                        PRZYKLADOWE_PRODUKTID as produktid,
                        KATEGORIA as kategoria,
                        LICZBA_ZAMOWIEN as liczba_zamowien,
                        LACZNA_ILOSC as laczna_ilosc,
                        LACZNA_WARTOSC_SPRZEDAZY as laczna_wartosc_sprzedazy
                      FROM TABLE(STATYSTYKA_PKG.STATISTICPRODUCT())
                      FETCH FIRST 5 ROWS ONLY");

            return $result;
        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania popularnych produktów: ' . $e->getMessage());
            return [];
        }
    }
}
