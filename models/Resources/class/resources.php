<?php
    class Resource{

        // Connection
        private $conn;

        // Table
        private $db_table = "resources";

        // Columns
        public $id;
        public $filename;
        public $type;
        public $category;
        public $project_id;

        // Db connection
        public function __construct($db){
            $this->conn = $db;
        }

        // GET ALL
        public function getResources(){
            $sqlQuery = "SELECT id, filename, type, category FROM " . $this->db_table . "";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            return $stmt;
        }

        // CREATE
        public function createResource(){
            $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                    SET
                        filename = :filename, 
                        type = :type,
                        category = :category,
                        project_id = :project_id";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->filename=htmlspecialchars(strip_tags($this->filename));
            $this->type=htmlspecialchars(strip_tags($this->type));
            $this->category=htmlspecialchars(strip_tags($this->category));
            $this->project_id=htmlspecialchars(strip_tags($this->project_id));
        
            // bind data
            $stmt->bindParam(":filename", $this->filename);
            $stmt->bindParam(":type", $this->type);
            $stmt->bindParam(":category", $this->category);
            $stmt->bindParam(":project_id", $this->project_id);

            if($stmt->execute()){
                $id = $this->conn->lastInsertId();
                $this->id = $id;
                return true;
            }
            return false;
        }

        /*// CREATE for user
        public function createResourceForUser($userId){
            $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                    SET
                        filename = :filename, 
                        type = :type;

                    INSERT INTO 
                        users_resources 
                    SET 
                        user_id = :userId, 
                        resource_id = (
                            SELECT 
                                id 
                            FROM
                                ". $this->db_table ."  
                            WHERE 
                                users.id = 1 
                            AND 
                                users.username = 'Vinyard' 
                        )";

            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->filename=htmlspecialchars(strip_tags($this->filename));
            $this->type=htmlspecialchars(strip_tags($this->type));
            $userId=htmlspecialchars(strip_tags($userId));
        
            // bind data
            $stmt->bindParam(":filename", $this->filename);
            $stmt->bindParam(":type", $this->type);
            $stmt->bindParam(":userId", $userId);
        
            if($stmt->execute()){
               return true;
            }
            return false;
        }*/

        // READ by id
        public function getResourceById($id){
            $sqlQuery = "SELECT
                        id, 
                        filename, 
                        type,
                        category,
                        project_id
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
            $this->filename = $dataRow['filename'];
            $this->type = $dataRow['type'];
            $this->category = $dataRow['category'];
            $this->project_id = $dataRow['project_id'];
        }       

        // READ all for a user
        /*public function getResourcesForUser($userId){
            $sqlQuery = "
                SELECT
                    id,
                    filename, 
                    type,
                    category,
                    user_id
                FROM
                    ". $this->db_table ." 
                WHERE 
                   user_id = ?";

            $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $userId=htmlspecialchars(strip_tags($userId));
        
            // bind data
            $stmt->bindParam(1, $userId);

            $stmt->execute();

            return $stmt;
        }*/

        // READ all resources for a project
        public function getResourcesForProject($projectId){
            $sqlQuery = "
                SELECT
                    id,
                    filename, 
                    type,
                    category,
                    project_id
                FROM
                    ". $this->db_table ." 
                WHERE 
                   project_id = ?";

            $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $projectId=htmlspecialchars(strip_tags($projectId));
        
            // bind data
            $stmt->bindParam(1, $projectId);

            $stmt->execute();

            return $stmt;
        }

        // UPDATE
        public function updateResource(){
            $sqlQuery = "UPDATE
                        ". $this->db_table ."
                    SET
                        filename = :filename, 
                        type = :type,
                        category = :category,
                        project_id = :project_id
                    WHERE 
                        id = :id";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            $this->filename=htmlspecialchars(strip_tags($this->filename));
            $this->type=htmlspecialchars(strip_tags($this->type));
            $this->category=htmlspecialchars(strip_tags($this->category));
            $this->project_id=htmlspecialchars(strip_tags($this->project_id));
            $this->id=htmlspecialchars(strip_tags($this->id));
        
            // bind data
            $stmt->bindParam(":filename", $this->filename);
            $stmt->bindParam(":type", $this->type);
            $stmt->bindParam(":category", $this->category);
            $stmt->bindParam(":project_id", $this->project_id);
            $stmt->bindParam(":id", $this->id);
        
            if($stmt->execute()){
               return true;
            }
            return false;
        }

        // DELETE
        function deleteResource(){
            $sqlQuery = "DELETE FROM " . $this->db_table . " WHERE id = ?";
            $stmt = $this->conn->prepare($sqlQuery);
        
            $this->id=htmlspecialchars(strip_tags($this->id));
        
            $stmt->bindParam(1, $this->id);
        
            if($stmt->execute()){
                return true;
            }
            return false;
        }

        // Delete all resources for a project
        function deleteResourcesForProject($projectId){
            $sqlQuery = "DELETE FROM " . $this->db_table . " WHERE project_id = ?";
            $stmt = $this->conn->prepare($sqlQuery);
        
            $projectId=htmlspecialchars(strip_tags($projectId));
        
            $stmt->bindParam(1, $projectId);
        
            if($stmt->execute()){
                return true;
            }
            return false;
        }

    }
?>