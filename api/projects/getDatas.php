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
    include_once '../../models/Resources/class/resources.php';
    require "../../vendor/autoload.php";
    use \Firebase\JWT\JWT;

    $jwt = null;
    $database = new Database();
    $db = $database->getConnection();
    $snapshots = new Snapshot($db);
    $resources = new Resource($db);
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

                $project_id = isset($data->project_id) ? $data->project_id : die();

                // Get snapshots for Project
                $stmt = $snapshots->getInfosForProject($project_id);
                $snapshotCount = $stmt->rowCount();
                        
                $snapshotArr = array();
                $snapshotArr["array"] = array();
                $snapshotArr["count"] = 0;
                
                if($snapshotCount > 0){
                    $snapshotArr["count"] = $snapshotCount;
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                        extract($row);
                        $e = array(
                            "id" => $id,
                            "project_id" => $project_id,
                            "save_name" => $save_name,
                            "hashcode" => $hashcode,
                            "created" => $created,
                            "prod" => $prod
                        );
                        array_push($snapshotArr["array"], $e);
                    }
                }

                // Get resources for Project
                $stmt = $resources->getResourcesForProject($project_id);
                $resourceCount = $stmt->rowCount();
                        
                $resourceArr = array();
                $resourceArr["array"] = array();
                $resourceArr["count"] = 0;
                
                if($resourceCount > 0){
                    $resourceArr["count"] = $resourceCount;
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                        extract($row);
                        $e = array(
                            "id" => $id,
                            "filename" => $filename,
                            "folder" => $type,
                            "category" => $category,
                            "infos" => $type == "img" ? getimagesize("../resources/img/".$filename) : []
                        );
                        array_push($resourceArr["array"], $e);
                    }
                }

                http_response_code(200);
                echo json_encode(array(
                    "message" => "Infos from project successfully loaded.",
                    "snapshots" => $snapshotArr,
                    "resources" => $resourceArr
                ));

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