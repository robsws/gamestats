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
  
$amount = $_GET["amount"];
if (!$amount or !is_numeric($amount)) {
    http_response_code(400);
    echo json_encode(array("message" => "Invalid amount given as parameter"));
}

try {
    $scores = fetchMostRecentPlays($db, $amount);
    $plays = array();
    foreach ($scores as $score) {
        // Initialise play object
        if (!array_key_exists($score['play_id'], $plays)) {
            $plays[$score['play_id']] = array(
                'time' => $score['time'],
                'game_name' => $score['game_name'],
                'scores' => array()
            );
        }
        // Add score
        array_push($plays[$score['play_id']]['scores'], array(
            'player_name' => $score['player_name'],
            'points' => $score['points'],
            'rank' => $score['rank']
        ));
    }
    usort($plays, function($a, $b) {
        return $b['time'] <=> $a['time'];
    });
    http_response_code(200);
    echo json_encode($plays);
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(array("message" => "Unable to fetch history."));
}
