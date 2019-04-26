<?php
    session_start();
    require 'config/setup.php';
    require "utils/auth.php";

    //connect to the db
    try {
        $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $ex) { exit($ex); };

    if (isset($_SESSION['logged_on_user']) && $_SESSION['logged_on_user'] != "") {
        header("Location: account.php");
    }

    //reset password, created a unique link to change pw
    if (isset($_POST['submit']) && $_POST['submit'] == "Reset password") {
        //check if email exists in database
        try {
            $sql = "SELECT `email`, `id`, `name`
                FROM `users` WHERE `email` = :email";
            $check = $pdo->prepare($sql);
            $check->execute(array(':email' => $_POST['email']));
            $user = $check->fetch();
        //if user email exists, create unique code and associate it with the right id in resetpw table
        if ($user !== false) {
                $code = md5(uniqid(rand(), true));
                $sql = "UPDATE `pwreset`
            SET `code` = ?
            WHERE `user_id` = ?";
                $check = $pdo->prepare($sql);
                $check->execute(array($code, $user['id']));

                require "emails/pwd.php";
                $headers = "Content-Type: text/html; charset=UTF-8\r\n";
                mail($user['email'], "Modify your password on Camargue'u", $message, $headers);
             }
        } catch(PDOException $ex) { exit($ex); };
        header("Location: login.php?success=ok");
        exit;
    }

    //actual login
    if (isset($_POST['login']) && isset($_POST['pwd'])) {
        if (auth($_POST['login'], $_POST['pwd'])) {
            //check if the account has been verified
            try {
                $sql = "SELECT `verified`
                FROM `verified` 
                JOIN `users`
                  ON `users`.id = `verified`.user_id
                WHERE `name` = :name";
                $check = $pdo->prepare($sql);
                $check->execute(array(':name' => $_POST['login']));
                $user = $check->fetch();
            } catch(PDOException $ex) { exit($ex); };
            if ($user['verified'] == 1) {
                try {
                    $sql = "SELECT `email`, `id`
                FROM `users` WHERE `name` = :name";
                    $check = $pdo->prepare($sql);
                    $check->execute(array(':name' => $_POST['login']));
                    $user = $check->fetch();
                } catch(PDOException $ex) { exit($ex); };
                $_SESSION['logged_on_user'] = $_POST['login'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['id'] = $user['id'];
                if (isset($_GET['redirect']) && $_GET['redirect'] == "montage") {
                    header("Location: montage.php");
                }
                else {
                    header("Location: index.php");
                }
            }
            else
                header("Location: login.php?error=account_not_verif");
        }
        else {
            header("Location: login.php?error=connection_failed");
        }
    }
?>
<HTML>
<HEAD>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="icon" type="image/png" href="img/Blason_Camargue.png">
    <TITLE>Camargue'U</TITLE>
</HEAD>

<BODY>
<div class="wrapper">
    <div class="head">
        <a href="index.php"><img class="logo" src="img/Camargue_U.png"></a>
        <a class="montage" href="montage.php">Post</a>
        <a class="login" href="login.php">Sign in</a>
        <a class="signup" href="create_account.php">Sign up</a>
        <a class="leaderboard" href="leaderboard.php?order=alpha">Leaderboard</a>
    </div>
    <div class="container">
        <form action="login.php<?php echo ((isset($_GET['redirect']) && $_GET['redirect'] == "montage") ? "?redirect=montage" : "");?>" method="post">
            <h2>Log in</h2>
                <input class="form-text" type="text" name="login" value="" required placeholder="Enter login">
                <br>
                <input class="form-text" type="password" name="pwd" value="" required placeholder="Enter password">
                <br>
                <input class="send" type="submit" name="submit" value="Sign in">
        </form>
        <form id="resetpw" href="login.php" method="post">
            <h2>Forgot your password</h2>
            <input class="form-text" type="text" name="email" value="" required placeholder="Enter your email">
            <br/>
            <input class="send" type="submit" name="submit" value="Reset password">
        </form>
    </div>
    <?php if (isset ($_GET['error']) && $_GET['error'] == "account_not_verif") {?>
        <div class="error">
            <p class="error_text">Error : Your account has yet to be verified, please check your emails</p>
        </div>
    <?php }
    else if (isset ($_GET['error']) && $_GET['error'] == "connection_failed") {?>
        <div class="error">
            <p class="error_text">Error : Connection failed. Please check again your login and password</p>
        </div>
    <?php }?>
    <?php if (isset ($_GET['success']) && $_GET['success'] == "ok") {?>
        <div class="success">
            <p class="error_text">Your demand has been received, please check your email to reset your password</p>
        </div>
    <?php }?>
</div>
</BODY>
</HTML>