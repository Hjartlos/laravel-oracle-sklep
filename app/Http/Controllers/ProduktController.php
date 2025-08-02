<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduktController extends Controller
{
    public function create(Request $request)
    {
        $kategoriaId = $request->kategoriaId;
        $amunicjaId = $request->amunicjaId;
        $nazwa = $request->nazwa;
        $numerSeryjny = $request->numerSeryjny;
        $cena = $request->cena;

        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN PRODUKT_PKG.ProduktCreate(:kategoriaId, :amunicjaId, :nazwa, :numerSeryjny, :cena); END;");
            $stmt->bindParam(':kategoriaId', $kategoriaId);
            $stmt->bindParam(':amunicjaId', $amunicjaId);
            $stmt->bindParam(':nazwa', $nazwa);
            $stmt->bindParam(':numerSeryjny', $numerSeryjny);
            $stmt->bindParam(':cena', $cena);
            $stmt->execute();

            return redirect()->route('produkt.index')->with('success', 'Produkt został utworzony.');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            \Log::error('Błąd podczas tworzenia produktu: ' . $errorMessage);

            if (preg_match('/ORA-\d+: (.*?)(\s+ORA-|$)/', $errorMessage, $matches)) {
                $userFriendlyError = $matches[1];
            } else {
                $userFriendlyError = 'Wystąpił błąd podczas zapisywania produktu.';
            }

            $kategorie = $this->getKategorie();
            $amunicje = $this->getAmunicje();

            return view('produkt.form', compact('kategorie', 'amunicje'))
                ->withInput()
                ->withErrors(['db_error' => $userFriendlyError]);
        }
    }

    public function update(Request $request, $id)
    {
        $kategoriaId = $request->kategoriaId;
        $amunicjaId = $request->amunicjaId;
        $nazwa = $request->nazwa;
        $numerSeryjny = $request->numerSeryjny;
        $cena = $request->cena;

        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN PRODUKT_PKG.ProduktUpdate(:produktId, :kategoriaId, :amunicjaId, :nazwa, :numerSeryjny, :cena); END;");
            $stmt->bindParam(':produktId', $id);
            $stmt->bindParam(':kategoriaId', $kategoriaId);
            $stmt->bindParam(':amunicjaId', $amunicjaId);
            $stmt->bindParam(':nazwa', $nazwa);
            $stmt->bindParam(':numerSeryjny', $numerSeryjny);
            $stmt->bindParam(':cena', $cena);
            $stmt->execute();

            return redirect()->route('produkt.index')->with('success', 'Produkt został zaktualizowany.');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            \Log::error('Błąd podczas aktualizacji produktu: ' . $errorMessage);

            if (preg_match('/ORA-\d+: (.*?)(\s+ORA-|$)/', $errorMessage, $matches)) {
                $userFriendlyError = $matches[1];
            } else {
                $userFriendlyError = 'Wystąpił błąd podczas aktualizacji produktu.';
            }

            return back()->withInput()->withErrors(['db_error' => $userFriendlyError]);
        }
    }

    public function readActive()
    {
        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN :cursor := PRODUKT_PKG.ProduktReadAvailable(:transakcjaId); END;");
            $stmt->bindParam(':cursor', $cursor, \PDO::PARAM_STMT);
            $transakcjaId = null;
            $stmt->bindParam(':transakcjaId', $transakcjaId);
            $stmt->execute();

            oci_execute($cursor, OCI_DEFAULT);

            $produkty = [];
            while ($row = oci_fetch_assoc($cursor)) {
                $produkt = new \stdClass();
                foreach ($row as $key => $value) {
                    $lowercaseKey = strtolower($key);
                    $produkt->$lowercaseKey = $value;
                }
                $produkty[] = $produkt;
            }

            oci_free_statement($cursor);

            $filterActive = true;
            return view('produkt.index', compact('produkty', 'filterActive'))
                ->with('filterActive', true);
        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania dostępnych produktów: ' . $e->getMessage());
            return view('produkt.index', ['produkty' => []])
                ->with('error', 'Wystąpił błąd podczas pobierania dostępnych produktów.');
        }
    }

    public function delete($id)
    {
        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN PRODUKT_PKG.ProduktDelete(:produktId); END;");
            $stmt->bindParam(':produktId', $id);
            $stmt->execute();

            return redirect()->route('produkt.index')->with('success', 'Produkt został usunięty.');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            \Log::error('Błąd podczas usuwania produktu: ' . $errorMessage);

            if (preg_match('/ORA-\d+: (.*?)(\s+ORA-|$)/', $errorMessage, $matches)) {
                $userFriendlyError = $matches[1];
            } else {
                $userFriendlyError = 'Wystąpił błąd podczas usuwania produktu.';
            }

            return redirect()->route('produkt.index')->with('error', $userFriendlyError);
        }
    }

    public function read()
    {
        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN :cursor := PRODUKT_PKG.ProduktRead(); END;");
            $stmt->bindParam(':cursor', $cursor, \PDO::PARAM_STMT);
            $stmt->execute();

            oci_execute($cursor, OCI_DEFAULT);

            $produkty = [];
            while ($row = oci_fetch_assoc($cursor)) {
                $produkt = new \stdClass();
                foreach ($row as $key => $value) {
                    $lowercaseKey = strtolower($key);
                    $produkt->$lowercaseKey = $value;
                }
                $produkty[] = $produkt;
            }

            oci_free_statement($cursor);

            return view('produkt.index', compact('produkty'));
        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania produktów: ' . $e->getMessage());
            return view('produkt.index', ['produkty' => []])->with('error', 'Wystąpił błąd podczas pobierania produktów.');
        }
    }

    public function readById($id)
    {
        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN :cursor := PRODUKT_PKG.ProduktReadById(:produktId); END;");
            $stmt->bindParam(':cursor', $cursor, \PDO::PARAM_STMT);
            $stmt->bindParam(':produktId', $id);
            $stmt->execute();

            oci_execute($cursor, OCI_DEFAULT);

            $produkt = null;
            if ($row = oci_fetch_assoc($cursor)) {
                $produkt = new \stdClass();
                foreach ($row as $key => $value) {
                    $lowercaseKey = strtolower($key);
                    $produkt->$lowercaseKey = $value;
                }
            }

            oci_free_statement($cursor);

            return $produkt;
        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania produktu: ' . $e->getMessage());
            return null;
        }
    }

    public function createForm()
    {
        $kategorie = $this->getKategorie();
        $amunicje = $this->getAmunicje();

        return view('produkt.form', compact('kategorie', 'amunicje'));
    }

    public function editForm($id)
    {
        $produkt = $this->readById($id);

        if (!$produkt) {
            return redirect()->route('produkt.index')->with('error', 'Produkt nie został znaleziony.');
        }

        $kategorie = $this->getKategorie();
        $amunicje = $this->getAmunicje();

        return view('produkt.form', compact('produkt', 'kategorie', 'amunicje'));
    }

    private function getKategorie()
    {
        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN :cursor := KATEGORIA_PKG.KategoriaRead(); END;");
            $stmt->bindParam(':cursor', $cursor, \PDO::PARAM_STMT);
            $stmt->execute();

            oci_execute($cursor, OCI_DEFAULT);

            $kategorie = [];
            while ($row = oci_fetch_assoc($cursor)) {
                $kategoria = new \stdClass();
                foreach ($row as $key => $value) {
                    $lowercaseKey = strtolower($key);
                    $kategoria->$lowercaseKey = $value;
                }
                $kategorie[] = $kategoria;
            }

            oci_free_statement($cursor);

            return $kategorie;
        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania kategorii: ' . $e->getMessage());
            return [];
        }
    }

    private function getAmunicje()
    {
        try {
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

            return $amunicje;
        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania amunicji: ' . $e->getMessage());
            return [];
        }
    }
}
