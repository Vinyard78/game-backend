<?php
    include_once '../../config/database.php';
    include_once '../../config/secretkey.php';
    include_once '../../models/Users/class/users.php';
    include_once '../../models/Projects/class/projects.php';
    include_once '../../models/UsersProjects/class/users_projects.php';
    include_once '../../models/Snapshots/class/snapshots.php';
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
    $project = new Project($db);
    $user_project = new UserProject($db);
    $user = new User($db);
    $snapshot = new Snapshot($db);
    $resource = new Resource($db);
    
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

                $user_project->user_id = $user->id;
                $user_project->project_id = $project->id;

                // On supprime d'abord le lien avec l'utilisateur
                if($user_project->deleteLink()){
                    // Puis le projet lui meme
                    if($project->deleteProject()){
                        // Ensuite les snapshots liés a ce projet  
                        if($snapshot->deleteSnapshotsForProject($project->id)){
                            // Et enfin les ressources liées au projet 
                            // On les recupere d'abord pour pouvoir les supprimer du dossier resources/img
                            $stmt = $resource->getResourcesForProject($project->id);
                            $resourceCount = $stmt->rowCount();
                            
                            if($resourceCount > 0){
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                                    extract($row);
                                    $file_pointer = "../resources/".$type."/".$filename;
                                    unlink($file_pointer);
                                }
                            }

                            //Puis on les supprime de la base
                            if($resource->deleteResourcesForProject($project->id)){
                                http_response_code(200);
                                echo json_encode(array(
                                    "message" => "Project deleted."
                                ));
                            }
                            else {
                                http_response_code(400);
                                echo json_encode(array(
                                    "message" => "Resources from project could not be deleted."
                                ));
                            }
                        }
                        else {
                            http_response_code(400);
                            echo json_encode(array(
                                "message" => "Snaphots from project could not be deleted."
                            ));
                        }
                    }
                    else {
                        http_response_code(400);
                        echo json_encode(array(
                            "message" => "Project could not be deleted."
                        ));
                    }
                }
                else {
                    http_response_code(400);
                    echo json_encode(array(
                        "message" => "Link between project and user could not be broken."
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