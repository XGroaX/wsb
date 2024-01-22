<?php
session_start();

if(!isset($_SESSION['logged'])){
    header('location: http://infolut1.cba.pl/Baza/');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Twoje faktury</title>
    <link rel="stylesheet" href="components/style_faktury.css">
</head>
<body>
    <h1>Twoje faktury</h1>
    <div class="faktury-container">
        <?php
            require_once "connect.php";
            $connect=@new mysqli($host,$db_user,$db_password,$db_name); 
            if(isset($_SESSION['logged'])) {
                $id_klienta = $_SESSION['user_id']; 

                // Pobierz id_user z sesji (załóżmy, że jest tam przechowywane)
                $id_user = $_SESSION['user_id']; // dostosuj to do sposobu przechowywania id użytkownika w sesji

                // Zapytanie SQL, które pobierze faktury dla danego użytkownika
                $sql_faktury = "SELECT * FROM `Faktury` WHERE id_user = $id_user";

                // Wykonaj zapytanie
                $result_faktury = mysqli_query($connect, $sql_faktury);

                // Sprawdź, czy zapytanie się powiodło
                if($result_faktury) {
                    // Wyświetl faktury
                    while ($row_faktury = mysqli_fetch_assoc($result_faktury)) {
                        $id_faktury = $row_faktury['id_faktury'];
                        $data_zakupu = $row_faktury['data_zakupu'];

                        echo "Faktura nr $id_faktury, Data zakupu: $data_zakupu<br>";

                        // Inicjalizuj sumę cen dla danej faktury
                        $suma_cen = 0;

                        // Zapytanie SQL, które pobierze szczegóły faktury dla danego użytkownika
                        $sql_detale_faktury = "SELECT * FROM `DetaleFaktury` WHERE id_faktury = $id_faktury AND id_user = $id_user";
                        
                        // Wykonaj zapytanie
                        $result_detale_faktury = mysqli_query($connect, $sql_detale_faktury);

                        // Sprawdź, czy zapytanie się powiodło
                        if ($result_detale_faktury) {
                            // Wyświetl szczegóły faktury
                            while ($row_detale_faktury = mysqli_fetch_assoc($result_detale_faktury)) {
                                $id_produktu = $row_detale_faktury['id_produktu'];
                                $ilosc_zakupionych = $row_detale_faktury['ilosc_zakupionych'];
                                $cena_jednostkowa = $row_detale_faktury['cena_jednostkowa'];

                                // Dodaj cenę produktu do sumy
                                $suma_cen += $ilosc_zakupionych * $cena_jednostkowa;

                                echo "Produkt ID: $id_produktu, Ilość: $ilosc_zakupionych, Cena jednostkowa: $cena_jednostkowa<br>";
                            }
                        } else {
                            echo "Błąd podczas pobierania szczegółów faktury.";
                        }

                        // Wyświetl sumę cen dla danej faktury
                        echo "Zapłacono: $suma_cen zł<br>";

                        echo "<hr>";
                    }
                } else {
                    echo "Błąd podczas pobierania faktur.";
                }
            }
            ?>
                <button onclick="goBack()">Wróć</button>
    </div>
    <script>
        function goBack() {
            window.location.href = 'database.php';
        }
    </script>
</body>
</html>
