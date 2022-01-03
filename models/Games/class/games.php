<?php
    class Game{

        // Connection
        private $conn;

        // Tables
        private $db_table_projects = "projects";
        private $db_table_snapshots = "snapshots";

        // Columns
        public $name;
        public $game_code;
        public $created;
        public $landscapes;
        public $characters;
        public $events;
        public $sprites;

        // Db connection
        public function __construct($db){
            $this->conn = $db;
        }

        // READ published game infos
        public function getPublishedGamesInfos(){
            $sqlQuery = "
                SELECT
                    name, 
                    ". $this->db_table_projects.".hashcode as game_code,
                    ". $this->db_table_snapshots.".created
                FROM
                    ". $this->db_table_projects ."
                    JOIN ". $this->db_table_snapshots ." ON published_snapshot_id = ". $this->db_table_snapshots.".id
                WHERE 
                   status = 'published'
                   AND published_snapshot_id != -1
            ";

            $stmt = $this->conn->prepare($sqlQuery);

            $stmt->execute();

            return $stmt;
        } 

        // READ game infos
        public function getGameInfos(){
            $sqlQuery = "
                    SELECT 
                        name, 
                        ". $this->db_table_snapshots.".created
                    FROM (
                            SELECT 
                                published_snapshot_id as 'snapshot_id' 
                            FROM 
                                ". $this->db_table_projects ." 
                            WHERE hashcode = :hashcode 
                            AND status = 'published' 
                            AND published_snapshot_id != -1 

                            UNION

                            SELECT 
                            id AS 'snapshot_id' 
                            FROM 
                                ". $this->db_table_snapshots ."
                            WHERE hashcode = :hashcode

                            LIMIT 1
                        ) AS t1 
                    JOIN ". $this->db_table_snapshots ." ON ". $this->db_table_snapshots.".id = t1.snapshot_id 
                    JOIN ". $this->db_table_projects ." ON ". $this->db_table_projects.".id = ". $this->db_table_snapshots.".project_id
                    LIMIT 0,1
            ";

            $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $this->game_code=htmlspecialchars(strip_tags($this->game_code));

            // bind data
            $stmt->bindParam(":hashcode", $this->game_code);

            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->name = $dataRow['name'];
            $this->created = $dataRow['created'];
        } 

        // READ by game_code
        public function getGame(){
            $sqlQuery = "
                    SELECT 
                        name, 
                        ". $this->db_table_snapshots.".created,
                        landscapes,
                        characters,
                        events,
                        sprites 
                    FROM (
                            SELECT 
                                published_snapshot_id as 'snapshot_id' 
                            FROM 
                                ". $this->db_table_projects ." 
                            WHERE hashcode = :hashcode 
                            AND status = 'published' 
                            AND published_snapshot_id != -1 

                            UNION

                            SELECT 
                            id AS 'snapshot_id' 
                            FROM 
                                ". $this->db_table_snapshots ."
                            WHERE hashcode = :hashcode

                            LIMIT 1
                        ) AS t1 
                    JOIN ". $this->db_table_snapshots ." ON ". $this->db_table_snapshots.".id = t1.snapshot_id 
                    JOIN ". $this->db_table_projects ." ON ". $this->db_table_projects.".id = ". $this->db_table_snapshots.".project_id
                    LIMIT 0,1
            ";

            $stmt = $this->conn->prepare($sqlQuery);

            // sanitize
            $this->game_code=htmlspecialchars(strip_tags($this->game_code));

            // bind data
            $stmt->bindParam(":hashcode", $this->game_code);

            $stmt->execute();

            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->name = $dataRow['name'];
            $this->created = $dataRow['created'];
            $this->landscapes = $dataRow['landscapes'];
            $this->characters = $dataRow['characters'];
            $this->events = $dataRow['events'];
            $this->sprites = $dataRow['sprites'];
        }

    }
?>