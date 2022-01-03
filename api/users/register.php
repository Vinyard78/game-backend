<?php

	header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    include_once '../../config/database.php';
    include_once '../../models/Users/class/users.php';

    $database = new Database();
    $db = $database->getConnection();

    $user = new User($db);

    $data = json_decode(file_get_contents("php://input"));

    // login params
    $username = $data->username; 
    $email = $data->email;

    // boolean
    $isUsernameInUse = false;
    $isEmailInUse = false;

    // Verify username
    $user->getUserByUsername($username);
    if($user->id != null){
        $isUsernameInUse = true;
    }

    $user = new User($db);

    // Verify email
    $user->getUserByEmail($email);
    if($user->id != null){
        $isEmailInUse = true;
    }

    if($isUsernameInUse || $isEmailInUse){
        http_response_code(400);
        echo json_encode(array("message" => ($isUsernameInUse ? ($isEmailInUse ? "Username and email" : "Username") : "Email") . " allready in use."));
    } else {

        $user = new User($db);

        $user->firstname = $data->firstname;
        $user->lastname = $data->lastname;
        $user->username = $data->username;
        $user->email = $data->email;
        $user->password = password_hash($data->password, PASSWORD_BCRYPT);
        $user->created = date('Y-m-d H:i:s');

        if($user->createUser()){
            http_response_code(200);
            echo json_encode(array("message" => "User was successfully registered."));
        } else{
            http_response_code(400);
            echo json_encode(array("message" => "Unable to register the user."));
        }
    }
    
?>