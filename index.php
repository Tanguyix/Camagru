<?php
    session_start();
    require 'config/setup.php';
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
        <?php if ($_SESSION['logged_on_user'] != "") { ?>
            <form action="utils/logout.php" method="get">
                <input class="send" type="submit" name="submit" value="Logout">
            </form>
        <?php } ?>
        </div>
    </div>
    <footer class ="foot">
        <p class="name">© tboissel, 42, 2019</p>
    </footer>
</BODY>
</HTML>