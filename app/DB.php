<?php
    class DB
    {
        private static $queries = [];

        function __call($sql, $args)
        {
            return $this->query(self::$queries[$sql], $args);
        }

        public static function registerQuery($name, $querySpec)
        {
            self::$queries[$name] = $querySpec;
        }
        
        private function Query($sql, $args)
        {
            $pdo = $this->getConnection();

            $stmt = $pdo->prepare($sql);
            
            try 
            {
                if (empty($args))
                {
                    $stmt->execute();
                }
                else
                {
                    $stmt->execute($args[0]);
                }
            }
            catch  (Exception $e) {
                print_r($e);
            }
            
            $records = [];

            while ($row = $stmt->fetch())
            {
                array_push($records, (object)$row);
            }

            $last_id = $pdo->lastInsertId();
            if ($last_id != 0)
            {
                if (empty($records))
                {
                    return ["id"=>$last_id];
                }
                else
                {
                    return [$records, ["id"=>$last_id]];
                }
            }
            return $records;
        }

        private function getConnection()
        {
            $opt = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            
            // TODO this needs to get it's connection in a better way
            $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET;

            return new PDO($dsn, DB_UID, DB_PWD, $opt);
        }
    }
?>