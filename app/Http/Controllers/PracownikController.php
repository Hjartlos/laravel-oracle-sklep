<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PracownikController extends Controller
{
    public function create(Request $request)
    {
        $imie = $request->imie;
        $nazwisko = $request->nazwisko;
        $stanowisko = $request->stanowisko;
        $typPozwolenia = $request->typPozwolenia;
        $login = $request->login;
        $haslo = $request->haslo;
        $ulica = $request->ulica;
        $numerDomu = $request->numerDomu;
        $numerMieszkania = $request->numerMieszkania;
        $kodPocztowy = $request->kodPocztowy;
        $miejscowosc = $request->miejscowosc;

        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN PRACOWNIK_PKG.PracownikCreate(:imie, :nazwisko, :stanowisko, :typPozwolenia, :login, :haslo, :ulica, :numerDomu, :numerMieszkania, :kodPocztowy, :miejscowosc); END;");
            $stmt->bindParam(':imie', $imie);
            $stmt->bindParam(':nazwisko', $nazwisko);
            $stmt->bindParam(':stanowisko', $stanowisko);
            $stmt->bindParam(':typPozwolenia', $typPozwolenia);
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':haslo', $haslo);
            $stmt->bindParam(':ulica', $ulica);
            $stmt->bindParam(':numerDomu', $numerDomu);
            $stmt->bindParam(':numerMieszkania', $numerMieszkania);
            $stmt->bindParam(':kodPocztowy', $kodPocztowy);
            $stmt->bindParam(':miejscowosc', $miejscowosc);
            $stmt->execute();

            return redirect()->route('pracownik.index')->with('success', 'Pracownik został utworzony.');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            \Log::error('Błąd podczas tworzenia pracownika: ' . $errorMessage);

            if (preg_match('/ORA-\d+: (.*?)(\s+ORA-|$)/', $errorMessage, $matches)) {
                $userFriendlyError = $matches[1];
            } else {
                $userFriendlyError = 'Wystąpił błąd podczas tworzenia pracownika.';
            }

            return back()->withInput()->withErrors(['db_error' => $userFriendlyError]);
        }
    }

    public function update(Request $request, $id)
    {
        $imie = $request->imie;
        $nazwisko = $request->nazwisko;
        $stanowisko = $request->stanowisko;
        $typPozwolenia = $request->typPozwolenia;
        $login = $request->login;
        $haslo = $request->haslo;
        $statusAktywnosci = $request->statusAktywnosci;
        $ulica = $request->ulica;
        $numerDomu = $request->numerDomu;
        $numerMieszkania = $request->numerMieszkania;
        $kodPocztowy = $request->kodPocztowy;
        $miejscowosc = $request->miejscowosc;

        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN PRACOWNIK_PKG.PracownikUpdate(:pracownikID, :imie, :nazwisko, :stanowisko, :typPozwolenia, :login, :haslo, :statusAktywnosci, :ulica, :numerDomu, :numerMieszkania, :kodPocztowy, :miejscowosc); END;");
            $stmt->bindParam(':pracownikID', $id);
            $stmt->bindParam(':imie', $imie);
            $stmt->bindParam(':nazwisko', $nazwisko);
            $stmt->bindParam(':stanowisko', $stanowisko);
            $stmt->bindParam(':typPozwolenia', $typPozwolenia);
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':haslo', $haslo);
            $stmt->bindParam(':statusAktywnosci', $statusAktywnosci);
            $stmt->bindParam(':ulica', $ulica);
            $stmt->bindParam(':numerDomu', $numerDomu);
            $stmt->bindParam(':numerMieszkania', $numerMieszkania);
            $stmt->bindParam(':kodPocztowy', $kodPocztowy);
            $stmt->bindParam(':miejscowosc', $miejscowosc);
            $stmt->execute();

            return redirect()->route('pracownik.index')->with('success', 'Pracownik został zaktualizowany.');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            \Log::error('Błąd podczas aktualizacji pracownika: ' . $errorMessage);

            if (preg_match('/ORA-\d+: (.*?)(\s+ORA-|$)/', $errorMessage, $matches)) {
                $userFriendlyError = $matches[1];
            } else {
                $userFriendlyError = 'Wystąpił błąd podczas aktualizacji pracownika.';
            }

            return back()->withInput()->withErrors(['db_error' => $userFriendlyError]);
        }
    }

    public function readActive()
    {
        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN :cursor := PRACOWNIK_PKG.PracownikReadActive(); END;");
            $stmt->bindParam(':cursor', $cursor, \PDO::PARAM_STMT);
            $stmt->execute();

            oci_execute($cursor, OCI_DEFAULT);

            $pracownicy = [];
            while ($row = oci_fetch_assoc($cursor)) {
                $pracownik = new \stdClass();
                foreach ($row as $key => $value) {
                    $lowercaseKey = strtolower($key);
                    $pracownik->$lowercaseKey = $value;
                }
                $pracownicy[] = $pracownik;
            }

            oci_free_statement($cursor);

            $filterActive = true;
            return view('pracownik.index', compact('pracownicy', 'filterActive'))
                ->with('filterActive', true);
        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania aktywnych pracowników: ' . $e->getMessage());
            return view('pracownik.index', ['pracownicy' => []])
                ->with('error', 'Wystąpił błąd podczas pobierania aktywnych pracowników.');
        }
    }

    public function delete($id)
    {
        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN PRACOWNIK_PKG.PracownikDelete(:pracownikID); END;");
            $stmt->bindParam(':pracownikID', $id);
            $stmt->execute();

            return redirect()->route('pracownik.index')->with('success', 'Pracownik został pomyślnie dezaktywowany.');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            if (preg_match('/ORA-20004/', $errorMessage)) {
                return redirect()->route('pracownik.index')
                    ->with('success', 'Pracownik został pomyślnie dezaktywowany.');
            }

            $userFriendlyError = 'Wystąpił błąd podczas dezaktywacji pracownika.';

            if (preg_match('/ORA-\d+: ([^:]+)/', $errorMessage, $matches)) {
                $userFriendlyError = trim($matches[1]);
            }
            return redirect()->route('pracownik.index')->with('error', $userFriendlyError);
        }
    }

    public function read()
    {
        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN :cursor := PRACOWNIK_PKG.PracownikRead(); END;");
            $stmt->bindParam(':cursor', $cursor, \PDO::PARAM_STMT);
            $stmt->execute();

            oci_execute($cursor, OCI_DEFAULT);

            $pracownicy = [];
            while ($row = oci_fetch_assoc($cursor)) {
                $pracownik = new \stdClass();
                foreach ($row as $key => $value) {
                    $lowercaseKey = strtolower($key);
                    $pracownik->$lowercaseKey = $value;
                }
                $pracownicy[] = $pracownik;
            }

            oci_free_statement($cursor);

            return view('pracownik.index', compact('pracownicy'));
        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania pracowników: ' . $e->getMessage());
            return view('pracownik.index', ['pracownicy' => []])->with('error', 'Wystąpił błąd podczas pobierania pracowników.');
        }
    }

    public function readById($id)
    {
        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN :cursor := PRACOWNIK_PKG.PracownikReadById(:pracownikID); END;");
            $stmt->bindParam(':cursor', $cursor, \PDO::PARAM_STMT);
            $stmt->bindParam(':pracownikID', $id);
            $stmt->execute();

            oci_execute($cursor, OCI_DEFAULT);

            $pracownik = null;
            if ($row = oci_fetch_assoc($cursor)) {
                $pracownik = new \stdClass();
                foreach ($row as $key => $value) {
                    $lowercaseKey = strtolower($key);
                    $pracownik->$lowercaseKey = $value;
                }
            }

            oci_free_statement($cursor);

            return $pracownik;
        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania pracownika: ' . $e->getMessage());
            return null;
        }
    }

    public function createForm()
    {
        return view('pracownik.form');
    }

    public function editForm($id)
    {
        $pracownik = $this->readById($id);

        if (!$pracownik) {
            return redirect()->route('pracownik.index')->with('error', 'Pracownik nie został znaleziony.');
        }

        $adresId = $pracownik->adresid;
        $adresController = new AdresController();
        $adres = $adresController->readById($adresId);

        return view('pracownik.form', compact('pracownik', 'adres'));
    }
}
