<?php
    require 'config/database.php';
    session_start();
    //connect to DB
    try {
        $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $ex) { exit($ex); };

    //get id from logged user
    try {
        $sql = "SELECT *
                    FROM `users` WHERE `name` = :name";
        $check = $pdo->prepare($sql);
        $check->execute(array(':name' => $_SESSION['logged_on_user']));
        $line = $check->fetch();
    } catch(PDOException $ex) { exit($ex); };
    if ($line === false)
        echo "Error, user doesn't exist";

    //Change login
    if (isset($_POST['login']) && ($_POST['submit'] == "Change login")) {
        //Check if login already exists
        try {
            $sql = "SELECT `name`
                FROM `users` WHERE `name` = :name";
            $check = $pdo->prepare($sql);
            $check->execute(array(':name' => $_POST['login']));
            $user = $check->fetch();
        } catch(PDOException $ex) { exit($ex); };
        if ($user !== false) {
            $error = 2;
            echo "login already exists";
        }
        else {
            $sql = "UPDATE `users`
        SET `name` = ?
        WHERE `id` = ?";
            $check = $pdo->prepare($sql);
            $check->execute(array($_POST['login'], $line['id']));
            $_SESSION['logged_on_user'] = $_POST['login'];
        }
    }
    //Change email
    if (isset($_POST['email']) && ($_POST['submit'] == "Change email"))
    {
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
            echo "email looks weird";
        }
        else {
            $sql = "UPDATE `users`
            SET `email` = ?
            WHERE `id` = ?";
            $check = $pdo->prepare($sql);
            $check->execute(array($_POST['email'], $line['id']));
            $_SESSION['email'] = $_POST['email'];
        }
    }

    //Change pwd
    if (isset($_POST['pwd']) && isset($_POST['re_pwd']) && ($_POST['submit'] == "Change password"))
    {
        if ($_POST['pwd'] != $_POST['re_pwd']) {
            echo "passwords don't match";
        }
        else {
            $salted_pwd = $_POST['pwd'] . $line['salt'];
            $salted_pwd = hash("sha512", $salted_pwd);
            $sql = "UPDATE `users`
            SET `pwd` = ?
            WHERE `id` = ?";
            $check = $pdo->prepare($sql);
            $check->execute(array($salted_pwd, $line['id']));
        }
    }
?>
<HTML>
<HEAD>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/account.css">
    <link rel="icon" type="image/png" href="img/Blason_Camargue.png">
    <TITLE>Camargue'U</TITLE>
</HEAD>


<BODY>
<div class="wrapper">
    <div class="head">
        <a href="index.php"><img class="logo" src="img/Camargue_U.png"></a>
        <a class="montage" href="montage.php">Create</a>
        <?php if ($_SESSION['logged_on_user'] == "") { ?>
            <a class="login" href="login.php">Sign in</a>
            <a class="signup" href="create_account.php">Sign up</a>
        <?php }
         else { ?>
            <form id="logout" action="utils/logout.php" method="get">
                <input class="send" type="submit" name="submit" value="Logout">
            </form>
            <a class="login" href="account.php">My account</a>
        <?php } ?>
    </div>
    <div class="container">
        <form action="account.php" method="post">
            <p>login</p>
            <input type="text" name="login" value="<?php echo $_SESSION['logged_on_user']?>" required>
            <input class="send" type="submit" name="submit" value="Change login">
        <br/>
            <p>Change password</p>
            <input type="password" name="pwd" value="" required>
            <br/>
            <p>Repeat password</p>
            <input type="password" name="re_pwd" value="" required>
            <input class="send" type="submit" name="submit" value="Change password">
        <br/>
            <p>email</p>
            <input type="text" name="email" value="<?php echo $_SESSION['email'] ?>" required>
            <input class="send" type="submit" name="submit" value="Change email">
        </form>
    </div>
</div>
<footer class ="foot">
    <p class="name">© tboissel, 42, 2019</p>
</footer>
</BODY>
</HTML>