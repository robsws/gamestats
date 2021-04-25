<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  
require_once dirname(__FILE__).'/shared/database.php';
require_once dirname(__FILE__).'/objects/game.php';
require_once dirname(__FILE__).'/objects/play.php';
require_once dirname(__FILE__).'/objects/player.php';
require_once dirname(__FILE__).'/objects/score.php';

$db = getConnection();

try {
    $scores = fetchLeaderboard($db);
    foreach ($scores as &$score) {
        if (is_null($score['golds'])) {
            $score['golds'] = 0;
        }
        if (is_null($score['silvers'])) {
            $score['silvers'] = 0;
        }
        if (is_null($score['bronzes'])) {
            $score['bronzes'] = 0;
        }
    }
    http_response_code(200);
    echo json_encode($scores);
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(array("message" => "Unable to fetch leaderboard."));
}