<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KategoriaController extends Controller
{
    public function create(Request $request)
    {
        $nazwakategorii = $request->nazwakategorii;
        $opis = $request->opis;
        $wymaganeuprawnienia = $request->wymaganeuprawnienia;
        $newKategoriaID = null;

        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN KATEGORIA_PKG.KategoriaCreate(:nazwakategorii, :opis, :wymaganeuprawnienia, :newKategoriaID); END;");
            $stmt->bindParam(':nazwakategorii', $nazwakategorii);
            $stmt->bindParam(':opis', $opis);
            $stmt->bindParam(':wymaganeuprawnienia', $wymaganeuprawnienia);
            $stmt->bindParam(':newKategoriaID', $newKategoriaID, \PDO::PARAM_INT | \PDO::PARAM_INPUT_OUTPUT, 32);
            $stmt->execute();

            return redirect()->route('kategoria.index')->with('success', 'Kategoria została utworzona.');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            \Log::error('Błąd podczas tworzenia kategorii: ' . $errorMessage);

            if (preg_match('/ORA-\d+: (.*?)(\s+ORA-|$)/', $errorMessage, $matches)) {
                $userFriendlyError = $matches[1];
            } else {
                $userFriendlyError = 'Wystąpił błąd podczas zapisywania kategorii.';
            }

            $kategoria = new \stdClass();
            $kategoria->kategoriaid = null;
            $kategoria->nazwakategorii = $nazwakategorii;
            $kategoria->opis = $opis;
            $kategoria->wymaganeuprawnienia = $wymaganeuprawnienia;

            return view('kategoria.form', compact('kategoria'))
                ->withErrors(['db_error' => $userFriendlyError]);
        }
    }

    public function update(Request $request, $id)
    {
        $nazwakategorii = $request->nazwakategorii;
        $opis = $request->opis;
        $wymaganeuprawnienia = $request->wymaganeuprawnienia;

        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN KATEGORIA_PKG.KategoriaUpdate(:kategoriaID, :nazwakategorii, :opis, :wymaganeuprawnienia); END;");
            $stmt->bindParam(':kategoriaID', $id);
            $stmt->bindParam(':nazwakategorii', $nazwakategorii);
            $stmt->bindParam(':opis', $opis);
            $stmt->bindParam(':wymaganeuprawnienia', $wymaganeuprawnienia);
            $stmt->execute();

            return redirect()->route('kategoria.index')->with('success', 'Kategoria została zaktualizowana.');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            \Log::error('Błąd podczas aktualizacji kategorii: ' . $errorMessage);

            if (preg_match('/ORA-\d+: (.*?)(\s+ORA-|$)/', $errorMessage, $matches)) {
                $userFriendlyError = $matches[1];
            } else {
                $userFriendlyError = 'Wystąpił błąd podczas aktualizacji kategorii.';
            }

            $kategoria = new \stdClass();
            $kategoria->kategoriaid = $id;
            $kategoria->nazwakategorii = $nazwakategorii;
            $kategoria->opis = $opis;
            $kategoria->wymaganeuprawnienia = $wymaganeuprawnienia;

            return view('kategoria.form', compact('kategoria'))
                ->withErrors(['db_error' => $userFriendlyError]);
        }
    }

    public function delete($id)
    {
        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN KATEGORIA_PKG.KategoriaDelete(:kategoriaID); END;");
            $stmt->bindParam(':kategoriaID', $id);
            $stmt->execute();

            return redirect()->route('kategoria.index')->with('success', 'Kategoria została usunięta.');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            \Log::error('Błąd podczas usuwania kategorii: ' . $errorMessage);

            if (preg_match('/ORA-\d+: (.*?)(\s+ORA-|$)/', $errorMessage, $matches)) {
                $userFriendlyError = $matches[1];
            } else {
                $userFriendlyError = 'Wystąpił błąd podczas usuwania kategorii.';
            }
            return redirect()->route('kategoria.index')->with('error', $userFriendlyError);
        }
    }

    public function read()
    {
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
                if ($lowercaseKey === 'opis' && is_object($value) && method_exists($value, 'read')) {
                    $kategoria->$lowercaseKey = $value->read($value->size());
                } else {
                    $kategoria->$lowercaseKey = $value;
                }
            }
            $kategorie[] = $kategoria;
        }

        oci_free_statement($cursor);

        return view('kategoria.index', compact('kategorie'));
    }

    public function readById($id)
    {
        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN :cursor := KATEGORIA_PKG.KategoriaReadById(:kategoriaID); END;");
            $stmt->bindParam(':cursor', $cursor, \PDO::PARAM_STMT);
            $stmt->bindParam(':kategoriaID', $id);
            $stmt->execute();

            oci_execute($cursor, OCI_DEFAULT);

            $kategoria = null;
            if ($row = oci_fetch_assoc($cursor)) {
                $kategoria = new \stdClass();
                foreach ($row as $key => $value) {
                    $lowercaseKey = strtolower($key);
                    if ($lowercaseKey === 'opis' && is_object($value) && method_exists($value, 'read')) {
                        $kategoria->$lowercaseKey = $value->read($value->size());
                    } else {
                        $kategoria->$lowercaseKey = $value;
                    }
                }
            }

            oci_free_statement($cursor);

            return $kategoria;
        } catch (\Exception $e) {
            \Log::error('Błąd podczas pobierania kategorii: ' . $e->getMessage());
            return null;
        }
    }

    public function readByUprawnienia(Request $request)
    {
        $wymaganeuprawnienia = $request->query('wymaganeuprawnienia', '');

        try {
            $pdo = DB::getPdo();
            $stmt = $pdo->prepare("BEGIN :cursor := KATEGORIA_PKG.KategoriaReadByUprawnienia(:wymaganeuprawnienia); END;");
            $stmt->bindParam(':cursor', $cursor, \PDO::PARAM_STMT);
            $stmt->bindParam(':wymaganeuprawnienia', $wymaganeuprawnienia);
            $stmt->execute();

            oci_execute($cursor, OCI_DEFAULT);

            $kategorie = [];
            while ($row = oci_fetch_assoc($cursor)) {
                $kategoria = new \stdClass();
                foreach ($row as $key => $value) {
                    $lowercaseKey = strtolower($key);
                    if ($lowercaseKey === 'opis' && is_object($value) && method_exists($value, 'read')) {
                        $kategoria->$lowercaseKey = $value->read($value->size());
                    } else {
                        $kategoria->$lowercaseKey = $value;
                    }
                }
                $kategorie[] = $kategoria;
            }

            oci_free_statement($cursor);

            return view('kategoria.index', compact('kategorie', 'wymaganeuprawnienia'));
        } catch (\Exception $e) {
            \Log::error('Błąd podczas filtrowania kategorii po uprawnieniach: ' . $e->getMessage());
            return redirect()->route('kategoria.index')->with('error', 'Wystąpił błąd podczas wyszukiwania kategorii.');
        }
    }

    public function createForm()
    {
        return view('kategoria.form');
    }

    public function editForm($id)
    {
        $kategoria = $this->readById($id);

        if (!$kategoria) {
            return redirect()->route('kategoria.index')->with('error', 'Kategoria nie została znaleziona.');
        }

        return view('kategoria.form', compact('kategoria'));
    }
}
