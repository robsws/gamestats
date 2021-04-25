<?php

require_once dirname(__FILE__).'/../shared/database.php';

function fetchPlayById($conn, $id) {
    $query = "SELECT id, game_id, time FROM play WHERE id = :id";
    $sth = runSql($conn, $query, array(
        "id" => $id
    ));
    return $sth->fetch(PDO::FETCH_ASSOC);
}

function addNewPlay($conn, $game_id, $time, $tags) {
    $play_query = "
        INSERT INTO play (game_id, `time`)
        VALUES (:gameid, :time);";
    $play_sth = runSql($conn, $play_query, array(
        "gameid" => $game_id,
        "time" => $time
    ));
    $play_id = $conn->lastInsertId();
    $tag_query = "
        INSERT IGNORE INTO tags (play_id, tag)
        VALUES (:playid, :tag)
    ";
    $tag_sth = $conn->prepare($tag_query);
    foreach ($tags as $tag) {
        $tag = sanitize($tag);
        $tag_sth->execute($play_id, $tag);
    }
    return $play_id;
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

function fetchLeaderboard($conn) {
    $query = "
        SELECT player.name, g.golds, s.silvers, b.bronzes
        FROM player
        LEFT JOIN (
            SELECT player.id, COUNT(*) as golds
            FROM player INNER JOIN score on player.id = score.player_id
            WHERE rank = 1
            GROUP BY player.id
        ) g ON player.id = g.id
        LEFT JOIN (
            SELECT player.id, COUNT(*) as silvers
            FROM player INNER JOIN score on player.id = score.player_id
            WHERE rank = 2
            GROUP BY player.id
        ) s ON player.id = s.id
        LEFT JOIN (
            SELECT player.id, COUNT(*) as bronzes
            FROM player INNER JOIN score on player.id = score.player_id
            WHERE rank = 3
            GROUP BY player.id
        ) b ON player.id = s.id
        ORDER BY g.golds DESC, s.silvers DESC, b.bronzes DESC
    ";
    $sth = runSql($conn, $query, array());
    return $sth->fetchAll(PDO::FETCH_ASSOC);
}

?>