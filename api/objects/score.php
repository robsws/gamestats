<?php

require_once dirname(__FILE__).'/../shared/database.php';

function fetchScoreById($conn, $id) {
    $query = "SELECT id, play_id, player_id, rank, points FROM score WHERE id = :id";
    $sth = runSql($conn, $query, array(
        "id" => $id
    ));
    return $sth->fetch(PDO::FETCH_ASSOC);
}

function addNewScore($conn, $play_id, $player_id, $rank, $points) {
    $query = "
        INSERT INTO score (play_id, player_id, rank, points)
        VALUES (:playid, :playerid, :rank, :points)";
    $sth = runSql($conn, $query, array(
        "playid" => $play_id,
        "playerid" => $player_id,
        "rank" => $rank,
        "points" => $points
    ));
    return $conn->lastInsertId();
}

?>