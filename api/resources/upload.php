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

	$data = isset($_FILES['file0']) ? $_FILES['file0'] : die();
    
	$authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];

	$arr = explode(" ", $authHeader);

	/*echo json_encode(array(
		"data" => $_GET['category']
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

	        	$typeArr = explode("/", $data['type']);
	        	switch($typeArr[0]){
	        		case "image": $type = "img";break;
	        		case "audio": $type = "audio";break;
	        		case "font": $type = "font";break;
	        		default: "";break;
	        	}

	        	// On enleve les espace du nom de fichier
	        	$resource->filename = $userData->id."-".time()."-".str_replace(' ', '_', $data['name']);
	        	$resource->type = $type;
	        	$resource->category = $_GET['category'];
	        	$resource->project_id = $_GET['project_id'];
	        	$file_pointer = "./".$resource->type."/".$resource->filename;

	        	if(move_uploaded_file($_FILES['file0']['tmp_name'],$file_pointer)){
	        		if($resource->createResource()){
	        			http_response_code(200);
				        echo json_encode(array(
				            "message" => "Resource created successfully.",
				            "resource" => array(
				            	"id" => $resource->id,
			                    "filename" => $resource->filename,
			                    "folder" => $resource->type,
			                    "category" => $resource->category,
			                    "project_id" => $resource->project_id,
			                    "infos" => 	$resource->type == "img" ? getimagesize($file_pointer) : []
			            )));
	        		}
	        		else {
	        			unlink($file_pointer);

	        			http_response_code(400);
			        	echo json_encode(array(
				            "message" => "Image could not be saved."
				        ));
	        		}
	        	}
	        	else {
	        		http_response_code(400);
		        	echo json_encode(array(
			            "message" => "Image could not be saved."
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