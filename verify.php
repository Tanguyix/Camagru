<?php
    require 'config/database.php';
    session_start();
    if (!isset($_GET['uid']) || $_GET['uid'] == 0 || !isset($_GET['code']) || $_GET['code'] == '')
        echo "link is broken";
    else {
        //connect to DB
        try {
            $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $ex) { exit($ex); };
        $sql = "SELECT *
                    FROM `verified` WHERE `id` = :id";
        $check = $pdo->prepare($sql);
        $check->execute(array(':id' => $_GET['uid']));
        $line = $check->fetch();
        if ($line === false) {
            echo "Error, user doesn't exist";
        }
        //check if code is right and then change verified status
        if ($line['code'] != $_GET['code']) {
            echo "Verfication failed";
        }
        else {
            $sql = "UPDATE `verified`
            SET `verified` = 1
            WHERE `id` = ?";
            $check = $pdo->prepare($sql);
            $check->execute(array($line['id']));

            //log the user that got verified and message to tell him
            try {
                $sql = "SELECT `name`
                FROM `users` WHERE `id` = :id";
                $check = $pdo->prepare($sql);
                $check->execute(array(':id' => $line['id']));
                $user = $check->fetch();
            } catch(PDOException $ex) { exit($ex); };
            $_SESSION['logged_on_user'] = $user['name'];
            echo "Welcome " . $_SESSION['logged_on_user'] . ", your account has been successfully verified";
        }
    }
?>
<HTML>
<meta charset="UTF-8">
<link rel="stylesheet" href="css/global.css">
<link rel="icon" type="image/png" href="img/Blason_Camargue.png">

<HEAD>
    <TITLE>Camargue'U</TITLE>
</HEAD>


<BODY>
    <div class="wrapper">
        <div class="head">
            <a href="index.php"><img class="logo" src="img/Camargue_U.png"></a>
        <a class="montage" href="montage.php">Create</a>
            <a class="login" href="login.php">Login</a>
        </div>
    </div>
    <footer class ="foot">
        <p class="name">© tboissel, 42, 2019</p>
    </footer>
</BODY>
</HTML>