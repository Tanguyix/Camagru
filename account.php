<?php
    require 'config/setup.php';
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
        $user = $check->fetch();
    } catch(PDOException $ex) { exit($ex); };
    if ($user === false) {
        header("Location: index.php");
        exit;
    }

    //Check is user entered the right password
    if (isset($_POST['submit']) && $_POST['submit'] == "Update" && isset($_POST['oldpwd'])) {
        $salted_pw = $_POST['oldpwd'] . $user['salt'];
        $salted_pw = (hash("sha512", $salted_pw));
        if ($salted_pw == $user['pwd']) {

            //Change login
            if (isset($_POST['login']) && $_POST['login'] != "") {
                if (!preg_match("([A-Za-z0-9\-\_]{4,}$)", $_POST['login']) || strlen($_POST['login']) > 16 ) {
                    header("Location: account.php?error=error_login");
                    exit;
                }
                try {
                    $sql = "SELECT `name`, `id`
                FROM `users` WHERE `name` = :name";
                    $check = $pdo->prepare($sql);
                    $check->execute(array(':name' => $_POST['login']));
                    $line = $check->fetch();
                } catch(PDOException $ex) { exit($ex); };
                if ($line !== false) { //if login exists, check if it is by another user or just that our login wasn't changed
                    if ($line['id'] != $user['id']) {
                        header("Location: account.php?error=login_taken");
                        exit;
                    }
                }
                else {
                    try {
                        $sql = "UPDATE `users`
                        SET `name` = ?
                        WHERE `id` = ?";
                        $check = $pdo->prepare($sql);
                        $check->execute(array($_POST['login'], $user['id']));
                        $_SESSION['logged_on_user'] = $_POST['login'];
                    } catch(PDOException $ex) { exit($ex); };
                }
            }


            //Change email
            if (isset($_POST['email']) && $_POST['email'] != "")
            {
                if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
                    header("Location: account.php?error=wrong_email");
                    exit;
                }
                else {
                    try {
                    $sql = "UPDATE `users`
                            SET `email` = ?
                            WHERE `id` = ?";
                    $check = $pdo->prepare($sql);
                    $check->execute(array($_POST['email'], $user['id']));
                    $_SESSION['email'] = $_POST['email'];
                    } catch(PDOException $ex) { exit($ex); };
                }
            }

            //Change profile_pic
            if (isset($_FILES) && !empty($_FILES) && !empty($_FILES['profile_pic']['tmp_name'])) {
                $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
                if ($check !== false) {
                    move_uploaded_file($_FILES["profile_pic"]["tmp_name"], "profile_pics/" . $_POST['login'] . ".png");
                }
                else {
                    header("Location: account.php?error=img_broken");
                    exit;
                }
                try {
                    $sql = "UPDATE `users`
                        SET `profile_pic` = ?
                        WHERE `id` = ?";
                    $check = $pdo->prepare($sql);
                    $check->execute(array("profile_pics/" . $_SESSION['logged_on_user'] . ".png" , $user['id']));
                } catch(PDOException $ex) { exit($ex); };
            }

            //Change pwd
            if (isset($_POST['pwd']) && isset($_POST['re_pwd']) && $_POST['pwd'] != "")
            {
                if ($_POST['pwd'] != $_POST['re_pwd']) {
                    header("Location: account.php?error=pw_not_match");
                }
                else {
                    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,16}$/", $_POST['pwd'])) {
                        header("Location: account.php?error=weak_pw");
                        exit; }
                    $salted_pwd = $_POST['pwd'] . $user['salt'];
                    $salted_pwd = hash("sha512", $salted_pwd);
                    try {
                        $sql = "UPDATE `users`
                        SET `pwd` = ?
                        WHERE `id` = ?";
                        $check = $pdo->prepare($sql);
                        $check->execute(array($salted_pwd, $user['id']));
                    } catch(PDOException $ex) { exit($ex); };
                }
            }

            //Change notif
            try {
                $sql = "UPDATE settings
                SET notif_on = ?
                WHERE user_id = ?;";
                $check = $pdo->prepare($sql);
                $check->execute(array((isset($_POST['notification'])), $_SESSION['id']));
            } catch(PDOException $ex) { exit($ex); };
        }
        else {
            header("Location: account.php?error=wrong_pw");
        }
    }

    //get if user has notif_on
    try {
        $sql = "SELECT notif_on
                            FROM `settings` WHERE `user_id` = :id";
        $check = $pdo->prepare($sql);
        $check->execute(array(':id' => $_SESSION['id']));
        $notif = $check->fetch();
    } catch(PDOException $ex) { exit($ex); };
