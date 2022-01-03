<?php
    include_once '../../config/database.php';
    include_once '../../config/secretkey.php';
    include_once '../../models/Users/class/users.php';
    include_once '../../models/Projects/class/projects.php';
    include_once '../../models/UsersProjects/class/users_projects.php';
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
    $project = new Project($db);
    $user_project = new UserProject($db);
    $user = new User($db);

    $data = json_decode(file_get_contents("php://input"));
    
    $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];

    $arr = explode(" ", $authHeader);

    /*echo json_encode(array(
        "data" => $data
        //"message" => "sd" .$arr[1]
    ));*/

    $jwt = $arr[1];

    if($jwt){

        try {

            $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
            $userData = $decoded->data;

            // User verifications
            $user->verifyUser($userData->id,$userData->email,$userData->username);
            
            if($user->id != null){

                $project->name = $data->name;
                $project->hashcode = $data->hashcode;
                $project->created = date('Y-m-d H:i:s');
                //$project->status = $data->status;

                if($project->createProject()){

                    $user_project->user_id = $user->id;
                    $user_project->project_id = $project->id; 

                    if($user_project->createLink()){
                        http_response_code(200);
                        echo json_encode(array(
                            "message" => "Project created successfully.",
                            "project" => array(
                                "id" => $project->id,
                                "name" => $project->name,
                                "hashcode" => $project->hashcode,
                                "created" => $project->created,
                                "status" => $project->status,
                                "publishedSnapshotId" => $project->published_snapshot_id,
                                "resources" => array( "array" => array(),"count" => 0),
                                "snapshots" => array( "array" => array(),"count" => 0)
                        )));
                    }
                    else {
                        $project->deleteProject();
                        
                        http_response_code(400);
                        echo json_encode(array(
                            "message" => "Project could not be created."
                        ));
                    }
                }
                else {
                    http_response_code(400);
                    echo json_encode(array(
                        "message" => "Project could not be created."
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