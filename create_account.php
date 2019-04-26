<?php
    require 'config/setup.php';

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
        if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,16}$/", $_POST['pwd'])) {
              header("Location: create_account.php?error=weak_pw&login=" . $_POST['login'] . "&email=" . $_POST['email']);
              exit;
            }
        if ($_POST['submit'] == "Inscription")
        {
            if (strlen($_POST['login']) > 16 || !preg_match("([A-Za-z0-9\-\_]{4,16}$)", $_POST['login'])) {
                header("Location: create_account.php?error=error_login&login=" . $_POST['login'] . "&email=" . $_POST['email']);
                $error = true;
                exit;
            }
            //check if login already exists
            try {
                $sql = "SELECT `name`, `id`
                FROM `users` WHERE `name` = :name";
                $check = $pdo->prepare($sql);
                $check->execute(array(':name' => $_POST['login']));
                $user = $check->fetch();
            } catch(PDOException $ex) { exit($ex); };
            if ($user !== false) {
                header("Location: create_account.php?error=log_exists&email=" . $_POST['email']);
                $error = true;
                exit;
            }


            //Check if emails has right format
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
                header("Location: create_account.php?error=email_pb&login=" . $_POST['login']);
                $error = true;
                exit;
            }
            //Check if 2 pwd are the same
            if (strcmp($_POST['pwd'], $_POST['re_pwd'])) {
                header("Location: create_account.php?error=pw_mismatch&login=" . $_POST['login'] . "&email=" . $_POST['email']);
                $error = true;
                exit;
            }


            //Upload picture and put it in db
            $check = false;
            if ($_FILES["profile_pic"]["tmp_name"])
                $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
            if ($check !== false) {
                move_uploaded_file($_FILES["profile_pic"]["tmp_name"], "profile_pics/" . $_POST['login'] . ".png");
            }
            else {
                header("Location: create_account.php?error=img_broken&login=" . $_POST['login'] . "&email=" . $_POST['email']);
                $error = true;
                exit;
            }

            //Create pwd and salt it
            $salt = generateRandomString(19);
            $salted_pwd = $_POST['pwd'] . $salt;
            $salted_pwd = hash("sha512", $salted_pwd);
            //If everything is good, put new user in db
            if (!$error) {
                try {
                    $sql = "INSERT INTO `users` (name, email, pwd, salt, profile_pic)
                    VALUES (?, ?, ?, ?, ?);";
                    $pdo->prepare($sql)->execute(array($_POST['login'], $_POST['email'], $salted_pwd, $salt, "profile_pics/" . $_POST['login'] . ".png"));

                    //create setting_db
                    $sql = "INSERT INTO `settings` (notif_on)
                    VALUES (true);";
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
                    $sql = "SELECT id
                    FROM `users` WHERE `name` = :name";
                    $check = $pdo->prepare($sql);
                    $check->execute(array(':name' => $_POST['login']));
                    $user = $check->fetch();
                } catch(PDOException $ex) { exit($ex); };
                require "emails/confirmation.php";
                $headers = "Content-Type: text/html; charset=UTF-8\r\n";
                mail($_POST['email'], "Welcome to Camargue'u, Verify your account", $message, $headers);
                header("Location: create_account.php?success=ok");
            }
        }
    }
?>
<HTML>
<HEAD>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/create.css">
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
        <form action="create_account.php" method="post" enctype="multipart/form-data">
            <h2>Sign Up</h2>
            <input class="form-text" type="text" name="login" value="<?php if (isset($_GET['login'])) { echo $_GET['login']; }?>" placeholder="Enter login" required>
            <br/>
            <input class="form-text" type="password" name="pwd" value="" placeholder="Enter password" required>
            <br/>
            <input class="form-text" type="password" name="re_pwd" value="" placeholder="Repeat password" required>
            <br/>
            <input class="form-text" type="text" name="email" value="<?php if (isset($_GET['email'])) { echo $_GET['email'];}?>" placeholder="Enter email address" required>
            <br/>
            <input class="hidden" id="choose_file" type="file" name="profile_pic" accept="image/*">
            <br/>
            <label class="chose_file" for="choose_file">Choose a profile picture</label>
            <br/>
            <input class="send" id="sub" type="submit" name="submit" value="Inscription">
            <br/>
        </form>
        </div>
    </div>
<?php if (isset ($_GET['error']) && $_GET['error'] == "error_login") {?>
    <div class="error">
        <p class="error_text">Error : Login must contain 4 to 16 characters(letters, numbers, _ and - only)</p>
    </div>
<?php }?>
<?php if (isset ($_GET['error']) && $_GET['error'] == "log_exists") {?>
    <div class="error">
        <p class="error_text">Error : Login already taken</p>
    </div>
<?php }?>
<?php if (isset ($_GET['error']) && $_GET['error'] == "email_pb") {?>
    <div class="error">
        <p class="error_text">Error : You must enter a valid email address</p>
    </div>
<?php }?>
<?php if (isset ($_GET['error']) && $_GET['error'] == "pw_mismatch") {?>
    <div class="error">
        <p class="error_text">Error : Passwords do not match</p>
    </div>
<?php }?>
<?php if (isset ($_GET['error']) && $_GET['error'] == "weak_pw") {?>
    <div class="error">
        <p class="error_text">Error : Password must contain between 8 and 16 characters, with at least a lowercase, an uppercase and a number</p>
    </div>
<?php }?>
<?php if (isset ($_GET['error']) && $_GET['error'] == "img_broken") {?>
    <div class="error">
        <p class="error_text">Error : Profile picture is wrong</p>
    </div>
<?php }?>
<?php if (isset ($_GET['success']) && $_GET['success'] == "ok") {?>
    <div class="success">
        <p class="error_text">Your account has been created, please check your email to click on the validation link you have received</p>
    </div>
<?php }?>
</div>
</BODY>
</HTML>