?>
<HTML>
<HEAD>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/account.css">
    <link rel="icon" type="image/png" href="img/Blason_Camargue.png">
    <TITLE>Camargue'U</TITLE>
</HEAD>


<BODY>
<div class="wrapper">
    <div class="head">
        <a href="index.php"><img class="logo" src="img/Camargue_U.png"></a>
        <a class="montage" href="montage.php">Post</a>
        <a class="leaderboard" href="leaderboard.php?order=alpha">Leaderboard</a>
        <?php if ($_SESSION['logged_on_user'] == "") { ?>
            <a class="login" href="login.php">Sign in</a>
            <a class="signup" href="create_account.php">Sign up</a>
        <?php }
         else { ?>
             <a class="Logout" href="utils/logout.php">Logout</a>
             <a class="login" href="account.php">My account</a>
        <?php } ?>
    </div>
    <div class="container">
        <form action="account.php" method="post" enctype="multipart/form-data">
            <h2>Modify personal information</h2>
            <p>Get notified when your pictures are commented</p>
            <input type="checkbox" id="notify_check" name="notification", value="Notif" <?php if ($notif['notif_on']) { echo "checked";} ?>>
            <p>Login</p>
            <input class="form-text" type="text" name="login" value="<?php echo $_SESSION['logged_on_user']?>" required>
        <br/>
            <p>Old password</p>
            <input class="form-text" type="password" name="oldpwd" value="" required>
            <br/>
            <p>New password</p>
            <input class="form-text" type="password" name="pwd" value="">
            <br/>
            <p>Repeat new password</p>
            <input class="form-text" type="password" name="re_pwd" value="">
            <br>
            <input class="hidden" id="choose_file" type="file" name="profile_pic" accept="image/*">
            <br>
            <label for="choose_file" id="change_pic" class="chose_file">Change profile pic</label>
            <p>Email</p>
            <input class="form-text" type="text" name="email" value="<?php echo $_SESSION['email'] ?>" required>
            <input class="send" type="submit" name="submit" value="Update">
        </form>
          <?php if (isset ($_GET['error']) && $_GET['error'] == "wrong_pw") {?>
            <div class="error">
                <p class="error_text">Error : Your password does not match</p>
            </div>
        <?php } ?>
        <?php if (isset ($_GET['error']) && $_GET['error'] == "wrong_email") {?>
            <div class="error">
                <p class="error_text">Error : Your new email is not well formatted</p>
            </div>
        <?php } ?>
        <?php if (isset ($_GET['error']) && $_GET['error'] == "login_taken") {?>
            <div class="error">
                <p class="error_text">Error : This login is already taken</p>
            </div>
        <?php } ?>
        <?php if (isset ($_GET['error']) && $_GET['error'] == "error_login") {?>
            <div class="error">
                <p class="error_text">Error : Login must contain 4 to 16 characters(letters, numbers, _ and - only)</p>
            </div>
        <?php }?>
        <?php if (isset ($_GET['error']) && $_GET['error'] == "weak_pw") {?>
            <div class="error">
                <p class="error_text">Error : Password must contain between 8 and 16 characters, with at least a lowercase, an uppercase and a number</p>
            </div>
        <?php }?>
        <?php if (isset ($_GET['error']) && $_GET['error'] == "pw_not_match") {?>
            <div class="error">
                <p class="error_text">Error : The passwords you entered do not match</p>
            </div>
        <?php }?>
        <?php if (isset ($_GET['error']) && $_GET['error'] == "img_broken") {?>
            <div class="error">
                <p class="error_text">Error : The image you sent is wrong</p>
            </div>
        <?php }?>
    </div>
</div>
</BODY>
</HTML>