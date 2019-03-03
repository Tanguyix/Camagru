<?php
    require 'config/database.php';

    function generateRandomString($lenght) {
        $chars =  '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charsLenght = strlen($chars);
        $rdmstr = '';
        for ($i = 0; $i < $lenght; $i++) {
            $rdmstr .= $chars[rand(0, $charsLenght - 1)];
        }
        return ($rdmstr);
    }

    //connect to DB
    try {
        $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $ex) { exit($ex); };

    $error = 0;
    if (isset($_POST['login']) && isset($_POST['pwd']) && isset($_POST['re_pwd']) && isset($_POST['email']) && isset($_POST['submit']))
    {
        if ($_POST['submit'] == "Inscription")
        {
            //check if login already exists
            try {
                $sql = "SELECT `name`, `id`
                FROM `users` WHERE `name` = :name";
                $check = $pdo->prepare($sql);
                $check->execute(array(':name' => $_POST['login']));
                $user = $check->fetch();
            } catch(PDOException $ex) { exit($ex); };
            if ($user !== false) {
                $error = 2;
                echo "login already exists";
            }

            //Check if emails has right format
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
                $error = 3;
                echo "email looks weird";
            }
            //Check if 2 pwd are the same
            if (strcmp($_POST['pwd'], $_POST['re_pwd'])) {
                $error = 1;
                echo "passwords don't match";
            }

            //Create pwd and salt it
            $salt = generateRandomString(19);
            $salted_pwd = $_POST['pwd'] . $salt;
            $salted_pwd = hash("sha512", $salted_pwd);
            //If everything is gine, put new user in db
            if (!$error) {
                $sql = "INSERT INTO `users` (name, email, pwd, salt)
                VALUES (?, ?, ?, ?);";
                $pdo->prepare($sql)->execute(array($_POST['login'], $_POST['email'], $salted_pwd, $salt));

                //create setting_db
                $sql = "INSERT INTO `settings` (notif_on, lang)
                VALUES (true, 'en');";
                $pdo->prepare($sql)->execute();

                //create pw_reset
                $code = md5(uniqid(rand(), true));
                $sql = "INSERT INTO `pwreset` (code)
                VALUES (?);";
                $pdo->prepare($sql)->execute(array($code));

                //create user in verified db and create unique code to verify him
                $code = md5(uniqid(rand(), true));
                $sql = "INSERT INTO `verified` (verified, code)
                VALUES (false, ?);";
                $pdo->prepare($sql)->execute(array($code));
                try {
                    $sql = "SELECT id
                FROM `users` WHERE `name` = :name";
                    $check = $pdo->prepare($sql);
                    $check->execute(array(':name' => $_POST['login']));
                    $user = $check->fetch();
                } catch(PDOException $ex) { exit($ex); };
                echo "Unique link to verify account is : http://localhost:8080/verify.php?uid={$user['id']}&code=$code";
            }
        }
    }
?>
<HTML>
<HEAD>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/create.css">
    <link rel="icon" type="image/png" href="img/Blason_Camargue.png">
    <TITLE>Camargue'U</TITLE>
</HEAD>


<BODY>
<div class="wrapper">
    <div class="head">
        <a href="index.php"><img class="logo" src="img/Camargue_U.png"></a>
        <a class="montage" href="montage.php">Create</a>
        <a class="login" href="login.php">Sign in</a>
        <a class="signup" href="create_account.php">Sign up</a>
    </div>
    <?php if ($_SESSION['logged_on_user'] != "") { ?>
            <form id="logout" action="utils/logout.php" method="get">
                <input class="send" type="submit" name="submit" value="Logout">
            </form>
        <?php } ?>
    <div class="container">
        <form action="create_account.php" method="post">
        <p>login</p>
        <input type="text" name="login" value="" required>
        <br/>
        <p>password</p>
        <input type="password" name="pwd" value="" required>
        <br/>
        <p>repeat password</p>
        <input type="password" name="re_pwd" value="" required>
        <br/>
        <p>email</p>
        <input type="text" name="email" value="" required>
        <input class="send" type="submit" name="submit" value="Inscription">
        </form>
    </div>
</div>
<footer class ="foot">
    <p class="name">© tboissel, 42, 2019</p>
</footer>
</BODY>
</HTML>