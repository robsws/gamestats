<?php

require_once dirname(__FILE__).'/../shared/database.php';

function fetchGameByName($conn, $name) {
    $query = "SELECT id, name FROM game WHERE name = :name";
    $sth = runSql($conn, $query, array(
        "name" => $name
    ));
    return $sth->fetch(PDO::FETCH_ASSOC);
}

function addNewGame($conn, $name) {
    $query = "INSERT INTO game (name) VALUES (:name)";
    $sth = runSql($conn, $query, array(
        "name" => $name
    ));
    return $conn->lastInsertId();
}

?>