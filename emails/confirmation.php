<?php
    $message = "<h1>Hello " . $_POST['login'] . "!!!</h1>" .
        "<p>Thanks for creating an account on Camargue'u! To start creating beautiful pics and share them, click on " .
        "<a href='http://localhost:8080/verify.php?uid=" . $user['id'] . "&code=" . $code . "'>this link</a></p>";
