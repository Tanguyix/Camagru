<?php
    //function called by login to check if the user exists and uses the right password
    function auth($login, $pwd) {
        $db = 'camargue_u';
        $DB_DSN = "mysql:host=localhost;dbname=$db;port=3307";
        $DB_USER = "root";
        $DB_PASSWORD = "rootroot";
        //connect to the database
        try {
            $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $ex) { exit($ex); };

        //get the 3 needed infos from database

        try {
            $sql = "SELECT `name`, `pwd`, `salt`
            FROM `users` WHERE `name` = :name";
            $check = $pdo->prepare($sql);
            $check->execute(array(':name' => $login));
            $user = $check->fetch();
        } catch(PDOException $ex) { exit($ex); };
        //if user does't exist, no further check
        if ($user === false) {
            return(false);
        }
        //Get the pwd given by the user, salt and hash it and compare with the hashed pwd in the db
        $salted_pw = $pwd . $user['salt'];
        $salted_pw = (hash("sha512", $salted_pw));
        if ($salted_pw == $user['pwd']) {
            return (true);
        }
        return (false);
    }