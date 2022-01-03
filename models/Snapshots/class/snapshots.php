<?php
    class Snapshot{

        // Connection
        private $conn;

        // Table
        private $db_table = "snapshots";

        // Columns
        public $id;
        public $save_name;
        public $hashcode;
        public $project_id;
        public $landscapes;
        public $characters;
        public $events;
        public $sprites;
        public $created;
        public $prod;

        // Db connection
        public function __construct($db){
            $this->conn = $db;
        }

        // GET ALL
        public function getSnapshots(){
            $sqlQuery = "SELECT id, save_name, hashcode, project_id, landscapes, characters, events, sprites, created, prod FROM " . $this->db_table . "";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            return $stmt;
        }

        // CREATE
        public function createSnapshot(){
            $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                    SET
                        save_name = :save_name, 
                        hashcode = :hashcode,
                        project_id = :project_id,
                        landscapes = :landscapes,
                        characters = :characters,
                        events = :events,
                        sprites = :sprites,
                        created = :created,
                        prod = :prod";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->save_name=htmlspecialchars(strip_tags($this->save_name));
            $this->hashcode=htmlspecialchars(strip_tags($this->hashcode));
            $this->project_id=htmlspecialchars(strip_tags($this->project_id));
            //$this->landscapes=htmlspecialchars(strip_tags($this->landscapes));
            //$this->characters=htmlspecialchars(strip_tags($this->characters));
            //$this->events=htmlspecialchars(strip_tags($this->events));
            $this->created=htmlspecialchars(strip_tags($this->created));
            $this->prod=htmlspecialchars(strip_tags($this->prod));
        
            // bind data
            $stmt->bindParam(":save_name", $this->save_name);
            $stmt->bindParam(":hashcode", $this->hashcode);
            $stmt->bindParam(":project_id", $this->project_id);
            $stmt->bindParam(":landscapes", $this->landscapes);
            $stmt->bindParam(":characters", $this->characters);
            $stmt->bindParam(":events", $this->events);
            $stmt->bindParam(":sprites", $this->sprites);
            $stmt->bindParam(":created", $this->created);
            $stmt->bindParam(":prod", $this->prod);

            if($stmt->execute()){
                $id = $this->conn->lastInsertId();
                $this->id = $id;
                return true;
            }
            return false;
        }   

        // READ by id
        public function getSnapshotById(){
            $sqlQuery = "SELECT
                        id, 
                        save_name, 
                        hashcode, 
                        project_id, 
                        landscapes, 
                        characters, 
                        events,
                        sprites,
                        created,
                        prod
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
            $this->save_name = $dataRow['save_name'];
            $this->hashcode = $dataRow['hashcode'];
            $this->project_id = $dataRow['project_id'];
            $this->landscapes = $dataRow['landscapes'];
            $this->characters = $dataRow['characters'];
            $this->events = $dataRow['events'];
            $this->sprites = $dataRow['sprites'];
            $this->created = $dataRow['created'];
            $this->prod = $dataRow['prod'];
        }    

        // READ by hashcode
        public function getSnapshotByHashcode($hashcode){
            $sqlQuery = "SELECT
                        id, 
                        save_name, 
                        hashcode, 
                        project_id, 
                        landscapes, 
                        characters, 
                        events,
                        sprites,
                        created,
                        prod
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
            $this->save_name = $dataRow['save_name'];
            $this->hashcode = $dataRow['hashcode'];
            $this->project_id = $dataRow['project_id'];
            $this->landscapes = $dataRow['landscapes'];
            $this->characters = $dataRow['characters'];
            $this->events = $dataRow['events'];
            $this->sprites = $dataRow['sprites'];
            $this->created = $dataRow['created'];
            $this->prod = $dataRow['prod'];
        }

        // READ infos by hashcode
        /*public function getPublishedSnapshotsInfos(){
            $sqlQuery = "
                SELECT
                    save_name, 
                    hashcode, 
                    created
                FROM
                    ". $this->db_table ." 
                WHERE 
                   status = 'published'";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();

            return $stmt;
        }*/ 

        // READ infos by hashcode
        public function getSnapshotInfos($hashcode){
            $sqlQuery = "
                SELECT
                    id, 
                    project_id,
                    save_name, 
                    hashcode, 
                    created,
                    prod
                FROM
                    ". $this->db_table ." 
                WHERE 
                    hashcode = ?";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->bindParam(1, $hashcode);

            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id = $dataRow['id'];
            $this->project_id = $dataRow['project_id'];
            $this->save_name = $dataRow['save_name'];
            $this->hashcode = $dataRow['hashcode'];
            $this->created = $dataRow['created'];
            $this->prod = $dataRow['prod'];
        } 

        // READ all for a user
        /*public function getSnapshotsForUser($userId){
            $sqlQuery = "
                SELECT
                    id, 
                    save_name, 
                    hashcode, 
                    created,
                    status
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

        // READ all snapshots infos for a project
        public function getInfosForProject($projectId){
            $sqlQuery = "
                SELECT
                    id, 
                    save_name, 
                    hashcode, 
                    created,
                    project_id,
                    prod
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
        public function updateSnapshot(){
            $sqlQuery = "UPDATE
                        ". $this->db_table ."
                    SET
                        save_name = :save_name, 
                        hashcode = :hashcode,
                        project_id = :project_id,
                        landscapes = :landscapes,
                        characters = :characters,
                        events = :events,
                        sprites = :sprites,
                        created = :created,
                        prod = :prod
                    WHERE 
                        id = :id";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            $this->save_name=htmlspecialchars(strip_tags($this->save_name));
            $this->hashcode=htmlspecialchars(strip_tags($this->hashcode));
            $this->project_id=htmlspecialchars(strip_tags($this->project_id));
            //$this->landscapes=htmlspecialchars(strip_tags($this->landscapes));
            //$this->characters=htmlspecialchars(strip_tags($this->characters));
            //$this->events=htmlspecialchars(strip_tags($this->events));
            //$this->sprites=htmlspecialchars(strip_tags($this->sprites));
            $this->created=htmlspecialchars(strip_tags($this->created));
            $this->prod=htmlspecialchars(strip_tags($this->prod));
            $this->id=htmlspecialchars(strip_tags($this->id));
        
            // bind data
            $stmt->bindParam(":save_name", $this->save_name);
            $stmt->bindParam(":hashcode", $this->hashcode);
            $stmt->bindParam(":project_id", $this->project_id);
            $stmt->bindParam(":landscapes", $this->landscapes);
            $stmt->bindParam(":characters", $this->characters);
            $stmt->bindParam(":events", $this->events);
            $stmt->bindParam(":sprites", $this->sprites);
            $stmt->bindParam(":created", $this->created);
            $stmt->bindParam(":prod", $this->prod);
            $stmt->bindParam(":id", $this->id);
        
            if($stmt->execute()){
               return true;
            }
            return false;
        }

        // DELETE
        function deleteSnapshot(){
            $sqlQuery = "DELETE FROM " . $this->db_table . " WHERE id = ?";
            $stmt = $this->conn->prepare($sqlQuery);
        
            $this->id=htmlspecialchars(strip_tags($this->id));
        
            $stmt->bindParam(1, $this->id);
        
            if($stmt->execute()){
                return true;
            }
            return false;
        }

        // DELETE all snapshots for project
        function deleteSnapshotsForProject($projectId){
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