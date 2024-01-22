<?php

session_start();

if(!isset($_SESSION['logged'])){

    header('location: http://infolut1.cba.pl/Baza/');

    exit();

}

if(isset($_SESSION['przypisana'])){

    $_SESSION['przypisana']--;

}



?>

<html lang="en" dir="ltr">

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.gstatic.com">

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">

    <link rel="stylesheet" href='components/style.css' type='text/css'>

    <title>Sklep papierniczy</title>

</head>

  <body>

    <nav class="menu">

      <?php

      // POŁĄCZNENIE Z BAZĄ

          require_once "connect.php";

          $connect=@new mysqli($host,$db_user,$db_password,$db_name);          // $connected=false;

          $is_searching=false;

          if ($connect->connect_error) {

              $_SESSION['error']="Nie udało połączyć się z bazą danych.";

              header('location: http://infolut1.cba.pl/Baza/logout.php');

           }else{

              mysqli_set_charset($connect,"utf8");

              $connected=true;

           }

      ?>

            <div class="message">PAPIERNICZY</div>

            <div class='status'>

                <div class='flex'>

                <span>zalogowano jako: <?php echo $_SESSION['myLogin'] ?></span>

                <span class='statusOut'>

                <?php

                $_SESSION['super_user']=0;

                // WYŚWIETLENIE UPRAWNIEŃ

                $q_permission = "SELECT rank FROM `user` WHERE login = '".$_SESSION['myLogin']."'";

                if($rank=mysqli_query($connect,$q_permission)){

                  $rank_row=mysqli_fetch_assoc($rank);

                  if($rank_row['rank'] == 'Administrator' || $rank_row['rank'] == 'Moderator'){

                      echo "Ranga: ".$rank_row['rank'];

                      $_SESSION['super_user']=1;

                  }

                }

                ?>

                </span>

            </div>

                <div class='flex'><a class='log_out_container' href="http://infolut1.cba.pl/Baza/logout.php"><img src="http://infolut1.cba.pl/Baza/pictures/log_out.png" alt='Wyloguj'></a></div>

            </div>



            <div class='nav_inputs_container'>

                <?php

                //OPCJE ADMINISTRATORA

                                if($_SESSION['super_user']==1){

                                    echo "<a class='super_option' href='http://infolut1.cba.pl/Baza/user_list.php'>Menadżer kont</a>";

                                }

                // POKAŻ PRZYCISK KOSZYKA

                                    echo "<div class='my_cart' name='my_cart'><img src='http://infolut1.cba.pl/Baza/pictures/my_cart.png'></div>";                              

                ?>                

            </div>

        </nav>

        <form method='post' action='' class="search">

        <span class='search_title'>WYSZUKIWARKA TOWARU</span>

        <div>

        <select name='search_tables' class='select_magazine'>



            <?php

            if(isset($_POST['send']) && $connected==true){

            $_SESSION['which_magazine']=$_POST['search_tables'];

            }

            // WYBÓR MAGAZYNU

                if($connected==true){

                    $list_magazines="SELECT * FROM `Magazyn` WHERE 1";

                    $show_magazines = mysqli_query($connect,$list_magazines);

                    $num_magazines= mysqli_num_rows($show_magazines);

                    for($i=0;$i<$num_magazines;$i++){

                        $mag_row=mysqli_fetch_assoc($show_magazines);

                        if($mag_row['id_magazynu'] == $_SESSION['which_magazine'] && isset($_SESSION['which_magazine'])){

                            echo "<option selected value='".$mag_row['id_magazynu']."'>".$mag_row['nazwa']."</option>";

                        }else{

                            echo "<option value='".$mag_row['id_magazynu']."'>".$mag_row['nazwa']."</option>";

                        }

                    }

                }

            ?>

        </select>

        <input type='text' name='word' class='word' placeholder="Wprowadź nazwe lub ID">

        <input type="submit" name="send" class='send_word' value='Znajdź'>

        </form>



        </div>

        <?php

        if(isset($_POST['dodaj_produkt']) && $connected==true){

            $nazwa_produktu = $_POST['nazwa'];

            $ilosc = $_POST['ilosc'];

            $cena = $_POST['cena'];



    // SQL Query do dodania produktu

    $dodaj_query = "INSERT INTO `Produkty` (nazwa, ilosc, cena, id_magazynu) VALUES ('$nazwa_produktu', $ilosc, $cena, ".$_SESSION['which_magazine'].")";



    if(mysqli_query($connect, $dodaj_query)){

        $_SESSION['przypisana']=2;

        $_SESSION['added'] = "Dodano nowy produkt do bazy danych.";

        // Można przekierować na stronę z informacją o dodaniu produktu lub odświeżyć aktualną stronę

        header('Refresh:0');

    } else {

        $_SESSION['added'] = "Błąd podczas dodawania produktu: " . mysqli_error($connect);

    }

}

