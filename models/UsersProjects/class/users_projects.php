<?php
    class UserProject{

        // Connection
        private $conn;

        // Table
        private $db_table = "users_projects";

        // Columns
        public $id;
        public $user_id;
        public $project_id;

        // Db connection
        public function __construct($db){
            $this->conn = $db;
        }

        // CREATE
        public function createLink(){
            $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                    SET
                        user_id = :user_id, 
                        project_id = :project_id";
        
            $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $this->user_id=htmlspecialchars(strip_tags($this->user_id));
            $this->project_id=htmlspecialchars(strip_tags($this->project_id));
        
            // bind data
            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->bindParam(":project_id", $this->project_id);
        
            if($stmt->execute()){
                $id = $this->conn->lastInsertId();
                $this->id = $id;
                return true;
            }
            return false;
        }

        // DELETE
        public function deleteLink(){
            $sqlQuery = "DELETE FROM " . $this->db_table . " WHERE user_id = :user_id AND project_id = :project_id";
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->user_id=htmlspecialchars(strip_tags($this->user_id));
            $this->project_id=htmlspecialchars(strip_tags($this->project_id));
        
            // bind data
            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->bindParam(":project_id", $this->project_id);
        
            if($stmt->execute()){
                return true;
            }
            return false;
        }

        // DELETE by link_id
        public function deleteLinkById(){
            $sqlQuery = "DELETE FROM " . $this->db_table . " WHERE id = ?";
            $stmt = $this->conn->prepare($sqlQuery);
        
            $this->id=htmlspecialchars(strip_tags($this->id));
        
            $stmt->bindParam(1, $this->id);
        
            if($stmt->execute()){
                return true;
            }
            return false;
        }

    }
?>