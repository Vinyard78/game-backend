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

    /*echo json_encode(array(
        "data" => json_decode(file_get_contents("php://input"))
    ));*/

    $game->game_code = isset($data->game_code) ? $data->game_code : die();

    $game->getGame();

    if($game->name != null){

        $game->landscapes = json_decode($game->landscapes);
        $game->characters = json_decode($game->characters);
        $game->events = json_decode($game->events);
        $game->sprites = json_decode($game->sprites);
        
        http_response_code(200);
        echo json_encode(array(
            "message" => "Game ".$game->name." successfuly loaded.",
            "game" => array(
                "name" => $game->name,
                "gameCode" => $game->game_code,
                "created" => $game->created,
                "landscapes" => $game->landscapes,
                "characters" => $game->characters,
                "events" => $game->events,
                "sprites" => $game->sprites
        )));
    }
    else {
        http_response_code(400);
        echo json_encode(array(
            "message" => "Game could not be loaded."
        ));
    }
    
?>