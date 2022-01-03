<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    include_once '../../config/database.php';
    include_once '../../config/secretkey.php';
    include_once '../../models/Users/class/users.php';
    include_once '../../models/Projects/class/projects.php';
    require "../../vendor/autoload.php";
    use \Firebase\JWT\JWT;

    $jwt = null;
    $database = new Database();
    $db = $database->getConnection();
    $project = new Project($db);
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

                $project->id = $data->id;

                // snapshot values
                $project->name = $data->name;
                $project->hashcode = $data->hashcode;
                $project->created = $data->created;
                $project->status = "published";
                $project->published_snapshot_id = $data->publishedSnapshotId;

                if($project->updateProject()){
                    http_response_code(200);

                    /*$snapshot->landscapes = json_decode($snapshot->landscapes);
                    $snapshot->characters = json_decode($snapshot->characters);
                    $snapshot->events = json_decode($snapshot->events);*/
                    
                    echo json_encode(array(
                        "message" => "Project successfuly published.",
                        "project" => array(
                            "id" => $project->id,
                            "name" => $project->name,
                            "hashcode" => $project->hashcode,
                            "created" => $project->created,
                            "status" => $project->status,
                            "publishedSnapshotId" => $project->published_snapshot_id
                    )));    
                }
                else {
                    http_response_code(400);
                    echo json_encode(array(
                        "message" => "Project could not be published."
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