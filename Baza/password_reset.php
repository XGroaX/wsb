<?php
session_start();

// Include your database connection file
require_once "connect.php";


if (isset($_POST['submit'])) {
    // Validate email
    $email = trim(mysqli_real_escape_string($connect, $_POST['email']));

    if (empty($email)) {
        $_SESSION['error'] = "Email address is required.";
    } else {
        // Check if the email exists in the database
        $check_email_query = "SELECT * FROM `user` WHERE `email` = '$email'";
        
        // Debugging: Output the SQL query
        echo "SQL Query: $check_email_query<br>";

        $check_email_result = mysqli_query($connect, $check_email_query);

        if (!$check_email_result) {
            $_SESSION['error'] = "Error checking email in the database: " . mysqli_error($connect);
        } else {
            if (mysqli_num_rows($check_email_result) > 0) {
                // Email found in the database
                // Generate a unique token for password reset
                $token = bin2hex(random_bytes(32));
            
                // Store the token in the database along with the user's email
                $store_token_query = "UPDATE `user` SET `reset_token` = '$token' WHERE `email` = '$email'";
                $store_token_result = mysqli_query($connect, $store_token_query);
            
                if ($store_token_result) {
                    // Debugging: Output the token and email for verification
                    echo "Token: $token<br>";
                    echo "Email: $email<br>";
            
                    // Send a password reset email with a link containing the token
                    $reset_link = "http://infolut1.cba.pl/Baza/password_reset_form.php?token=$token";
                    $to = $email;
                    $subject = "Password Reset";
                    $message = "Click on the link below to reset your password:\n$reset_link";
                    $headers = "From: filip02521@infolut1.cba.pl";
            
                    // Uncomment the line below to send the email (make sure your server supports mail())
                    if (mail($to, $subject, $message, $headers)) {
                        $_SESSION['success'] = "An email with instructions to reset your password has been sent.";
                    } else {
                        $_SESSION['error'] = "Error sending email: " . error_get_last()['message'];
                    }
                } else {
                    $_SESSION['error'] = "Error storing reset token in the database: " . mysqli_error($connect);
                }
            } else {
                $_SESSION['error'] = "Email address not found in our records.";
            }
        }
    }
}

mysqli_close($connect);

?>


<!-- Your HTML code for the forgot password form -->
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.gstatic.com">
<!-- <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300&display=swap" rel="stylesheet"> -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
<link rel="stylesheet" href='components/style_login.css' type='text/css'>
<title>Niepamiętam hasła</title>
</head>
<body>
<div class="login_container">
<form action="password_reset.php" method="post">
    <div class="login_box">
        <input type="text" class='password' name="email" placeholder="Wprowadź adres email">
        <input type='submit' class='submit' name='submit' value="Zresetuj hasło">
        <a class='register_button' href='http://infolut1.cba.pl/Baza/index.php'>Wróć do logowania</a>
        <?php
        if (isset($_SESSION['error'])) {
            echo "<div class='message'>".$_SESSION['error']."</div>";
            unset($_SESSION['error']);
        } elseif (isset($_SESSION['success'])) {
            echo "<div class='message success'>".$_SESSION['success']."</div>";
            unset($_SESSION['success']);
        }
        ?>
    </form>
</div>
</div>
</body>
</html>