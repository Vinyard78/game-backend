<?php
    class User{

        // Connection
        private $conn;

        // Table
        private $db_table = "users";

        // Columns
        public $id;
        public $firstname;
        public $lastname;
        public $username;
        public $email;
        public $password;
        public $created;

        // Db connection
        public function __construct($db){
            $this->conn = $db;
        }

        // GET ALL
        public function getUsers(){
            $sqlQuery = "SELECT id, firstname, lastname, username, email, password, created FROM " . $this->db_table . "";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            return $stmt;
        }

        // CREATE
        public function createUser(){
            $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                    SET
                        firstname = :firstname, 
                        lastname = :lastname, 
                        username = :username, 
                        email = :email, 
                        password = :password, 
                        created = :created";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->firstname=htmlspecialchars(strip_tags($this->firstname));
            $this->lastname=htmlspecialchars(strip_tags($this->lastname));
            $this->username=htmlspecialchars(strip_tags($this->username));
            $this->email=htmlspecialchars(strip_tags($this->email));
            $this->password=htmlspecialchars(strip_tags($this->password));
            $this->created=htmlspecialchars(strip_tags($this->created));
        
            // bind data
            $stmt->bindParam(":firstname", $this->firstname);
            $stmt->bindParam(":lastname", $this->lastname);
            $stmt->bindParam(":username", $this->username);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":password", $this->password);
            $stmt->bindParam(":created", $this->created);
        
            if($stmt->execute()){
               return true;
            }
            return false;
        }

        // READ by id
        public function getUserById($id){
            $sqlQuery = "SELECT
                        id, 
                        firstname, 
                        lastname, 
                        username, 
                        email, 
                        password, 
                        created
                      FROM
                        ". $this->db_table ."
                    WHERE 
                       id = ?
                    LIMIT 0,1";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $id);

            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id = $dataRow['id'];
            $this->firstname = $dataRow['firstname'];
            $this->lastname = $dataRow['lastname'];
            $this->username = $dataRow['username'];
            $this->email = $dataRow['email'];
            $this->password = $dataRow['password'];
            $this->created = $dataRow['created'];
        }       

        // READ by log params
        public function getUserByLogin($login){
            $sqlQuery = "SELECT
                        id, 
                        firstname, 
                        lastname, 
                        username, 
                        email, 
                        password, 
                        created
                      FROM
                        ". $this->db_table ."
                    WHERE 
                        email = :login
                    OR
                        username = :login
                    LIMIT 0,1";

            $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $login=htmlspecialchars(strip_tags($login));
        
            // bind data
            $stmt->bindParam(":login", $login);

            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id = $dataRow['id'];
            $this->firstname = $dataRow['firstname'];
            $this->lastname = $dataRow['lastname'];
            $this->username = $dataRow['username'];
            $this->email = $dataRow['email'];
            $this->password = $dataRow['password'];
            $this->created = $dataRow['created'];
        } 

        // READ by username
        public function getUserByUsername($username){
            $sqlQuery = "SELECT
                        id, 
                        firstname, 
                        lastname, 
                        username, 
                        email, 
                        password, 
                        created
                      FROM
                        ". $this->db_table ."
                    WHERE 
                        username = :username
                    LIMIT 0,1";

            $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $username=htmlspecialchars(strip_tags($username));
        
            // bind data
            $stmt->bindParam(":username", $username);

            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id = $dataRow['id'];
            $this->firstname = $dataRow['firstname'];
            $this->lastname = $dataRow['lastname'];
            $this->username = $dataRow['username'];
            $this->email = $dataRow['email'];
            $this->password = $dataRow['password'];
            $this->created = $dataRow['created'];
        } 

        // READ by email
        public function getUserByEmail($email){
            $sqlQuery = "SELECT
                        id, 
                        firstname, 
                        lastname, 
                        username, 
                        email, 
                        password, 
                        created
                      FROM
                        ". $this->db_table ."
                    WHERE 
                        email = :email
                    LIMIT 0,1";

            $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $email=htmlspecialchars(strip_tags($email));
        
            // bind data
            $stmt->bindParam(":email", $email);

            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id = $dataRow['id'];
            $this->firstname = $dataRow['firstname'];
            $this->lastname = $dataRow['lastname'];
            $this->username = $dataRow['username'];
            $this->email = $dataRow['email'];
            $this->password = $dataRow['password'];
            $this->created = $dataRow['created'];
        } 

        // UPDATE
        public function updateUser(){
            $sqlQuery = "UPDATE
                        ". $this->db_table ."
                    SET
                        firstname = :firstname, 
                        lastname = :lastname,
                        username = :username,
                        email = :email, 
                        password = :password, 
                        created = :created
                    WHERE 
                        id = :id";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            $this->firstname=htmlspecialchars(strip_tags($this->firstname));
            $this->lastname=htmlspecialchars(strip_tags($this->lastname));
            $this->username=htmlspecialchars(strip_tags($this->username));
            $this->email=htmlspecialchars(strip_tags($this->email));
            $this->password=htmlspecialchars(strip_tags($this->password));
            $this->created=htmlspecialchars(strip_tags($this->created));
            $this->id=htmlspecialchars(strip_tags($this->id));
        
            // bind data
            $stmt->bindParam(":firstname", $this->firstname);
            $stmt->bindParam(":lastname", $this->lastname);
            $stmt->bindParam(":username", $this->username);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":password", $this->password);
            $stmt->bindParam(":created", $this->created);
            $stmt->bindParam(":id", $this->id);
        
            if($stmt->execute()){
               return true;
            }
            return false;
        }

        // DELETE
        function deleteUser(){
            $sqlQuery = "DELETE FROM " . $this->db_table . " WHERE id = ?";
            $stmt = $this->conn->prepare($sqlQuery);
        
            $this->id=htmlspecialchars(strip_tags($this->id));
        
            $stmt->bindParam(1, $this->id);
        
            if($stmt->execute()){
                return true;
            }
            return false;
        }

        // VERIFY
        public function verifyUser($id,$email,$username){
            $sqlQuery = "SELECT
                        id, 
                        firstname, 
                        lastname, 
                        username, 
                        email, 
                        password, 
                        created
                      FROM
                        ". $this->db_table ."
                    WHERE 
                        id = :id 
                    AND
                        username = :username
                    AND
                        email = :email
                    LIMIT 0,1";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            $username=htmlspecialchars(strip_tags($username));
            $email=htmlspecialchars(strip_tags($email));
            $id=htmlspecialchars(strip_tags($id));
        
            // bind data
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":id", $id);

            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id = $dataRow['id'];
            $this->firstname = $dataRow['firstname'];
            $this->lastname = $dataRow['lastname'];
            $this->username = $dataRow['username'];
            $this->email = $dataRow['email'];
            $this->password = $dataRow['password'];
            $this->created = $dataRow['created'];
        }

    }
?>