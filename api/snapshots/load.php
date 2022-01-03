<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    include_once '../../config/database.php';
    include_once '../../config/secretkey.php';
    include_once '../../models/Users/class/users.php';
    include_once '../../models/Snapshots/class/snapshots.php';
    require "../../vendor/autoload.php";
    use \Firebase\JWT\JWT;

    $jwt = null;
    $database = new Database();
    $db = $database->getConnection();
    $snapshot = new Snapshot($db);
    $user = new User($db);
    
    $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];

    $arr = explode(" ", $authHeader);

    /*echo json_encode(array(
        "data" => json_decode(file_get_contents("php://input"))
    ));*/

    $jwt = $arr[1];

    if($jwt){

        try {

            $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
            $userData = $decoded->data;

            // User verifications
            $user->verifyUser($userData->id,$userData->email,$userData->username);
            
            if($user->id != null){

                $data = json_decode(file_get_contents("php://input"));

                $snapshot->id = $data->id;

                $snapshot->getSnapshotById();

                if($snapshot->id != null){
                    http_response_code(200);

                    $snapshot->landscapes = json_decode($snapshot->landscapes);
                    $snapshot->characters = json_decode($snapshot->characters);
                    $snapshot->events = json_decode($snapshot->events);
                    $snapshot->sprites = json_decode($snapshot->sprites);
                    
                    echo json_encode(array(
                        "message" => "Snapshot successfuly loaded.",
                        "snapshot" => $snapshot
                    ));
                }
                else {
                    http_response_code(400);
                    echo json_encode(array(
                        "message" => "Snapshot could not be loaded."
                    ));
                }


            } else {
                http_response_code(400);
                echo json_encode(array(
                    "message" => "User not found."
                ));
            }          

        } catch (Exception $e){

            http_response_code(401);

            echo json_encode(array(
                "message" => "Access denied.",
                "error" => $e->getMessage()
            ));
        }
    }

    else {
        // If preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {    
            http_response_code(200);
        } else {
            http_response_code(401);
            echo json_encode(array(
                "message" => "Access denied."
            ));
        }
    }
?>