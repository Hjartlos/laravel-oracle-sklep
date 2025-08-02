<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdresController extends Controller
{
    public function create(Request $request)
    {
        $ulica = $request->ulica;
        $numerDomu = $request->numerDomu;
        $numerMieszkania = $request->numerMieszkania;
        $kodPocztowy = $request->kodPocztowy;
        $miejscowosc = $request->miejscowosc;
        $newAdresID = null;

        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN ADRES_PKG.AdresCreate(:ulica, :numerDomu, :numerMieszkania, :kodPocztowy, :miejscowosc, :newAdresID); END;");
            $stmt->bindParam(':ulica', $ulica);
            $stmt->bindParam(':numerDomu', $numerDomu);
            $stmt->bindParam(':numerMieszkania', $numerMieszkania);
            $stmt->bindParam(':kodPocztowy', $kodPocztowy);
            $stmt->bindParam(':miejscowosc', $miejscowosc);
            $stmt->bindParam(':newAdresID', $newAdresID, \PDO::PARAM_INT | \PDO::PARAM_INPUT_OUTPUT, 32);
            $stmt->execute();

            return redirect()->route('adres.index')->with('success', 'Adres został utworzony.');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            \Log::error('Błąd podczas wykonania procedury: ' . $errorMessage);

            if (preg_match('/ORA-\d+: Błąd podczas tworzenia adresu: ORA-\d+: (.*?)(\s+ORA-|$)/', $errorMessage, $matches)) {
                $userFriendlyError = $matches[1];
            } else {
                $userFriendlyError = 'Wystąpił błąd podczas zapisywania adresu.';
            }

            $adres = new \stdClass();
            $adres->adresid = null;
            $adres->ulica = $ulica;
            $adres->numerdomu = $numerDomu;
            $adres->numermieszkania = $numerMieszkania;
            $adres->kodpocztowy = $kodPocztowy;
            $adres->miejscowosc = $miejscowosc;

            return view('adres.form', compact('adres'))
                ->withErrors(['db_error' => $userFriendlyError]);
        }
    }

    public function update(Request $request, $id)
    {
        $ulica = $request->ulica;
        $numerDomu = $request->numerDomu;
        $numerMieszkania = $request->numerMieszkania;
        $kodPocztowy = $request->kodPocztowy;
        $miejscowosc = $request->miejscowosc;

        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN ADRES_PKG.AdresUpdate(:adresID, :ulica, :numerDomu, :numerMieszkania, :kodPocztowy, :miejscowosc); END;");
            $stmt->bindParam(':adresID', $id);
            $stmt->bindParam(':ulica', $ulica);
            $stmt->bindParam(':numerDomu', $numerDomu);
            $stmt->bindParam(':numerMieszkania', $numerMieszkania);
            $stmt->bindParam(':kodPocztowy', $kodPocztowy);
            $stmt->bindParam(':miejscowosc', $miejscowosc);
            $stmt->execute();

            return redirect()->route('adres.index')->with('success', 'Adres został zaktualizowany.');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            if (preg_match('/ORA-\d+: Błąd podczas aktualizacji adresu: ORA-\d+: (.*?)(\s+ORA-|$)/', $errorMessage, $matches)) {
                $userFriendlyError = $matches[1];
            } else {
                $userFriendlyError = 'Wystąpił błąd podczas zapisywania adresu.';
            }

            $adres = new \stdClass();
            $adres->adresid = $id;
            $adres->ulica = $ulica;
            $adres->numerdomu = $numerDomu;
            $adres->numermieszkania = $numerMieszkania;
            $adres->kodpocztowy = $kodPocztowy;
            $adres->miejscowosc = $miejscowosc;

            return view('adres.form', compact('adres'))
                ->withErrors(['db_error' => $userFriendlyError]);
        }
    }

    public function delete($id)
    {
        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN ADRES_PKG.AdresDelete(:adresID); END;");
            $stmt->bindParam(':adresID', $id);
            $stmt->execute();

            return redirect()->route('adres.index')->with('success', 'Adres został usunięty.');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            \Log::error('Błąd podczas usuwania adresu: ' . $errorMessage);

            if (preg_match('/ORA-\d+: (.*?)(\s+ORA-|$)/', $errorMessage, $matches)) {
                $userFriendlyError = $matches[1];
            } else {
                $userFriendlyError = 'Wystąpił błąd podczas usuwania adresu.';
            }

            return redirect()->route('adres.index')->with('error', $userFriendlyError);
        }
    }

    public function read()
    {
        $pdo = DB::getPdo();
        $stmt = $pdo->prepare("BEGIN :cursor := ADRES_PKG.ADRESREAD(); END;");
        $stmt->bindParam(':cursor', $cursor, \PDO::PARAM_STMT);
        $stmt->execute();

        oci_execute($cursor, OCI_DEFAULT);

        $adresy = [];
        while ($row = oci_fetch_assoc($cursor)) {
            $adres = new \stdClass();
            foreach ($row as $key => $value) {
                $lowercaseKey = strtolower($key);
                $adres->$lowercaseKey = $value;
            }
            $adresy[] = $adres;
        }

        oci_free_statement($cursor);

        return view('adres.index', compact('adresy'));
    }

    public function readById($id)
    {
        $pdo = DB::getPdo();
        $stmt = $pdo->prepare("BEGIN :cursor := ADRES_PKG.ADRESREADBYID(:adresID); END;");
        $stmt->bindParam(':cursor', $cursor, \PDO::PARAM_STMT);
        $stmt->bindParam(':adresID', $id);
        $stmt->execute();

        oci_execute($cursor, OCI_DEFAULT);

        $adres = null;
        if ($row = oci_fetch_assoc($cursor)) {
            $adres = new \stdClass();
            foreach ($row as $key => $value) {
                $lowercaseKey = strtolower($key);
                $adres->$lowercaseKey = $value;
            }
        }

        oci_free_statement($cursor);

        return $adres;
    }

    public function readByMiasto(Request $request)
    {
        $miasto = $request->query('miasto', '');

        $pdo = DB::getPdo();
        $stmt = $pdo->prepare("BEGIN :cursor := ADRES_PKG.ADRESREADBYMIASTO(:miejscowosc); END;");
        $stmt->bindParam(':cursor', $cursor, \PDO::PARAM_STMT);
        $stmt->bindParam(':miejscowosc', $miasto);
        $stmt->execute();

        oci_execute($cursor, OCI_DEFAULT);

        $adresy = [];
        while ($row = oci_fetch_assoc($cursor)) {
            $adres = new \stdClass();
            foreach ($row as $key => $value) {
                $lowercaseKey = strtolower($key);
                $adres->$lowercaseKey = $value;
            }
            $adresy[] = $adres;
        }

        oci_free_statement($cursor);

        return view('adres.index', compact('adresy', 'miasto'));
    }

    public function createForm()
    {
        return view('adres.form');
    }

    public function editForm($id)
    {
        $adres = $this->readById($id);

        if (!$adres) {
            return redirect()->route('adres.index')->with('error', 'Adres nie został znaleziony.');
        }

        return view('adres.form', compact('adres'));
    }
}
