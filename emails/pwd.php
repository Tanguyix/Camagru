<?php
$message = "<h1>Hello " . $user['name'] . "!!!</h1>" .
    "<p>Thanks for creating an account on Camargue'u! To start creating beautiful pics and share them, click on " .
    "<a href='http://localhost:8080/modif_pw.php?uid=" . $user['id'] . "&code=" . $code . "'>this link</a></p>";
