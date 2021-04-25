<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  
require_once dirname(__FILE__).'/shared/database.php';
require_once dirname(__FILE__).'/objects/game.php';
require_once dirname(__FILE__).'/objects/play.php';
require_once dirname(__FILE__).'/objects/player.php';
require_once dirname(__FILE__).'/objects/score.php';

$db = getConnection();
  
// get posted data
$data = json_decode(file_get_contents("php://input"));

/* Format of request body to record

{
    "game": "game_name",
    "time": "datetime, empty uses current time",
    "tags": ["tag1", "tag2"],
    "scores": [
        {
            "player": "player_name",
            "rank": rank (integer),
            "points": points scored (integer),
        },
        {
            "player": "player_name_2",
            "rank": rank (integer),
            "points": points scored (integer),
        },
        ...
    ]
}
*/

if (!isset($data->game) or !isset($data->scores)) {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to create play. game and scores are required."));
}

// Create the parent play object
$db->beginTransaction();
try {
    $game = fetchGameByName($db, $data->game);
    if ($game) {
        $game_id = $game['id'];
    } else {
        $game_id = addNewGame($db, $data->game);
    }
    if (!isset($data->time)) {
        $data->time = date('Y-m-d H:i:s');
    }
    $play_id = addNewPlay($db, $game_id, $data->time, $data->tags);
    // Create a score object for each score
    foreach ($data->scores as $score) {
        $player = fetchPlayerByName($db, $score->player);
        if ($player) {
            $player_id = $player['id'];
        } else {
            $player_id = addNewPlayer($db, $score->player);
        }
        addNewScore($db, $play_id, $player_id, $score->rank, $score->points);
    }
    $db->commit();
    http_response_code(201);
    echo json_encode(array("message" => "Play was recorded."));
} catch(PDOException $exception) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(array("message" => "Unable to record play."));
}
?>