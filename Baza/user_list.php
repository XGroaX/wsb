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

                    $q_list = "SELECT * FROM `user` WHERE `email` LIKE '%$search_term%' OR `id` = '$search_term' ORDER BY id";

                      echo "<form action='' method='post'>
                            <label for='search_term'>Szukaj po adresie email bądź ID użytkownika:</label><input type='text' class='password' name='search_term' id='search_term'><button type='submit' class='change' name='search_user'>Szukaj</button>
                            </form>";
// Check if the search form is submitted
if (isset($_POST['search_user'])) {
  $search_term = $_POST['search_term'];

  // Perform a search query
  $q_search = "SELECT * FROM `user` WHERE `email` LIKE '%$search_term%' OR `id` = '$search_term'";
  if ($result = mysqli_query($connect, $q_search)) {
      $search_num_rows = mysqli_num_rows($result);

      if ($search_num_rows > 0) {
          echo "<table>";
          echo "<tr>
                  <th class='password'>ID</th>
                  <th class='password'>Login</th>
                  <th class='password'>Email</th>
                  <th class='password'>Ranga</th>
                  <th class='password'>Zmiana adresu</th>
                  <th class='password'>Zmiana rangi</th>
                  <th class='password'>Usuń</th>
              </tr>";

          while ($row = mysqli_fetch_assoc($result)) {
              echo "<tr>
                      <td class='password'>{$row['id']}</td>
                      <td class='password'>{$row['login']}</td>
                      <td class='password'>{$row['email']}</td>
                      <td class='password'>{$row['rank']}</td>
                      <td class='password'>
                          <form action='' method='post'>
                              <input type='hidden' name='id_to_change_email' value='{$row['id']}'>
                              <input type='text' name='email_change[{$row['id']}]' placeholder='Nowy email' class='input-field' required>
                              <button type='submit' name='change_email' class='change'>Zmień email</button>
                          </form>
                      </td>
                      <td class='password'>
                          <form action='' method='post'>
                              <input type='hidden' name='id_to_change' value='{$row['id']}'>
                              <select name='rank_change' class='select'>
                                  <option value='2'>Administrator</option>
                                  <option value='1'>Użytkownik</option><!-- Dodaj opcje dla innych rang -->
                              </select>
                              <button type='submit' name='change_rank' class='change'>Zmień rangę</button>
                          </form>
                      </td>
                      <td class='password'>
                          <form action='' method='post'>
                              <input type='hidden' name='id_to_delete' value='{$row['id']}'>
                              <button style='border:none;cursor:pointer;' type='submit' name='user_delete'>
                                  <img src='http://infolut1.cba.pl/Baza/pictures/remove.png'>
                              </button>
                          </form>
                      </td>
                  </tr>";
          }

          echo "</table>";
      } else {
          echo "<div class='message'>No results found.</div>";
      }

      mysqli_free_result($result);
  } else {
      echo "<div class='message'>Error in search query: " . mysqli_error($connect) . "</div>";
  }
} else {
  // Display all users when no search is performed
  $q_list = "SELECT * FROM `user` ORDER BY id";
  if ($list = mysqli_query($connect, $q_list)) {
      echo "<table>";
      echo "<tr>
              <th class='password'>ID</th>
              <th class='password'>Login</th>
              <th class='password'>Email</th>
              <th class='password'>Ranga</th>
              <th class='password'>Zmiana adresu</th>
              <th class='password'>Zmiana rangi</th>
              <th class='password'>Usuń</th>
          </tr>";

      while ($list_row = mysqli_fetch_assoc($list)) {
          echo "<tr>
                  <td class='password'>{$list_row['id']}</td>
                  <td class='password'>{$list_row['login']}</td>
                  <td class='password'>{$list_row['email']}</td>
                  <td class='password'>{$list_row['rank']}</td>
                  <td class='password'>
                      <form action='' method='post'>
                          <input type='hidden' name='id_to_change_email' value='{$list_row['id']}'>
                          <input type='text' name='email_change[{$list_row['id']}]' placeholder='Nowy email' class='input-field' required>
                          <button type='submit' name='change_email' class='change'>Zmień email</button>
                      </form>
                  </td>
                  <td class='password'>
                      <form action='' method='post'>
                          <input type='hidden' name='id_to_change' value='{$list_row['id']}'>
                          <select name='rank_change' class='select'>
                              <option value='2'>Administrator</option>
                              <option value='1'>Użytkownik</option><!-- Dodaj opcje dla innych rang -->
                          </select>
                          <button type='submit' name='change_rank' class='change'>Zmień rangę</button>
                      </form>
                  </td>
                  <td class='password'>
                      <form action='' method='post'>
                          <input type='hidden' name='id_to_delete' value='{$list_row['id']}'>
                          <button style='border:none;cursor:pointer;' type='submit' name='user_delete'>
                              <img src='http://infolut1.cba.pl/Baza/pictures/remove.png'>
                          </button>
                      </form>
                  </td>
              </tr>";
      }

      echo "</table>";
  }
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
                // Handling the email change - check if the form was submitted
                if (isset($_POST['change_email'])) {
                  foreach ($_POST['email_change'] as $user_id_email_change => $new_email) {
                      if (!empty($new_email)) { // Ensure the new email is not empty
                          $q_update_email = "UPDATE `user` SET `email` = '$new_email' WHERE `id` = '$user_id_email_change'";
                          if ($q_email_change = mysqli_query($connect, $q_update_email)) {
                              $_SESSION['manager_error'] = 'Zmieniono email użytkownika o ID ' . $user_id_email_change;
                              header('Refresh:0');
                              exit();
                          } else {
                              $_SESSION['manager_error'] = 'Nie udało się zmienić emaila użytkownika o ID ' . $user_id_email_change;
                              header('Refresh:0');
                              exit();
                          }
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
