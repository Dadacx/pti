<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Dodaj Zakup - Sklep XML</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 20px; background-color: #f9f9f9; }
        form { background: white; padding: 20px; border: 1px solid #ddd; border-radius: 8px; max-width: 500px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        label { display: block; margin-top: 10px; font-weight: bold; }
        select, input { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        input[type="submit"] { margin-top: 20px; background-color: #28a745; color: white; cursor: pointer; border: none; font-size: 16px; }
        input[type="submit"]:hover { background-color: #218838; }
        .message { padding: 10px; margin-bottom: 20px; border-radius: 4px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        a { text-decoration: none; color: #007bff; display: inline-block; margin-top: 15px; }
    </style>
</head>
<body>

<h1>Rejestracja nowej transakcji</h1>

<?php
$plikXML = 'sklep.xml';

// Sprawdzenie czy plik istnieje
if (!file_exists($plikXML)) {
    echo "<div class='message error'>Błąd: Plik $plikXML nie istnieje!</div>";
    exit;
}

$xml = simplexml_load_file($plikXML);

// --- OBSŁUGA FORMULARZA (DODAWANIE DANYCH) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Pobranie danych z formularza
    $klientRef = $_POST['klient'];
    $towarRef = $_POST['towar'];
    $ilosc = $_POST['ilosc'];
    $data = $_POST['data'];

    // 2. Generowanie nowego ID dla zakupu (np. Z + liczba elementów + 1)
    // UWAGA: W prostym rozwiązaniu liczymy elementy. W profesjonalnym użylibyśmy UUID.
    $liczbaZakupow = count($xml->zakupy->zakup);
    $noweID = "Z" . ($liczbaZakupow + 100); // +100 żeby uniknąć kolizji z istniejącymi Z1, Z2...

    // 3. Dodawanie elementu <zakup> do węzła <zakupy>
    [span_0](start_span)// Zgodnie z wykładem używamy addChild i addAttribute[span_0](end_span)
    $nowyZakup = $xml->zakupy->addChild('zakup');
    
    // Ustawienie atrybutów (ID oraz referencje IDREF)
    $nowyZakup->addAttribute('id_z', $noweID);
    $nowyZakup->addAttribute('klient_ref', $klientRef);
    $nowyZakup->addAttribute('towar_ref', $towarRef);
    
    // Ustawienie elementów potomnych (ilość, data)
    $nowyZakup->addChild('ilosc', $ilosc);
    $nowyZakup->addChild('data', $data);

    // 4. Zapis do pliku
    // Formatowanie wyjścia, aby XML był czytelny (DOMDocument)
    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml->asXML());
    
    if($dom->save($plikXML)) {
        echo "<div class='message success'>Pomyślnie dodano zakup (ID: $noweID)!</div>";
        // Przeładowanie XML, żeby formularz widział ew. zmiany (choć tu nieistotne)
        $xml = simplexml_load_file($plikXML); 
    } else {
        echo "<div class='message error'>Błąd zapisu pliku! Sprawdź uprawnienia do zapisu.</div>";
    }
}
?>

<form method="POST" action="">
    <label>Wybierz Klienta:</label>
    <select name="klient" required>
        <option value="">-- wybierz klienta --</option>
        <?php
        // Iteracja po klientach, aby wypełnić listę rozwijaną
        foreach ($xml->klienci->klient as $klient) {
            $id = (string)$klient['id'];
            $imie = $klient->imie;
            $nazwisko = $klient->nazwisko;
            echo "<option value='$id'>$imie $nazwisko</option>";
        }
        ?>
    </select>

    <label>Wybierz Towar:</label>
    <select name="towar" required>
        <option value="">-- wybierz towar --</option>
        <?php
        // Iteracja po towarach
        foreach ($xml->towary->towar as $towar) {
            $id = (string)$towar['id'];
            $nazwa = $towar->nazwa;
            $cena = $towar->cena;
            echo "<option value='$id'>$nazwa ($cena PLN)</option>";
        }
        ?>
    </select>

    <label>Ilość:</label>
    <input type="number" name="ilosc" min="1" value="1" required>

    <label>Data zakupu:</label>
    <input type="date" name="data" value="<?php echo date('Y-m-d'); ?>" required>

    <input type="submit" value="Dodaj rekord">
</form>

<a href="index.php">← Powrót do przeglądania zakupów</a>

</body>
</html>
