<?php
session_start();

if(!isset($_SESSION['logged'])){
    header('location: http://infolut1.cba.pl/Baza/');
    exit();
}

// Sprawdź, czy użytkownik jest administratorem
$is_admin = isset($_SESSION['super_user']) ? $_SESSION['super_user'] : false;

// Pobierz daty do filtrowania (jeśli przesłane)
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;

// Pobierz ID do filtrowania (tylko dla administratora)
$filter_user_id = $is_admin && isset($_GET['user_id']) ? $_GET['user_id'] : $_SESSION['user_id'];

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
        <!-- Formularz do wprowadzania dat -->
        <form action="faktury.php" method="get">
        <label for="start_date">Data początkowa:</label>
        <input type="date" id="start_date" name="start_date" value="<?= $start_date ?>">

        <label for="end_date">Data końcowa:</label>
        <input type="date" id="end_date" name="end_date" value="<?= $end_date ?>">
        
        <?php if ($is_admin): ?>
            <label for="user_id">ID użytkownika:</label>
            <input type="text" id="user_id" name="user_id" value="<?= $filter_user_id ?>">
        <?php endif; ?>

        <button type="submit">Filtruj</button>
    </form>
    <div class="faktury-container">
        <?php
            require_once "connect.php";
            $connect=@new mysqli($host,$db_user,$db_password,$db_name); 
            if(isset($_SESSION['logged'])) {
                $id_user = $_SESSION['user_id']; 

            
                $sql_faktury = "SELECT * FROM `Faktury`";

            // Dodaj do zapytania warunki dla daty i ID klienta (jeśli dostępne)
            if ($start_date && $end_date) {
                $sql_faktury .= " WHERE data_zakupu BETWEEN '$start_date' AND '$end_date'";
            }
            if ($is_admin && $filter_user_id !== "") {
                if ($start_date || $end_date) {
                    $sql_faktury .= " AND id_user = $filter_user_id";
                } else {
                    $sql_faktury .= " WHERE id_user = $filter_user_id";
                }
            }
                

                // Wykonaj zapytanie
                $result_faktury = mysqli_query($connect, $sql_faktury);

                // Sprawdź, czy zapytanie się powiodło
                if($result_faktury) {
                    // Wyświetl faktury
                    while ($row_faktury = mysqli_fetch_assoc($result_faktury)) {
                        $login = $row_faktury['login'];
                        $id_faktury = $row_faktury['id_faktury'];
                        $data_zakupu = $row_faktury['data_zakupu'];

                        // Only display details if the user is an admin or if the invoice belongs to the current user
                        if ($is_admin || ($id_user == $row_faktury['id_user'])) {
                            echo "Login: $login, Faktura nr $id_faktury, Data zakupu: $data_zakupu<br>";

                            // Inicjalizuj sumę cen dla danej faktury
                            $suma_cen = 0;

                            // Zapytanie SQL, które pobierze szczegóły faktury dla danego użytkownika
                            $sql_detale_faktury = "SELECT * FROM `DetaleFaktury` WHERE id_faktury = $id_faktury";

                            // Limituj dostęp dla usera
                            if (!$is_admin) {
                                $sql_detale_faktury .= " AND id_user = $id_user";
                            }

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
                    } 
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
