<?php
    session_start();
    require 'config/setup.php';
?>
<HTML>

<HEAD>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1" />
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/gallery.css">
    <link rel="icon" type="image/png" href="img/Blason_Camargue.png">
    <TITLE>Camargue'U</TITLE>
</HEAD>

<BODY onload="infinite_display(0, 0)">
    <div class="wrapper">
        <div class="head">
            <a href="index.php"><img class="logo" src="img/Camargue_U.png"></a>
            <a class="montage" href="montage.php">Post</a>
            <a class="leaderboard" href="leaderboard.php?order=alpha">Leaderboard</a>
        <?php if (!isset($_SESSION['logged_on_user']) || $_SESSION['logged_on_user'] == "") { ?>
            <a class="login" href="login.php">Sign in</a>
            <a class="signup" href="create_account.php">Sign up</a>
        <?php }
         else { ?>
            <a class="Logout" href="utils/logout.php">Logout</a>
            <a class="login" href="account.php">My account</a>
        <?php } ?>
        </div>
        <div class="gallery">
        </div>
        <footer><p>Camagru, projet ecole 42</p>by Tanguy BOISSEL-DALLIER</footer>
        <?php if (isset ($_GET['success']) && $_GET['success'] == "welcome") {?>
            <div class="success">
                <p class="error_text">Hello <?php echo $_SESSION['logged_on_user'];?>, Welcome to Camargue'u !!!</p>
            </div>
        <?php }?>
    </div>
</BODY>
<script async src="js/comment.js"></script>
<script async src="js/like.js"></script>
<script async src="js/infinite.js"></script>
</HTML>