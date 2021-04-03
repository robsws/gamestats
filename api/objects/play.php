<?php

require_once dirname(__FILE__).'/../shared/database.php';

function fetchPlayById($conn, $id) {
    $query = "SELECT id, game_id, time FROM play WHERE id = :id";
    $sth = runSql($conn, $query, array(
        "id" => $id
    ));
    return $sth->fetch(PDO::FETCH_ASSOC);
}

function addNewPlay($conn, $game_id, $time) {
    $query = "
        INSERT INTO play (game_id, `time`)
        VALUES (:gameid, :time);";
    $sth = runSql($conn, $query, array(
        "gameid" => $game_id,
        "time" => $time
    ));
    return $conn->lastInsertId();
}

?>