?>







    <main class='result' style="overflow-x:auto;">

    <?php





    ?>

    <table>

        <?php



// WYSZUKIWARKA



                if(isset($_POST['send']) && $connected==true){

                    $is_searching=true;

                    $to_find=$_POST['word'];

                    $_SESSION['which_magazine']=$_POST['search_tables'];

                    $to_search="SELECT id_produktu,nazwa,ilosc,cena FROM `Produkty` WHERE nazwa LIKE '%$to_find%' AND id_magazynu = '".$_SESSION['which_magazine']."' OR id_produktu like '%$to_find%' AND id_magazynu = '".$_SESSION['which_magazine']."'";

                    $searching=mysqli_query($connect,$to_search);

                    $num_result=mysqli_num_rows($searching);

                    echo "<tr>";

                    while($fieldinfo = mysqli_fetch_field($searching)){

                        if($fieldinfo -> name == "id_produktu"){

                            echo "<th>id</th>";

                        }else{

                            echo "<th>".$fieldinfo -> name."</th>";

                        }

                    }

                    echo "<th>Wybierz</th>";

                    echo "<th>Zmień cene</th>";

                    echo "</tr>";




                    for($i=1;$i<=$num_result;$i++){

                        $search_row=mysqli_fetch_assoc($searching);

                        echo "<tr id='".$search_row['id_produktu']."'><td class='id_td'><form action='' method='post'><input type='hidden' name='id_produktu' value='".$search_row['id_produktu']."'>".$search_row['id_produktu']."</input></td>";

                        echo "<td>".$search_row['nazwa']."<input type='hidden' name='nazwa' value='".$search_row['nazwa']."'> </td>";

                        echo "<td  class='database_changer'>".$search_row['ilosc']." szt </td>";

                        echo "<td>".$search_row['cena']." zł <input type='hidden' name='price' value=".$search_row['cena']."></td>";

                        echo "<td class='fix'> <div class='buttons_container'><input type='number' min='1' max='".$search_row['ilosc']."' class='my_cart_numbers' placeholder='ilość' name='number_to_reduce' required> <button type='submit' name='add_to_cart' style='background-color:transparent;'><img src='http://infolut1.cba.pl/Baza/pictures/shopping-cart.png' class='to_buy_img'></button> </div></form></td></tr>";

                    }

            }

// ZMIANA CENY

 echo <label for='cena'>Nowa cena:</label>
 echo <input type='number' name='new_cena' step='0.01' class='input_text' required><br><br>


// Logika przetwarzania zmiany ceny w bazie danych
if (isset($_POST['zmien_cene']) && $connected == true) {
    $id_produktu = $_POST['id_produktu'];
    $nowa_cena = $_POST['new_cena'];

    $zmien_cene_query = "UPDATE `Produkty` SET cena = $nowa_cena WHERE id_produktu = $id_produktu";

    if (mysqli_query($connect, $zmien_cene_query)) {
        $_SESSION['przypisana'] = 2;
        $_SESSION['added'] = "Zmieniono cenę produktu.";

        // Można przekierować na stronę z informacją o zmianie ceny produktu lub odświeżyć aktualną stronę
        header('Refresh:0');
    } else {
        $_SESSION['added'] = "Błąd podczas zmiany ceny produktu: " . mysqli_error($connect);
    }
}


// <!-- Dodajemy przycisk do zmiany ceny w formularzu -->
    echo <input type='submit' name='zmien_cene' value='Zmień cenę' class='submit_button'>

