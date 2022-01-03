<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    include_once '../../config/database.php';
    include_once '../../models/Games/class/games.php';

    $database = new Database();
    $db = $database->getConnection();
    $game = new Game($db);

    $data = json_decode(file_get_contents("php://input"));

    $game->game_code = isset($data->game_code) ? $data->game_code : die();

    $game->getGameInfos();

    if($game->name != null){
        
        http_response_code(200);
        echo json_encode(array(
            "message" => "Game ".$game->name." successfuly loaded.",
            "gameInfos" => array(
                "name" => $game->name,
                "gameCode" => $game->game_code,
                "created" => $game->created
        )));
    }
    else {
        http_response_code(400);
        echo json_encode(array(
            "message" => "Game could not be loaded."
        ));
    }
    
?>