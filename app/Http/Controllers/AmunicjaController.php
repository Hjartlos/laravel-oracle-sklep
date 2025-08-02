<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AmunicjaController extends Controller
{
    public function create(Request $request)
    {
        $nazwa = $request->nazwa;
        $cena = $request->cena;
        $ilosc = $request->ilosc;
        $newAmunicjaID = null;

        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN AMMO_PKG.AmmoCreate(:nazwa, :cena, :ilosc, :newAmunicjaID); END;");
            $stmt->bindParam(':nazwa', $nazwa);
            $stmt->bindParam(':cena', $cena);
            $stmt->bindParam(':ilosc', $ilosc);
            $stmt->bindParam(':newAmunicjaID', $newAmunicjaID, \PDO::PARAM_INT | \PDO::PARAM_INPUT_OUTPUT, 32);
            $stmt->execute();

            return redirect()->route('amunicja.index')->with('success', 'Amunicja została dodana.');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            \Log::error('Błąd podczas wykonania procedury: ' . $errorMessage);

            if (preg_match('/ORA-\d+: Błąd podczas dodawania amunicji: ORA-\d+: (.*?)(\s+ORA-|$)/', $errorMessage, $matches)) {
                $userFriendlyError = $matches[1];
            } else {
                $userFriendlyError = 'Wystąpił błąd podczas zapisywania amunicji.';
            }

            $amunicja = new \stdClass();
            $amunicja->amunicjaid = null;
            $amunicja->nazwa = $nazwa;
            $amunicja->cena = $cena;
            $amunicja->ilosc = $ilosc;

            return view('amunicja.form', compact('amunicja'))
                ->withErrors(['db_error' => $userFriendlyError]);
        }
    }

    public function update(Request $request, $id)
    {
        $nazwa = $request->nazwa;
        $cena = $request->cena;
        $ilosc = $request->ilosc;

        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN AMMO_PKG.AmmoUpdate(:amunicjaID, :nazwa, :cena, :ilosc); END;");
            $stmt->bindParam(':amunicjaID', $id);
            $stmt->bindParam(':nazwa', $nazwa);
            $stmt->bindParam(':cena', $cena);
            $stmt->bindParam(':ilosc', $ilosc);
            $stmt->execute();

            return redirect()->route('amunicja.index')->with('success', 'Amunicja została zaktualizowana.');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            if (preg_match('/ORA-\d+: Błąd podczas aktualizacji amunicji: ORA-\d+: (.*?)(\s+ORA-|$)/', $errorMessage, $matches)) {
                $userFriendlyError = $matches[1];
            } else {
                $userFriendlyError = 'Wystąpił błąd podczas aktualizacji amunicji.';
            }

            $amunicja = new \stdClass();
            $amunicja->amunicjaid = $id;
            $amunicja->nazwa = $nazwa;
            $amunicja->cena = $cena;
            $amunicja->ilosc = $ilosc;

            return view('amunicja.form', compact('amunicja'))
                ->withErrors(['db_error' => $userFriendlyError]);
        }
    }

    public function delete($id)
    {
        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN AMMO_PKG.AmmoDelete(:amunicjaID); END;");
            $stmt->bindParam(':amunicjaID', $id);
            $stmt->execute();

            return redirect()->route('amunicja.index')->with('success', 'Amunicja została usunięta.');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            \Log::error('Błąd podczas usuwania amunicji: ' . $errorMessage);

            if (preg_match('/ORA-\d+: (.*?)(\s+ORA-|$)/', $errorMessage, $matches)) {
                $userFriendlyError = $matches[1];
            } else {
                $userFriendlyError = 'Wystąpił błąd podczas usuwania amunicji.';
            }
            return redirect()->route('amunicja.index')->with('error', $userFriendlyError);
        }
    }

    public function read()
    {
        $pdo = DB::getPdo();
        $stmt = $pdo->prepare("BEGIN :cursor := AMMO_PKG.AmmoRead(); END;");
        $stmt->bindParam(':cursor', $cursor, \PDO::PARAM_STMT);
        $stmt->execute();

        oci_execute($cursor, OCI_DEFAULT);

        $amunicje = [];
        while ($row = oci_fetch_assoc($cursor)) {
            $amunicja = new \stdClass();
            foreach ($row as $key => $value) {
                $lowercaseKey = strtolower($key);
                $amunicja->$lowercaseKey = $value;
            }
            $amunicje[] = $amunicja;
        }

        oci_free_statement($cursor);

        return view('amunicja.index', compact('amunicje'));
    }

    public function readById($id)
    {
        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN :cursor := AMMO_PKG.AmmoReadById(:amunicjaID); END;");
            $stmt->bindParam(':cursor', $cursor, \PDO::PARAM_STMT);
            $stmt->bindParam(':amunicjaID', $id);
            $stmt->execute();

            oci_execute($cursor, OCI_DEFAULT);

            $amunicja = null;
            if ($row = oci_fetch_assoc($cursor)) {
                $amunicja = new \stdClass();
                foreach ($row as $key => $value) {
                    $lowercaseKey = strtolower($key);
                    $amunicja->$lowercaseKey = $value;
                }
            }

            oci_free_statement($cursor);

            return $amunicja;
        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania amunicji: ' . $e->getMessage());
            return null;
        }
    }

    public function readByNazwa(Request $request)
    {
        $nazwa = $request->query('nazwa', '');

        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN :cursor := AMMO_PKG.AmmoReadByNazwa(:nazwa); END;");
            $stmt->bindParam(':cursor', $cursor, \PDO::PARAM_STMT);
            $stmt->bindParam(':nazwa', $nazwa);
            $stmt->execute();

            oci_execute($cursor, OCI_DEFAULT);

            $amunicje = [];
            while ($row = oci_fetch_assoc($cursor)) {
                $amunicja = new \stdClass();
                foreach ($row as $key => $value) {
                    $lowercaseKey = strtolower($key);
                    $amunicja->$lowercaseKey = $value;
                }
                $amunicje[] = $amunicja;
            }

            oci_free_statement($cursor);

            return view('amunicja.index', compact('amunicje', 'nazwa'));
        } catch (\Exception $e) {
            \Log::error('Błąd podczas filtrowania amunicji: ' . $e->getMessage());
            return redirect()->route('amunicja.index')->with('error', 'Wystąpił błąd podczas wyszukiwania amunicji.');
        }
    }

    public function createForm()
    {
        return view('amunicja.form');
    }

    public function editForm($id)
    {
        $amunicja = $this->readById($id);

        if (!$amunicja) {
            return redirect()->route('amunicja.index')->with('error', 'Amunicja nie została znaleziona.');
        }

        return view('amunicja.form', compact('amunicja'));
    }
}
