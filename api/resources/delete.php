<?php
    include_once '../../config/database.php';
    include_once '../../config/secretkey.php';
    include_once '../../models/Users/class/users.php';
    include_once '../../models/Resources/class/resources.php';
    require "../../vendor/autoload.php";
    use \Firebase\JWT\JWT;

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    $jwt = null;
    $database = new Database();
    $db = $database->getConnection();
    $resource = new Resource($db);
    $user = new User($db);
    
    $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];

    $arr = explode(" ", $authHeader);

    $jwt = $arr[1];

    if($jwt){

        try {

            $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
            $userData = $decoded->data;

            // User verifications
            $user->verifyUser($userData->id,$userData->email,$userData->username);
            
            if($user->id != null){

                $data = json_decode(file_get_contents("php://input"));
                $resource->id = $data->id;

                /*echo json_encode(array(
                    "data" => $data
                    //"message" => "sd" .$arr[1]
                ));*/

                if($resource->deleteResource()){
                    $file_pointer = "./".$data->folder."/".$data->filename;
                    if(unlink($file_pointer)){
                        http_response_code(200);
                        echo json_encode(array(
                            "message" => "Resource deleted."
                        ));
                    }
                    else {
                        http_response_code(200);
                        echo json_encode(array(
                            "message" => "Resource deleted from database but not from directory."
                        ));
                    }
                }
                else {
                    http_response_code(400);
                    echo json_encode(array(
                        "message" => "Resource could not be deleted."
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