// DOMYŚLNE WYSZUKANIE WSZYSTKIEGO



                            if($connected==true && $is_searching==false){

                    mysqli_set_charset($connect,"utf8");

                    if(isset($_SESSION['which_magazine'])){

                        $inquiry="SELECT id_produktu,nazwa,ilosc,cena FROM `Produkty` WHERE id_magazynu = ".$_SESSION['which_magazine'];

                    }else{

                        $inquiry="SELECT id_produktu,nazwa,ilosc,cena FROM `Produkty` WHERE id_magazynu = 1";

                    }

                    $query=mysqli_query($connect,$inquiry);

                    $rows=mysqli_num_rows($query);

                    echo "<tr>";

                    while($fieldinfo = mysqli_fetch_field($query)){

                        if($fieldinfo -> name == "id_produktu"){

                            echo "<th>id</th>";

                        }else{

                            echo "<th>".$fieldinfo -> name."</th>";

                        }

                    }

                    echo "<th>Wybierz</th>";

                    echo "<th>Zmień cene</th>";
                    
                    echo "</tr>";

                    for($i=1;$i<=$rows;$i++){

                        $row=mysqli_fetch_assoc($query);

                        echo "<tr id='".$row['id_produktu']."'><td class='id_td'><form action='' method='post'><input type='hidden' name='id_produktu' value='".$row['id_produktu']."'>".$row['id_produktu']."</input></td>";

                        echo "<td>".$row['nazwa']."<input type='hidden' name='nazwa' value='".$row['nazwa']."'> </td>";

                        echo "<td  class='database_changer'>".$row['ilosc']." szt </td>";

                        echo "<td>".$row['cena']." zł <input type='hidden' name='price' value=".$row['cena']."></td>";

                        echo "<td class='fix'> <div class='buttons_container'><input type='number' min='1' max='".$row['ilosc']."' class='my_cart_numbers' placeholder='ilość' name='number_to_reduce' required> <button type='submit' name='add_to_cart' style='background-color:transparent;'><img src='http://infolut1.cba.pl/Baza/pictures/shopping-cart.png' class='to_buy_img'></button> </div></form></td></tr>";

                    }

                }

?>

</table>



<?php

