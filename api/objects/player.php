<?php

require_once dirname(__FILE__).'/../shared/database.php';

function fetchPlayerByName($conn, $name) {
    $query = "SELECT id, name FROM player WHERE name = :name";
    $sth = runSql($conn, $query, array(
        "name" => $name
    ));
    return $sth->fetch(PDO::FETCH_ASSOC);
}

function addNewPlayer($conn, $name) {
    $query = "INSERT INTO player (name) VALUES (:name)";
    $sth = runSql($conn, $query, array(
        "name" => $name
    ));
    return $conn->lastInsertId();
}

?>