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
    /*include_once '../../models/Resources/class/resources.php';
    include_once '../../models/Snapshots/class/snapshots.php';*/
    require "../../vendor/autoload.php";
    use \Firebase\JWT\JWT;

    $database = new Database();
    $db = $database->getConnection();

    $user = new User($db);
    $projects = new Project($db);
    /*$resources = new Resource($db);
    $snapshots = new Snapshot($db);*/

    $data = json_decode(file_get_contents("php://input"));

    $login = isset($data->login) ? $data->login : die();
    $password = isset($data->password) ? $data->password : die();
  
    $user->getUserByLogin($login);
    
    if($user->id != null){
        if(password_verify($password, $user->password)) {

            // Get projects for User
            $stmt = $projects->getProjectsForUser($user->id);
            $projectCount = $stmt->rowCount();
            
            $projectArr = array();
            $projectArr["array"] = array();
            $projectArr["count"] = 0;
            
            if($projectCount > 0){
                $projectArr["count"] = $projectCount;
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    extract($row);
                    $e = array(
                        "id" => $id,
                        "name" => $name,
                        "hashcode" => $hashcode,
                        "created" => $created,
                        "status" => $status,
                        "publishedSnapshotId" => $published_snapshot_id,
                        "resources" => array( "array" => array(),"count" => 0),
                        "snapshots" => array( "array" => array(),"count" => 0)
                    );
                    array_push($projectArr["array"], $e);
                }
            }

            // Create Token JWT
            $issuer_claim = "histoiresderisoires.fr"; 
            $audience_claim = "THE_AUDIENCE";
            $issuedat_claim = time(); 
            $notbefore_claim = $issuedat_claim + 10; 
            $expire_claim = $issuedat_claim + 3600 * 24; 
            $token = array(
                "iss" => $issuer_claim,
                "aud" => $audience_claim,
                "iat" => $issuedat_claim,
                "nbf" => $notbefore_claim,
                "exp" => $expire_claim,
                "data" => array(
                    "id" => $user->id,
                    "username" => $user->username,
                    "email" => $user->email
            ));
            $jwt = JWT::encode($token, $secret_key);

            http_response_code(200);
            echo json_encode(array(
                "message" => "Successful login.",
                "token" => $jwt,
                "username" => $user->username,
                "projects" => $projectArr,
                "expireAt" => $expire_claim
            ));
        } 

        else {
            http_response_code(401);
            echo json_encode(array(
                "message" => "Login failed, incorrect password."
            ));
        }
    }

    else {
        http_response_code(401);
        echo json_encode(array(
            "message" => "Login failed, user unknown."
        ));
    }
?>