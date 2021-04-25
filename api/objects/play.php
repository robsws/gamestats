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

function fetchMostRecentPlays($conn, $amount) {
    $play_query = "
        SELECT
            p.id as play_id,
            p.time,
            p.game_name,
            player.name as player_name,
            score.rank,
            score.points
        FROM
        (
            SELECT play.id, play.time, game.name as game_name
            FROM play INNER JOIN game ON play.game_id = game.id
            ORDER BY play.time DESC
            LIMIT ?
        ) p
        INNER JOIN score ON p.id = score.play_id
        INNER JOIN player ON score.player_id = player.id
        ORDER BY p.time DESC, p.id
    ";
    $play_sth = runSql($conn, $play_query, array($amount));
    return $play_sth->fetchAll(PDO::FETCH_ASSOC);
}

?>