// DODAWANIE DO KOSZYKA

            if(isset($_POST['add_to_cart'])){

                $id_pro=$_POST['id_produktu'];

                $number_to_reduce=$_POST['number_to_reduce'];

                $nazwa=$_POST['nazwa'];

                $cena=$_POST['price'];

                $dodajemy=true;

                if(sizeof($_SESSION['my_cart'])==0){

                    $to_add=array('id' =>$id_pro, 'nazwa' => $nazwa, 'cena' => $cena,'liczba' => $number_to_reduce);

                    array_push($_SESSION['my_cart'],$to_add);

                    $_SESSION['added']= "Dodano do koszyka produkt o id=".$id_pro;

                    $_SESSION['przypisana']=2;

                    header('Refresh:0');

                }else{



                      foreach ($_SESSION['my_cart'] as $key => $value) {

                        foreach ($value as $item)

                          {

                            if ($item == $id_pro)

                            {

                                  $_SESSION['added']= "Wybrany produkt jest już w koszyku";

                                  $_SESSION['przypisana']=2;

                                  $dodajemy=false;

                                  header('Refresh:0');

                                  break;

                            }

                          }

                      }



                    if($dodajemy==true){

                        $to_add=array('id' =>$id_pro, 'nazwa' => $nazwa, 'cena' => $cena,'liczba' => $number_to_reduce);

                        array_push($_SESSION['my_cart'],$to_add);

                        $_SESSION['added']= "Dodano do koszyka produkt o id=".$id_pro;

                        $_SESSION['przypisana']=2;

                        header('Refresh:0');

                    }

                }

            }





            //KOSZYK

            

                    ?>

                        <div class='shop_page_container'>

                        <form action='' method='post' id="show_my_cart">

                        <div class='show_my_cart'>

                        <span class="message2">Podsumowanie zamówienia</span>

                        <div class='table_container'><table>



                            <?php

                            if($connected == true){



                                    $suma=0;

                                    echo "<tr><th class='fix_2'>nazwa</th> <th class='fix_2'>ilosc</th> <th class='fix_2'>cena</th></tr>";

                                for($i=0;$i<sizeof($_SESSION['my_cart']);$i++){

                                    echo "<tr><td> ".$_SESSION['my_cart'][$i]['nazwa']."</td> <td>x".$_SESSION['my_cart'][$i]['liczba']."</td>  <td>".$_SESSION['my_cart'][$i]['cena']." zł</td> <td>        <button type='submit' class='remove_button' name='remove_this' style='background-color:transparent;cursor:pointer;'><input type='hidden' name='id_to_remove' value='".$i."'><img src='http://infolut1.cba.pl/Baza/pictures/remove.png'></button> </td>   </tr>";

                                    $suma+=$_SESSION['my_cart'][$i]['liczba'] * $_SESSION['my_cart'][$i]['cena'];

                                }



                            }

                            ?>

                            </table></div><span class='price'>Łączna wartość koszyka: <b><?php echo $suma ?> zł</b></span>

                            <input type='submit' value='Przejdź do zamówienia' class='super_option' name='reduce'></div></form>

                            

                        </div>

                        </div>



                <?php

            //Blok dodawania produktów do bazy

            // Sprawdzenie, czy użytkownik jest zalogowany i ma odpowiednie uprawnienia
                        if(isset($_SESSION['logged']) && $_SESSION['super_user'] === 1) {                    

                            echo "<form id='addProductForm' method='post' action='' style='display: none;'>";

                                    echo "<div class='show_my_cart1'>";

                                        echo "<span class='message2'>Dodaj nowy produkt</span><br>";

                                            echo "<label for='nazwa'>Nazwa produktu:</label>";

                                            echo "<input type='text' name='nazwa'  class='input_text' required><br><br>";

                                            echo "<label for='ilosc'>Ilość:</label>";

                                            echo "<input type='number' name='ilosc' class='input_text' required><br><br>";

                                            echo "<label for='cena'>Cena:</label>";

                                            echo "<input type='number' name='cena' step='0.01' class='input_text' required><br><br>";

                                            echo "<input type='submit' name='dodaj_produkt' value='Dodaj produkt' class='submit_button'>";

                                    echo "</div>";
                            echo "</form>";
                        }    
                ?>            
                <?php

            // //USUŃ Z KOSZYKA



                                    if(isset($_POST["remove_this"]) && $connected == true){

                                        $value_of_i=$_POST['id_to_remove'];

                                        $_SESSION['show']=true;

                                        $_SESSION['przypisana']=2;

                                        $_SESSION['added']= "Usunięto produkt z koszyka.";

                                        unset($_SESSION['my_cart'][$value_of_i]);

                                        header('Refresh:0');

                                    }

            //USUWANIE Z BAZY ZA POMOCĄ KOSZYKA

                                        if(isset($_POST['reduce'])){

                                            // $ids=implode(', ', array_column($_SESSION['my_cart'], 'id'));

                                            for($i=0;$i<sizeof($_SESSION['my_cart']);$i++){

                                                $number_from_databaze= mysqli_query($connect,"SELECT id_produktu,ilosc FROM `Produkty` WHERE id_produktu = ".$_SESSION['my_cart'][$i]['id']);

                                                $row=mysqli_fetch_assoc($number_from_databaze);

                                                $value_to_remove= $row['ilosc'] - $_SESSION['my_cart'][$i]['liczba'];

                                                $sql="UPDATE `Produkty` SET `ilosc` = ".$value_to_remove." WHERE id_produktu= ".$_SESSION['my_cart'][$i]['id'];

                                                if(mysqli_query($connect,$sql)==true){

                                                    $_SESSION['added']='Zakup przebiegł pomyślnie.';

                                                    $_SESSION['przypisana']=2;

                                                    // unset($_SESSION['which_magazine']);

                                                    echo "Pzetwarzam...";

                                                    echo("<script>location.href = 'http://infolut1.cba.pl/Baza/database.php';</script>");

                                                }else{

                                                    echo "Nie powiodło się.";

                                                }

                                            }

                                            $_SESSION['my_cart']=array();

                                            // echo $prices;

                                        }







                            // ROZŁĄCZENIE Z BAZĄ

                                        if($connected==true){

                                            mysqli_close($connect);

                                            $connected=false;

                                        }

                                    ?>

                                    <button class='my_database' name='my_database'><img src='http://infolut1.cba.pl/Baza/pictures/add_to_database.png' ></button>

                                    <?php

                                    if($_SESSION['przypisana']==1)

                                    {

                                        echo "<div class='notification'>".$_SESSION['added']."</div>";

                                    }



                                    ?>

                                </main>



                                <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha256-4+XzXVhsDmqanXGHaHvgh1gMQKX40OUvDEBTu8JcmNs=" crossorigin="anonymous"></script>

                                <script type="text/javascript" src="http://infolut1.cba.pl/Baza/my_script.js"></script>

                                <script type='text/javascript'>



                                </script>



                            </body>

                            </html>

