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

    //get infos for the given id
    $sql = "SELECT users.id, users.pwd, users.salt, pwreset.user_id, pwreset.code
    FROM `users`
    JOIN `pwreset` ON users.id=pwreset.user_id
    WHERE users.id=:id;";
    $check = $pdo->prepare($sql);
    $check->execute(array(':id' => $_GET['uid']));
    $line = $check->fetch();

    if ($line === false) {
        echo "Error, user doesn't exist";
    }
    //check if code is right
    if ($line['code'] != $_GET['code']) {
        echo "change pw failed";
    }
    else {
        if (isset($_POST['pwd']) && $_POST['pwd'] != "" && isset($_POST['re_pwd']) && $_POST['re_pwd'] != "") {
            if ($_POST['pwd'] != $_POST['re_pwd']) {echo "passwords don't match";}
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
    }
}
?>
<HTML>
<meta charset="UTF-8">
<link rel="stylesheet" href="css/global.css">
<link rel="stylesheet" href="css/modif_pw.css">
<link rel="icon" type="image/png" href="img/Blason_Camargue.png">

<HEAD>
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
    <div class="container">
        <form action="modif_pw.php?<?php echo "uid=" . $_GET['uid'] . "&code=" . $_GET['code'] ?>" method="post">
            <p>password</p>
            <input type="password" name="pwd" value="" required>
            <br/>
            <p>repeat password</p>
            <input type="password" name="re_pwd" value="" required>
            <input class="send" type="submit" name="submit" value="Change password">
        </form>
    </div>
</div>
<footer class ="foot">
    <p class="name">© tboissel, 42, 2019</p>
</footer>
</BODY>
</HTML>