<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransakcjaController extends Controller
{
    public function create(Request $request)
    {
        $klientId = $request->klientid;
        $pracownikId = $request->pracownikid;
        $produkty = $request->produkty ?? [];
        $iloscAmunicji = $request->iloscAmunicji ?? [];

        DB::beginTransaction();
        try {
            $pdo = DB::getPdo();

            $stmt = $pdo->prepare("BEGIN TRANSAKCJA_PKG.TRANSAKCJACREATE(:klientID, :pracownikID, :newTransakcjaID); END;");
            $stmt->bindParam(':klientID', $klientId);
            $stmt->bindParam(':pracownikID', $pracownikId);
            $stmt->bindParam(':newTransakcjaID', $newTransakcjaID, \PDO::PARAM_INT | \PDO::PARAM_INPUT_OUTPUT, 32);
            $stmt->execute();

            for ($i = 0; $i < count($produkty); $i++) {
                if (!empty($produkty[$i])) {
                    $produktId = $produkty[$i];
                    $ilosc = isset($iloscAmunicji[$i]) ? $iloscAmunicji[$i] : 0;

                    $stmtProdukt = $pdo->prepare("BEGIN TRANSAKCJA_PKG.TRANSAKCJAPRODUKTCREATE(:transakcjaID, :produktID, 1, :iloscAmunicji); END;");
                    $stmtProdukt->bindParam(':transakcjaID', $newTransakcjaID);
                    $stmtProdukt->bindParam(':produktID', $produktId);
                    $stmtProdukt->bindParam(':iloscAmunicji', $ilosc);
                    $stmtProdukt->execute();
                }
            }

            DB::commit();

            return redirect()->route('transakcja.index')->with('success', 'Transakcja została utworzona.');
        } catch (\Exception $e) {
            DB::rollBack();

            $errorMessage = $e->getMessage();
            \Log::error('Błąd podczas tworzenia transakcji: ' . $errorMessage);

            if (preg_match('/ORA-\d+: (.*?)(\s+ORA-|$)/', $errorMessage, $matches)) {
                $userFriendlyError = $matches[1];
            } else {
                $userFriendlyError = 'Wystąpił błąd podczas zapisywania transakcji.';
            }

            return back()->withInput()->withErrors(['db_error' => $userFriendlyError]);
        }
    }

    public function update(Request $request, $id)
    {
        $klientId = $request->klientid;
        $pracownikId = $request->pracownikid;
        $produkty = $request->produkty ?? [];
        $iloscAmunicji = $request->iloscAmunicji ?? [];

        DB::beginTransaction();
        try {
            $pdo = DB::getPdo();

            $stmt = $pdo->prepare("BEGIN TRANSAKCJA_PKG.TRANSAKCJAUPDATE(:transakcjaID, :klientID, :pracownikID, null, null); END;");
            $stmt->bindParam(':transakcjaID', $id);
            $stmt->bindParam(':klientID', $klientId);
            $stmt->bindParam(':pracownikID', $pracownikId);
            $stmt->execute();

            $currentProdukty = $this->getTransakcjaProdukty($id);
            $currentProduktIds = [];
            foreach ($currentProdukty as $produkt) {
                $currentProduktIds[$produkt->produktid] = $produkt;
            }

            $formProduktIds = [];
            for ($i = 0; $i < count($produkty); $i++) {
                if (!empty($produkty[$i])) {
                    $produktId = $produkty[$i];
                    $ilosc = isset($iloscAmunicji[$i]) ? $iloscAmunicji[$i] : 0;
                    $formProduktIds[$produktId] = $ilosc;
                }
            }

            foreach ($formProduktIds as $produktId => $ilosc) {
                if (isset($currentProduktIds[$produktId])) {
                    if ($currentProduktIds[$produktId]->ilosc_amunicji != $ilosc) {
                        $stmtUpdate = $pdo->prepare("BEGIN TRANSAKCJA_PKG.TRANSAKCJAPRODUKTUPDATE(:transakcjaID, :produktID, :iloscAmunicji); END;");
                        $stmtUpdate->bindParam(':transakcjaID', $id);
                        $stmtUpdate->bindParam(':produktID', $produktId);
                        $stmtUpdate->bindParam(':iloscAmunicji', $ilosc);
                        $stmtUpdate->execute();
                    }
                    unset($currentProduktIds[$produktId]);
                } else {
                    $stmtProdukt = $pdo->prepare("BEGIN TRANSAKCJA_PKG.TRANSAKCJAPRODUKTCREATE(:transakcjaID, :produktID, 1, :iloscAmunicji); END;");
                    $stmtProdukt->bindParam(':transakcjaID', $id);
                    $stmtProdukt->bindParam(':produktID', $produktId);
                    $stmtProdukt->bindParam(':iloscAmunicji', $ilosc);
                    $stmtProdukt->execute();
                }
            }

            foreach ($currentProduktIds as $produktId => $produkt) {
                $stmtDelete = $pdo->prepare("BEGIN TRANSAKCJA_PKG.TRANSAKCJAPRODUKTDELETE(:transakcjaID, :produktID); END;");
                $stmtDelete->bindParam(':transakcjaID', $id);
                $stmtDelete->bindParam(':produktID', $produktId);
                $stmtDelete->execute();
            }

            DB::commit();

            return redirect()->route('transakcja.index')->with('success', 'Transakcja została zaktualizowana.');
        } catch (\Exception $e) {
            DB::rollBack();

            $errorMessage = $e->getMessage();
            \Log::error('Błąd podczas aktualizacji transakcji: ' . $errorMessage);

            if (preg_match('/ORA-\d+: (.*?)(\s+ORA-|$)/', $errorMessage, $matches)) {
                $userFriendlyError = $matches[1];
            } else {
                $userFriendlyError = 'Wystąpił błąd podczas aktualizacji transakcji.';
            }

            return back()->withInput()->withErrors(['db_error' => $userFriendlyError]);
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN TRANSAKCJA_PKG.TRANSAKCJADELETE(:transakcjaID); END;");
            $stmt->bindParam(':transakcjaID', $id);
            $stmt->execute();

            DB::commit();
            return redirect()->route('transakcja.index')->with('success', 'Transakcja została usunięta.');
        } catch (\Exception $e) {
            DB::rollBack();
            $errorMessage = $e->getMessage();
            \Log::error('Błąd podczas usuwania transakcji: ' . $errorMessage);

            if (preg_match('/ORA-\d+: (.*?)(\s+ORA-|$)/', $errorMessage, $matches)) {
                $userFriendlyError = $matches[1];
            } else {
                $userFriendlyError = 'Wystąpił błąd podczas usuwania transakcji.';
            }

            return redirect()->route('transakcja.index')->with('error', $userFriendlyError);
        }
    }

    public function readByData(Request $request)
    {
        $dataOd = $request->query('dataOd');
        $dataDo = $request->query('dataDo');

        $filterActive = (!empty($dataOd) || !empty($dataDo));

        try {
            $pdo = DB::getPdo();

            $stmt = $pdo->prepare("BEGIN :cursor := TRANSAKCJA_PKG.TRANSAKCJAREADBYDATA(:dataOd, :dataDo); END;");
            $stmt->bindParam(':cursor', $cursor, \PDO::PARAM_STMT);
            $stmt->bindParam(':dataOd', $dataOd);
            $stmt->bindParam(':dataDo', $dataDo);
            $stmt->execute();

            oci_execute($cursor, OCI_DEFAULT);

            $transakcje = [];
            while ($row = oci_fetch_assoc($cursor)) {
                $transakcja = new \stdClass();
                foreach ($row as $key => $value) {
                    $lowercaseKey = strtolower($key);
                    $transakcja->$lowercaseKey = $value;
                }
                $transakcje[] = $transakcja;
            }

            oci_free_statement($cursor);

            return view('transakcja.index', compact('transakcje', 'dataOd', 'dataDo', 'filterActive'));
        } catch (\Exception $e) {
            \Log::error('Błąd podczas filtrowania transakcji: ' . $e->getMessage());
            return view('transakcja.index', ['transakcje' => []])
                ->with('error', 'Wystąpił błąd podczas pobierania szczegółów transakcji: ' . $e->getMessage());
        }
    }

    public function read()
    {
        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN :cursor := TRANSAKCJA_PKG.TRANSAKCJAREAD(); END;");
            $stmt->bindParam(':cursor', $cursor, \PDO::PARAM_STMT);
            $stmt->execute();

            oci_execute($cursor, OCI_DEFAULT);

            $transakcje = [];
            while ($row = oci_fetch_assoc($cursor)) {
                $transakcja = new \stdClass();
                foreach ($row as $key => $value) {
                    $lowercaseKey = strtolower($key);
                    $transakcja->$lowercaseKey = $value;
                }
                $transakcje[] = $transakcja;
            }

            oci_free_statement($cursor);

            return view('transakcja.index', compact('transakcje'));
        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania transakcji: ' . $e->getMessage());
            return view('transakcja.index', ['transakcje' => []])->with('error', 'Wystąpił błąd podczas pobierania transakcji.');
        }
    }

    public function readById($id)
    {
        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN :cursor := TRANSAKCJA_PKG.TRANSAKCJAPRODUKTREAD(:transakcjaId); END;");
            $stmt->bindParam(':cursor', $cursor, \PDO::PARAM_STMT);
            $stmt->bindParam(':transakcjaId', $id);
            $stmt->execute();

            oci_execute($cursor, OCI_DEFAULT);

            $transakcja = null;
            $produkty = [];

            while ($row = oci_fetch_assoc($cursor)) {
                if (!$transakcja) {
                    $transakcja = new \stdClass();
                    foreach ($row as $key => $value) {
                        if (!str_contains(strtolower($key), 'produkt') &&
                            !str_contains(strtolower($key), 'amunicj') &&
                            !str_contains(strtolower($key), 'ilosc') &&
                            !str_contains(strtolower($key), 'cena')) {
                            $lowercaseKey = strtolower($key);
                            $transakcja->$lowercaseKey = $value;
                        }
                    }
                }

                if (!empty($row['PRODUKTID'])) {
                    $produkt = new \stdClass();
                    $produkt->produktid = $row['PRODUKTID'];
                    $produkt->nazwa_produktu = $row['NAZWA_PRODUKTU'];
                    $produkt->cena_jednostkowa = $row['CENA_JEDNOSTKOWA'];
                    $produkt->amunicjaid = $row['AMUNICJAID'];
                    $produkt->nazwa_amunicji = $row['NAZWA_AMUNICJI'];
                    $produkt->cena_amunicji = $row['CENA_AMUNICJI'];
                    $produkt->ilosc_amunicji = $row['ILOSC_AMUNICJI'];
                    $produkty[] = $produkt;
                }
            }

            oci_free_statement($cursor);

            if (request()->route()->getName() == 'transakcja.show') {
                return view('transakcja.show', compact('transakcja', 'produkty'));
            }

            $klientController = new KlientController();
            $klienci = $klientController->getKlienci();

            $pracownikController = new PracownikController();
            $pracownicy = $pracownikController->getPracownicy();

            $produktController = new ProduktController();
            $wszystkieProdukty = $produktController->getProdukty($id);

            $zamowioneProdukty = [];
            foreach ($produkty as $produkt) {
                $zamowionyProdukt = new \stdClass();
                $zamowionyProdukt->produktid = $produkt->produktid;
                $zamowionyProdukt->amunicjaid = $produkt->amunicjaid;
                $zamowionyProdukt->ilosc_amunicji = $produkt->ilosc_amunicji;
                $zamowioneProdukty[] = $zamowionyProdukt;
            }

            return view('transakcja.form', compact('transakcja', 'klienci', 'pracownicy', 'wszystkieProdukty', 'zamowioneProdukty'));

        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania szczegółów transakcji: ' . $e->getMessage());
            return redirect()->route('transakcja.index')->with('error', 'Wystąpił błąd podczas pobierania szczegółów transakcji.');
        }
    }

    public function createForm()
    {
        try {
            $klienci = $this->getKlienci();
            $pracownicy = $this->getPracownicy();
            $produkty = $this->getProdukty(null);

            return view('transakcja.form', compact('klienci', 'pracownicy', 'produkty'));
        } catch (\Exception $e) {
            \Log::error('Błąd podczas przygotowywania formularza transakcji: ' . $e->getMessage());
            return redirect()->route('transakcja.index')->with('error', 'Wystąpił błąd podczas ładowania formularza.');
        }
    }

    public function editForm($id)
    {
        try {
            $transakcja = $this->getTransakcjaById($id);

            if (!$transakcja) {
                return redirect()->route('transakcja.index')->with('error', 'Transakcja nie została znaleziona.');
            }

            $zamowioneProdukty = $this->getTransakcjaProdukty($id);
            $klienci = $this->getKlienci();
            $pracownicy = $this->getPracownicy();
            $produkty = $this->getProdukty($id);

            return view('transakcja.form', compact('transakcja', 'zamowioneProdukty', 'klienci', 'pracownicy', 'produkty'));
        } catch (\Exception $e) {
            \Log::error('Błąd podczas przygotowywania formularza edycji transakcji: ' . $e->getMessage());
            return redirect()->route('transakcja.index')->with('error', 'Wystąpił błąd podczas ładowania formularza edycji.');
        }
    }

    private function getKlienci()
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

            return $klienci;
        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania klientów: ' . $e->getMessage());
            return [];
        }
    }

    private function getPracownicy()
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

            return $pracownicy;
        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania pracowników: ' . $e->getMessage());
            return [];
        }
    }

    private function getProdukty($transakcjaId = null)
    {
        try {
            $pdo = DB::getPdo();

            $stmt = $pdo->prepare("BEGIN :cursor := PRODUKT_PKG.PRODUKTREADAVAILABLE(:transakcjaId); END;");
            $stmt->bindParam(':cursor', $cursor, \PDO::PARAM_STMT);
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

                if (!empty($produkt->amunicjaid)) {
                    $stmtAmunicja = $pdo->prepare("BEGIN :cursor := AMMO_PKG.AmmoReadById(:amunicjaId); END;");
                    $stmtAmunicja->bindParam(':cursor', $cursorAmunicja, \PDO::PARAM_STMT);
                    $stmtAmunicja->bindParam(':amunicjaId', $produkt->amunicjaid);
                    $stmtAmunicja->execute();

                    oci_execute($cursorAmunicja, OCI_DEFAULT);

                    if ($rowAmunicja = oci_fetch_assoc($cursorAmunicja)) {
                        $produkt->amunicja_cena = $rowAmunicja['CENA'];
                        $produkt->amunicja_nazwa = $rowAmunicja['NAZWA'];
                    }

                    oci_free_statement($cursorAmunicja);
                }

                $produkty[] = $produkt;
            }

            oci_free_statement($cursor);

            return $produkty;
        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania produktów: ' . $e->getMessage());
            return [];
        }
    }

    private function getTransakcjaById($id)
    {
        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN :cursor := TRANSAKCJA_PKG.TRANSAKCJAREADBYID(:transakcjaID); END;");
            $stmt->bindParam(':cursor', $cursor, \PDO::PARAM_STMT);
            $stmt->bindParam(':transakcjaID', $id);
            $stmt->execute();

            oci_execute($cursor, OCI_DEFAULT);

            $transakcja = null;
            if ($row = oci_fetch_assoc($cursor)) {
                $transakcja = new \stdClass();
                foreach ($row as $key => $value) {
                    $lowercaseKey = strtolower($key);
                    $transakcja->$lowercaseKey = $value;
                }
            }

            oci_free_statement($cursor);

            return $transakcja;
        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania transakcji: ' . $e->getMessage());
            return null;
        }
    }

    private function getTransakcjaProdukty($id)
    {
        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN :cursor := TRANSAKCJA_PKG.TRANSAKCJAPRODUKTREAD(:transakcjaID); END;");
            $stmt->bindParam(':cursor', $cursor, \PDO::PARAM_STMT);
            $stmt->bindParam(':transakcjaID', $id);
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

            return $produkty;
        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania produktów transakcji: ' . $e->getMessage());
            return [];
        }
    }
}
