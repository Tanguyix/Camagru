<?php
    require 'database.php';

    // Connexion au serveur de BDD
    try {
        $pdo = new PDO("mysql:host=localhost;port=3306", $DB_USER, $DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $ex) { exit($ex); };

    // Creation de la BDD
    try {
        $sql = "CREATE DATABASE IF NOT EXISTS $db;";
        $pdo->prepare($sql)->execute();
    } catch(PDOException $ex) { exit($ex); };

    $pdo = null; // Deconnexion au serveur de BDD

    // Connexion a la BDD
    try {
        $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $ex) { exit($ex); };

    // Creation de la table users
    try {
        $sql = "CREATE TABLE IF NOT EXISTS `users`
        (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(20),
            email VARCHAR(64),
            pwd VARCHAR(128),
            salt VARCHAR(20)
        );";
        $pdo->prepare($sql)->execute();
    } catch(PDOException $ex) { exit($ex); };

    // Creation de la table verified
    try {
        $sql = "CREATE TABLE IF NOT EXISTS `verified`
            (
                user_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(32),
                verified boolean
            );";
        $pdo->prepare($sql)->execute();
    } catch(PDOException $ex) { exit($ex); };

    //Creation of the settings table
    try {
        $sql = "CREATE TABLE IF NOT EXISTS `settings`
                (
                    user_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    notif_on boolean,
                    lang varchar(2)
                );";
        $pdo->prepare($sql)->execute();
    } catch(PDOException $ex) { exit($ex); };

    //Creation of the password reset table
    try {
        $sql = "CREATE TABLE IF NOT EXISTS `pwreset`
                    (
                        user_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        code varchar(32)
                    );";
        $pdo->prepare($sql)->execute();
    } catch(PDOException $ex) { exit($ex); };

    //Creation of the photo table, change image size
    try {
        $sql = "CREATE TABLE IF NOT EXISTS `photos`
                    (
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        user_id int not null,
                        image varbinary(256),
                        legend varchar(240)
                    );";
        $pdo->prepare($sql)->execute();
    } catch(PDOException $ex) { exit($ex); };

    //Creation of the likes table
    try {
        $sql = "CREATE TABLE IF NOT EXISTS `likes`
                    (
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        photos_id int not null,
                        user_id int not null
                    );";
        $pdo->prepare($sql)->execute();
    } catch(PDOException $ex) { exit($ex); };

    //Creation of the comments table
    try {
        $sql = "CREATE TABLE IF NOT EXISTS `comments`
        (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            photos_id int not null,
            user_id int not null,
            comment varchar(240)
        );";
            $pdo->prepare($sql)->execute();
            } catch(PDOException $ex) { exit($ex); };