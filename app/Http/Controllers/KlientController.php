<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KlientController extends Controller
{
    public function create(Request $request)
    {
        $imie = $request->imie;
        $nazwisko = $request->nazwisko;
        $numerPozwolenia = $request->numerPozwolenia;
        $typPozwolenia = $request->typPozwolenia;
        $dataWaznosci = $request->dataWaznosci;
        $email = $request->email;
        $telefon = $request->telefon;
        $ulica = $request->ulica;
        $numerDomu = $request->numerDomu;
        $numerMieszkania = $request->numerMieszkania;
        $kodPocztowy = $request->kodPocztowy;
        $miejscowosc = $request->miejscowosc;

        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN KLIENT_PKG.KlientCreate(:imie, :nazwisko, :numerPozwolenia, :typPozwolenia, :dataWaznosci, :email, :telefon, :ulica, :numerDomu, :numerMieszkania, :kodPocztowy, :miejscowosc); END;");
            $stmt->bindParam(':imie', $imie);
            $stmt->bindParam(':nazwisko', $nazwisko);
            $stmt->bindParam(':numerPozwolenia', $numerPozwolenia);
            $stmt->bindParam(':typPozwolenia', $typPozwolenia);
            $stmt->bindParam(':dataWaznosci', $dataWaznosci);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telefon', $telefon);
            $stmt->bindParam(':ulica', $ulica);
            $stmt->bindParam(':numerDomu', $numerDomu);
            $stmt->bindParam(':numerMieszkania', $numerMieszkania);
            $stmt->bindParam(':kodPocztowy', $kodPocztowy);
            $stmt->bindParam(':miejscowosc', $miejscowosc);
            $stmt->execute();

            return redirect()->route('klient.index')->with('success', 'Klient został utworzony.');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            \Log::error('Błąd podczas tworzenia klienta: ' . $errorMessage);

            if (preg_match('/ORA-\d+: (.*?)(\s+ORA-|$)/', $errorMessage, $matches)) {
                $userFriendlyError = $matches[1];
            } else {
                $userFriendlyError = 'Wystąpił błąd podczas zapisywania klienta.';
            }

            return back()->withInput()->withErrors(['db_error' => $userFriendlyError]);
        }
    }

    public function update(Request $request, $id)
    {
        $imie = $request->imie;
        $nazwisko = $request->nazwisko;
        $numerPozwolenia = $request->numerPozwolenia;
        $typPozwolenia = $request->typPozwolenia;
        $dataWaznosci = $request->dataWaznosci;
        $email = $request->email;
        $telefon = $request->telefon;
        $statusAktywnosci = $request->statusAktywnosci;
        $ulica = $request->ulica;
        $numerDomu = $request->numerDomu;
        $numerMieszkania = $request->numerMieszkania;
        $kodPocztowy = $request->kodPocztowy;
        $miejscowosc = $request->miejscowosc;

        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN KLIENT_PKG.KlientUpdate(:klientID, :imie, :nazwisko, :numerPozwolenia, :typPozwolenia, :dataWaznosci, :email, :telefon, :statusAktywnosci, :ulica, :numerDomu, :numerMieszkania, :kodPocztowy, :miejscowosc); END;");
            $stmt->bindParam(':klientID', $id);
            $stmt->bindParam(':imie', $imie);
            $stmt->bindParam(':nazwisko', $nazwisko);
            $stmt->bindParam(':numerPozwolenia', $numerPozwolenia);
            $stmt->bindParam(':typPozwolenia', $typPozwolenia);
            $stmt->bindParam(':dataWaznosci', $dataWaznosci);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telefon', $telefon);
            $stmt->bindParam(':statusAktywnosci', $statusAktywnosci);
            $stmt->bindParam(':ulica', $ulica);
            $stmt->bindParam(':numerDomu', $numerDomu);
            $stmt->bindParam(':numerMieszkania', $numerMieszkania);
            $stmt->bindParam(':kodPocztowy', $kodPocztowy);
            $stmt->bindParam(':miejscowosc', $miejscowosc);
            $stmt->execute();

            return redirect()->route('klient.index')->with('success', 'Klient został zaktualizowany.');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            \Log::error('Błąd podczas aktualizacji klienta: ' . $errorMessage);

            if (preg_match('/ORA-\d+: (.*?)(\s+ORA-|$)/', $errorMessage, $matches)) {
                $userFriendlyError = $matches[1];
            } else {
                $userFriendlyError = 'Wystąpił błąd podczas aktualizacji klienta.';
            }

            return back()->withInput()->withErrors(['db_error' => $userFriendlyError]);
        }
    }

    public function delete($id)
    {
        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN KLIENT_PKG.KlientDelete(:klientID); END;");
            $stmt->bindParam(':klientID', $id);
            $stmt->execute();

            return redirect()->route('klient.index')->with('success', 'Klient został pomyślnie dezaktywowany.');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            if (preg_match('/ORA-20004/', $errorMessage)) {
                return redirect()->route('klient.index')
                    ->with('success', 'Klient został pomyślnie dezaktywowany.');
            }
            $userFriendlyError = 'Wystąpił błąd podczas dezaktywacji klienta.';

            if (preg_match('/ORA-\d+: ([^:]+)/', $errorMessage, $matches)) {
                $userFriendlyError = trim($matches[1]);
            }

            return redirect()->route('klient.index')->with('error', $userFriendlyError);
        }
    }

    public function readActive()
    {
        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN :cursor := KLIENT_PKG.KlientReadActive(); END;");
            $stmt->bindParam(':cursor', $cursor, \PDO::PARAM_STMT);
            $stmt->execute();

            oci_execute($cursor, OCI_DEFAULT);

            $klienci = [];
            while ($row = oci_fetch_assoc($cursor)) {
                $klient = new \stdClass();
                foreach ($row as $key => $value) {
                    $lowercaseKey = strtolower($key);
                    $klient->$lowercaseKey = $value;
                }
                $klienci[] = $klient;
            }

            oci_free_statement($cursor);

            $filterActive = true;
            return view('klient.index', compact('klienci', 'filterActive'))
                ->with('filterActive', true);
        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania aktywnych klientów: ' . $e->getMessage());
            return view('klient.index', ['klienci' => []])
                ->with('error', 'Wystąpił błąd podczas pobierania aktywnych klientów.');
        }
    }

    public function read()
    {
        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN :cursor := KLIENT_PKG.KlientRead(); END;");
            $stmt->bindParam(':cursor', $cursor, \PDO::PARAM_STMT);
            $stmt->execute();

            oci_execute($cursor, OCI_DEFAULT);

            $klienci = [];
            while ($row = oci_fetch_assoc($cursor)) {
                $klient = new \stdClass();
                foreach ($row as $key => $value) {
                    $lowercaseKey = strtolower($key);
                    $klient->$lowercaseKey = $value;
                }
                $klienci[] = $klient;
            }

            oci_free_statement($cursor);

            return view('klient.index', compact('klienci'));
        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania klientów: ' . $e->getMessage());
            return view('klient.index', ['klienci' => []])->with('error', 'Wystąpił błąd podczas pobierania klientów.');
        }
    }

    public function readById($id)
    {
        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN :cursor := KLIENT_PKG.KlientReadById(:klientID); END;");
            $stmt->bindParam(':cursor', $cursor, \PDO::PARAM_STMT);
            $stmt->bindParam(':klientID', $id);
            $stmt->execute();

            oci_execute($cursor, OCI_DEFAULT);

            $klient = null;
            if ($row = oci_fetch_assoc($cursor)) {
                $klient = new \stdClass();
                foreach ($row as $key => $value) {
                    $lowercaseKey = strtolower($key);
                    $klient->$lowercaseKey = $value;
                }
            }

            oci_free_statement($cursor);

            return $klient;
        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania klienta: ' . $e->getMessage());
            return null;
        }
    }

    public function show($id)
    {
        $klient = $this->readById($id);

        if (!$klient) {
            return redirect()->route('klient.index')->with('error', 'Klient nie został znaleziony.');
        }

        $adresId = $klient->adresid;
        $adresController = new AdresController();
        $adres = $adresController->readById($adresId);

        return view('klient.show', compact('klient', 'adres'));
    }

    public function createForm()
    {
        return view('klient.form');
    }

    public function editForm($id)
    {
        $klient = $this->readById($id);

        if (!$klient) {
            return redirect()->route('klient.index')->with('error', 'Klient nie został znaleziony.');
        }

        $adresId = $klient->adresid;
        $adresController = new AdresController();
        $adres = $adresController->readById($adresId);

        return view('klient.form', compact('klient', 'adres'));
    }
}
