<?php
    session_start();
    require "utils/auth.php";
    $db = 'camargue_u';

    $DB_DSN = "mysql:host=localhost;dbname=$db;port=3307";
    $DB_USER = "root";
    $DB_PASSWORD = "rootroot";

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
            $sql = "SELECT `email`, `id`
                FROM `users` WHERE `email` = :email";
            var_dump($_POST['email']);
            $check = $pdo->prepare($sql);
            $check->execute(array(':email' => $_POST['email']));
            $user = $check->fetch();
        } catch(PDOException $ex) { exit($ex); };
        //if user email exists, create unique code and associate it with the right id in resetpw table
        if ($user !== false) {
                $code = md5(uniqid(rand(), true));
                $sql = "UPDATE `pwreset`
            SET `code` = ?
            WHERE `id` = ?";
                $check = $pdo->prepare($sql);
                $check->execute(array($code, $user['id']));
        }
    }

    //actual login
    if (isset($_POST['login']) && isset($_POST['pwd'])) {
        if (auth($_POST['login'], $_POST['pwd'])) {
            //check if the account has been verified
            try {
                $sql = "SELECT `verified`
                FROM `verified` 
                JOIN `users`
                  ON `users`.id = `verified`.id
                WHERE `name` = :name";
                $check = $pdo->prepare($sql);
                $check->execute(array(':name' => $_POST['login']));
                $user = $check->fetch();
            } catch(PDOException $ex) { exit($ex); };
            if ($user['verified'] == 1) {
                try {
                    $sql = "SELECT `email`, `id`
                FROM `users` WHERE `name` = :name";
                    var_dump($_POST['email']);
                    $check = $pdo->prepare($sql);
                    $check->execute(array(':name' => $_POST['login']));
                    $user = $check->fetch();
                } catch(PDOException $ex) { exit($ex); };
                var_dump($user);
                $_SESSION['logged_on_user'] = $_POST['login'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['id'] = $user['id'];
            }
            else
                echo "Your account has yet to be verified, check your emails";
            //   header("Location: index.php");
        }
        else {
            echo "Connection failed";
        }
    }
?>
<HTML>
<HEAD>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="icon" type="image/png" href="img/Blason_Camargue.png">
    <TITLE>Camargue'U</TITLE>
</HEAD>

<BODY>
<div class="wrapper">
    <div class="head">
        <a href="index.php"><img class="logo" src="img/Camargue_U.png"></a>
        <a class="montage" href="montage.php">Create</a>
        <a class="login" href="login.php">Login</a>
    </div>
    <a id = "bout" href = "create_account.php">Create account</a>
    <form action="login.php" method="post">
        <p>login</p>
        <input type="text" name="login" value="" required>
        <br/>
        <p>password</p>
        <input type="password" name="pwd" value="" required>
        <br/>
        <input class="send" type="submit" name="submit" value="Login">
    </form>
    <form id="resetpw" href="login.php" method="post">
        <p>email</p>
        <input type="text" name="email" value="" required>
        <input class="send" type="submit" name="submit" value="Reset password">
    </form>
</div>
<footer class ="foot">
    <p class="name">© tboissel, 42, 2019</p>
</footer>
</BODY>
</HTML>