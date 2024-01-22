<?php
session_start();
if(!isset($_POST['reg_pass2'])){
  header('location: http://infolut1.cba.pl/Baza/register.php');
}

  require_once("connect.php");
  $connect=@new mysqli($host,$db_user,$db_password,$db_name);
  if($connect->connect_error){
    $_SESSION['error']="<font color='red'>Nie udało połączyć się z bazą danych</font>";
    header('location: http://infolut1.cba.pl/Baza/register.php');
  }else{
    mysqli_set_charset($connect,"utf8");
    $login=strip_tags(mysqli_real_escape_string($connect,$_POST['reg_login']));
    $pass1=strip_tags(mysqli_real_escape_string($connect,$_POST['reg_pass1']));
    $pass2=strip_tags(mysqli_real_escape_string($connect,$_POST['reg_pass2']));
    $check=$_POST['reg_check'];
//TWORZENIE UŻYTKOWNIKA
    if($pass1 != $pass2){
      $_SESSION['reg_error']='Hasła różnią się od siebie.';
      header('location: http://infolut1.cba.pl/Baza/register.php');
    }else{
      $qr="SELECT login FROM `user` WHERE login = '$login'";
      if($qr_do=@mysqli_query($connect,$qr)){
        $num_users=mysqli_num_rows($qr_do);
        if($num_users>0){
          $_SESSION['reg_error']='Konto z taką nazwą użytkownika już istnieje.';
          header('location: http://infolut1.cba.pl/Baza/register.php');
        }else if(!isset($check)){
          $_SESSION['reg_error']='Aby się zarejestrować musisz zaakceptować regulamin.';
          header('location: http://infolut1.cba.pl/Baza/register.php');
        }else{
          //WSZYTKIE DANE POPRAWNE, TWORZYMY!
          $pass_hashed=password_hash($pass1,PASSWORD_DEFAULT);
          $qr="INSERT INTO `user`(`login`, `password`) VALUES ('$login','$pass_hashed')";
          if($qr_do=@mysqli_query($connect,$qr)){
            unset($_SESSION['reg_error']);
            unset($_POST['reg_pass2']);
            //TWORZYMY IM TABELE
                $_SESSION['error']='Utworzono konto pomyślnie';
                mysqli_close($connect);
                header('location: http://infolut1.cba.pl/Baza/');
              }else{
                echo "Error: ". mysqli_error($connect);
                exit();
              }
            }
          }else{
            $_SESSION['reg_error']="Coś poszło nie tak, spróbuj za jakiś czas.";
          }
        }
      }

    mysqli_close($connect);

?>
