<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Obsługa XML w PHP</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { border-collapse: collapse; margin-top: 20px; }
        td, th { border: 1px solid #ccc; padding: 10px; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h1>Historia zakupów klienta</h1>
    
    <form method="POST" action="">
        <label>Wybierz klienta:</label>
        <select name="wybrany_klient">
            <?php
                // Ładowanie pliku XML
                $xml = simplexml_load_file('sklep.xml');
                
                // Generowanie opcji selecta z listy klientów
                foreach($xml->klienci->klient as $klient) {
                    $id = (string)$klient['id'];
                    $imie = (string)$klient->imie;
                    $nazwisko = (string)$klient->nazwisko;
                    
                    // Zachowanie zaznaczenia po wysłaniu formularza
                    $selected = (isset($_POST['wybrany_klient']) && $_POST['wybrany_klient'] == $id) ? 'selected' : '';
                    
                    echo "<option value='$id' $selected>$imie $nazwisko</option>";
                }
            ?>
        </select>
        <input type="submit" value="Pokaż zakupy">
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $klientID = $_POST['wybrany_klient'];
        
        // Wyszukanie zakupów gdzie atrybut klient_ref równa się wybranemu ID
        // Użycie XPath analogicznie do slajdu 73 [cite: 1324]
        $zakupy = $xml->xpath("/sklep/zakupy/zakup[@klient_ref='$klientID']");
        
        if (count($zakupy) > 0) {
            echo "<table>";
            echo "<tr><th>Towar</th><th>Cena</th><th>Ilość</th><th>Data</th></tr>";
            
            foreach($zakupy as $zakup) {
                // Pobranie ID towaru z atrybutu referencyjnego
                $towarID = (string)$zakup['towar_ref'];
                
                // Znalezienie nazwy i ceny towaru po ID (analogicznie do relacji w bazie)
                // XPath szuka węzła towar o danym atrybucie id
                $towarDane = $xml->xpath("/sklep/towary/towar[@id='$towarID']");
                
                $nazwaTowaru = (string)$towarDane[0]->nazwa;
                $cenaTowaru = (string)$towarDane[0]->cena;
                $ilosc = (string)$zakup->ilosc;
                $data = (string)$zakup->data;
                
                echo "<tr>";
                echo "<td>$nazwaTowaru</td>";
                echo "<td>$cenaTowaru PLN</td>";
                echo "<td>$ilosc</td>";
                echo "<td>$data</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Brak zakupów dla wybranego klienta.</p>";
        }
    }
    ?>
</body>
</html>