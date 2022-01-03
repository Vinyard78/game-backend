<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    include_once '../../config/database.php';
    include_once '../../models/Games/class/games.php';

    // If preflight
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {    
        http_response_code(200);
    } else {

        $database = new Database();
        $db = $database->getConnection();
        $games = new Game($db);

        $stmt = $games->getPublishedGamesInfos();
        $gameInfosCount = $stmt->rowCount();

        if($gameInfosCount > 0){

            $gameInfosArr = array();
            $gameInfosArr["array"] = array();
            $gameInfosArr["count"] = $gameInfosCount;

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);
                $e = array(
                    "name" => $name,
                    "gameCode" => $game_code,
                    "created" => $created
                );
                array_push($gameInfosArr["array"], $e);
            }

            http_response_code(200);
            echo json_encode(array(
                "message" => "Games Infos successfully loaded.",
                "publishedGamesInfos" => $gameInfosArr
            ));
        }

        else {
            http_response_code(400);
            echo json_encode(array(
                "message" => "Games Infos could not be loaded."
            ));
        }

    }

?>