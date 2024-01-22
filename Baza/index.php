<?php
session_start();
if(isset($_SESSION['logged'])){
    header('location: http://infolut1.cba.pl/Baza/database.php');
    exit();
}
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <!-- <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300&display=swap" rel="stylesheet"> -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href='components/style_login.css' type='text/css'>
    <title>Logowanie do sklepu</title>
</head>
<body>
        <div class="login_container">
          <form action="zaloguj.php" method="post">
            <div class="login_box">
              <input type="text" class='password' name="login" placeholder="nazwa użytkownika">
              <input type="password" class='password' name='password' placeholder="Hasło">
              <input type='submit' class='submit' name='submit' value="Zaloguj">
              <a class='register_button' href='http://infolut1.cba.pl/Baza/register.php'>Rejestracja</a>
              <?php
              if(isset($_SESSION['error'])){
                echo "<div class='message'>".$_SESSION['error']."</div>";
              }
              ?>
            </form>
          </div>
        </div>



</body>
</html>
