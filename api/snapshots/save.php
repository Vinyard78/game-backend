<?php
	include_once '../../config/database.php';
	include_once '../../config/secretkey.php';
	include_once '../../models/Users/class/users.php';
	include_once '../../models/Snapshots/class/snapshots.php';
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
	$snapshot = new Snapshot($db);
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

	        	$snapshot->save_name = $data->saveName;
	        	$snapshot->project_id = $data->project_id;
	        	$snapshot->landscapes = json_encode($data->landscapes);
	        	$snapshot->characters = json_encode($data->characters);
	        	$snapshot->events = json_encode($data->events);
	        	$snapshot->sprites = json_encode($data->sprites);
	        	$snapshot->hashcode = $data->hashcode;
	        	$snapshot->created = date('Y-m-d H:i:s');
	        	$snapshot->prod = $data->prod;

	        	if($snapshot->createSnapshot()){
	        		http_response_code(200);
			        echo json_encode(array(
			            "message" => "Snapshot created successfully.",
			            "snapshot" => array(
			            	"id" => $snapshot->id,
			            	"project_id" => $snapshot->project_id,
		                    "save_name" => $snapshot->save_name,
		                    "hashcode" => $snapshot->hashcode,
		                    "created" => $snapshot->created,
		                    "prod" => $snapshot->prod
		            )));
	        	}
	        	else {
	        		http_response_code(400);
		        	echo json_encode(array(
			            "message" => "Snapshot could not be saved."
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