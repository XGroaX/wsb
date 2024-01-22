<?php
session_start();
if(!isset($_SESSION['logged']) || $_SESSION['super_user']==0){
    header('location: http://infolut1.cba.pl/Baza/');
    exit();
}
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <!-- <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300&display=swap" rel="stylesheet"> -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href='http://infolut1.cba.pl/Baza/components/style_login.css' type='text/css'>
    <title>Menadżer użytkowników</title>
</head>
<body>
        <div class="login_container">
            <div class="login_box">
              <?php
              require_once "connect.php";
              $connect=@new mysqli($host,$db_user,$db_password,$db_name);          // $connected=false;
                if ($connect->connect_error) {
                    $_SESSION['error']="Nie udało połączyć się z bazą danych.";
                    header('location: http://infolut1.cba.pl/Baza/logout.php');
                 }else{
                    mysqli_set_charset($connect,"utf8");
                    $connected=true;

                    $q_list="SELECT * FROM `user` ORDER BY id";
                    if($list=mysqli_query($connect,$q_list)){
                      $list_num_rows=mysqli_num_rows($list);
                      echo "<table>";
                      echo "<form action='' method='post'>";
                      for ($i=1; $i <= $list_num_rows; $i++) {
                        $list_row=mysqli_fetch_assoc($list);
                        echo "<tr><td class='password'>".$list_row['id']."</td>
                          <td class='password'>". $list_row['login']."</td> 
                          <td class='password'>".$list_row['rank']."</td> 
                          <td class='password'><form action='' method='post'><input type='hidden' name='id_to_change' value='" . $list_row['id'] . "'><select name='rank_change' class='select'><option value='2'>Administrator</option><option value='1'>Użytkownik</option><!-- Dodaj opcje dla innych rang --></select><button type='submit' name='change_rank' class='change'>Zmień rangę</button></form></td>
                          <td class='password'><form action='' method='post'><input type='hidden' name='id_to_delete' value='".$list_row['id']."'><button style='border:none;cursor:pointer;' type='submit' name='user_delete'><img src='http://infolut1.cba.pl/Baza/pictures/remove.png'></button></form></td>
                          </tr>";
                      }
                      echo "</form>";
                      echo "</table>";
                    }
                 }
//USUWANIE UŻYTKONIKÓW I NADAWANIE RANG
              if(isset($_POST['user_delete'])){
                $q="DELETE FROM `user` WHERE `user`.`id` = ".$_POST['id_to_delete'];
                if($q_user=mysqli_query($connect,$q)){
                  $_SESSION['manager_error']= 'Usunięto konto o id '.$_POST['id_to_delete'];
                  header('Refresh:0');
                }
              }
              if (isset($_POST['change_rank'])) {
                $new_rank = $_POST['rank_change'];
                $user_id = $_POST['id_to_change'];
                
                require_once "connect.php";
                $connect = @new mysqli($host, $db_user, $db_password, $db_name);
            
                if ($connect->connect_error) {
                    $_SESSION['error'] = "Nie udało połączyć się z bazą danych.";
                    header('location: http://infolut1.cba.pl/Baza/logout.php');
                    exit();
                } else {
                    mysqli_set_charset($connect, "utf8");
            
                    $q_update_rank = "UPDATE `user` SET `rank` = '$new_rank' WHERE `id` = '$user_id'";
                    if ($q_user = mysqli_query($connect, $q_update_rank)) {
                        $_SESSION['manager_error'] = 'Zmieniono rangę użytkownika o ID ' . $user_id;
                        header('Refresh:0');
                        exit();
                    } else {
                        $_SESSION['manager_error'] = 'Nie udało się zmienić rangi użytkownika o ID ' . $user_id;
                        header('Refresh:0');
                        exit();
                    }
                }
            }
              ?>




                <a class='register_button' href='http://infolut1.cba.pl/Baza/'>Wróć</a>
              <?php
              if(isset($_SESSION['manager_error'])){
                echo "<div class='message'>".$_SESSION['manager_error']."</div>";
              }
              ?>
          </div>
        </div>


        <?php
          if($connected==true){
            mysqli_close($connect);
          }
        ?>
</body>
</html>
