<?php
    class Project{

        // Connection
        private $conn;

        // Tables
        private $db_table = "projects";
        private $db_table_join = "users_projects";
        private $db_table_users = "users";

        // Columns
        public $id;
        public $name;
        public $hashcode;
        public $created;
        public $status;
        public $published_snapshot_id;

        // Db connection
        public function __construct($db){
            $this->conn = $db;
        }

        // GET ALL
        public function getProjects(){
            $sqlQuery = "SELECT id, name, hashcode, created, status, published_snapshot_id FROM " . $this->db_table . "";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            return $stmt;
        }

        // CREATE
        public function createProject(){
            $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                    SET
                        name = :name, 
                        hashcode = :hashcode,
                        created = :created,
                        status = :status,
                        published_snapshot_id = :published_snapshot_id";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->name=htmlspecialchars(strip_tags($this->name));
            $this->hashcode=htmlspecialchars(strip_tags($this->hashcode));
            $this->created=htmlspecialchars(strip_tags($this->created));
            $this->status=htmlspecialchars(strip_tags($this->status));
            $this->published_snapshot_id=htmlspecialchars(strip_tags($this->published_snapshot_id));

            // bind data
            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":hashcode", $this->hashcode);
            $stmt->bindParam(":created", $this->created);
            $stmt->bindParam(":status", $this->status);
            $stmt->bindParam(":published_snapshot_id", $this->published_snapshot_id);

            if($stmt->execute()){
                $id = $this->conn->lastInsertId();
                $this->id = $id;
                return true;
            }
            return false;
        }   

        // READ by id
        public function getProjectById(){
            $sqlQuery = "SELECT
                        id, 
                        name, 
                        hashcode,
                        created,
                        status,
                        published_snapshot_id
                      FROM
                        ". $this->db_table ."
                    WHERE 
                       id = ?
                    LIMIT 0,1";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $this->id);

            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id = $dataRow['id'];
            $this->name = $dataRow['name'];
            $this->hashcode = $dataRow['hashcode'];
            $this->created = $dataRow['created'];
            $this->status = $dataRow['status'];
            $this->published_snapshot_id = $dataRow['published_snapshot_id'];
        }    

        // READ by hashcode
        public function getProjectByHashcode($hashcode){
            $sqlQuery = "SELECT
                        id, 
                        name, 
                        hashcode,
                        created,
                        status,
                        published_snapshot_id
                      FROM
                        ". $this->db_table ."
                    WHERE 
                       hashcode = ?
                    LIMIT 0,1";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $hashcode);

            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id = $dataRow['id'];
            $this->name = $dataRow['name'];
            $this->hashcode = $dataRow['hashcode'];
            $this->created = $dataRow['created'];
            $this->status = $dataRow['status'];
            $this->published_snapshot_id = $dataRow['published_snapshot_id'];
        }

        // READ published projects
        /*public function getPublishedProjectsInfos(){
            $sqlQuery = "
                SELECT
                    name, 
                    hashcode, 
                    created
                FROM
                    ". $this->db_table ." 
                WHERE 
                   status = 'published'";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();

            return $stmt;
        } */

        // READ all for a user
        public function getProjectsForUser($userId){
            $sqlQuery = "
                SELECT 
                    a.id, 
                    a.name, 
                    a.hashcode, 
                    a.created,
                    a.status,
                    a.published_snapshot_id
                FROM 
                    ". $this->db_table ." as a 
                join 
                    ". $this->db_table_join ." as b 
                on 
                    a.id = b.project_id 
                join 
                    ". $this->db_table_users ." as c 
                on 
                    c.id = b.user_id 
                where 
                    c.id = ?
                group by 
                    a.id";

            $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $userId=htmlspecialchars(strip_tags($userId));
        
            // bind data
            $stmt->bindParam(1, $userId);

            $stmt->execute();

            return $stmt;
        }      

        // UPDATE
        public function updateProject(){
            $sqlQuery = "UPDATE
                        ". $this->db_table ."
                    SET
                        name = :name, 
                        hashcode = :hashcode,
                        created = :created,
                        status = :status,
                        published_snapshot_id = :published_snapshot_id
                    WHERE 
                        id = :id";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            $this->name=htmlspecialchars(strip_tags($this->name));
            $this->hashcode=htmlspecialchars(strip_tags($this->hashcode));
            $this->created=htmlspecialchars(strip_tags($this->created));
            $this->status=htmlspecialchars(strip_tags($this->status));
            $this->id=htmlspecialchars(strip_tags($this->id));
            $this->published_snapshot_id=htmlspecialchars(strip_tags($this->published_snapshot_id));
        
            // bind data
            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":hashcode", $this->hashcode);
            $stmt->bindParam(":created", $this->created);
            $stmt->bindParam(":status", $this->status);
            $stmt->bindParam(":id", $this->id);
            $stmt->bindParam(":published_snapshot_id", $this->published_snapshot_id);
        
            if($stmt->execute()){
               return true;
            }
            return false;
        }

        // DELETE
        public function deleteProject(){
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