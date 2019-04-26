<?php
    require 'config/setup.php';
    session_start();
    if (!isset($_GET['uid']) || $_GET['uid'] == 0 || !isset($_GET['code']) || $_GET['code'] == '') {}

    else {
        //connect to DB
        try {
            $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $ex) { exit($ex); };
        $sql = "SELECT *
                    FROM `verified` WHERE `user_id` = :id";
        $check = $pdo->prepare($sql);
        $check->execute(array(':id' => $_GET['uid']));
        $line = $check->fetch();
        if ($line === false) {
            header("Location: verify.php?error=broken_link");
            exit;
        }
        //check if code is right and then change verified status
        if ($line['code'] != $_GET['code']) {
            header("Location: verify.php?error=broken_link");
            exit;
        }
        else {
            try {
                $sql = "UPDATE `verified`
                SET `verified` = 1
                WHERE `user_id` = ?";
                $check = $pdo->prepare($sql);
                $check->execute(array($line['user_id']));

                //log the user that got verified
                $sql = "SELECT `name`, `email`, `id`
                FROM `users` WHERE `id` = :id";
                $check = $pdo->prepare($sql);
                $check->execute(array(':id' => $line['user_id']));
                $user = $check->fetch();
            } catch(PDOException $ex) { exit($ex); };
            $_SESSION['logged_on_user'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['id'] = $user['id'];
            header("Location: index.php?success=welcome");
        }
    }
?>
<HTML>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/global.css">
<link rel="icon" type="image/png" href="img/Blason_Camargue.png">

<HEAD>
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
            <?php if (isset ($_GET['error']) && $_GET['error'] == "broken_link") {?>
                <div class="error">
                    <p class="error_text">Error : The link to modify your account is broken, please contact a webmaster</p>
                </div>
            <?php }?>
        </div>
    </div>
</BODY>
</HTML>