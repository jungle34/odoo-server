<?php 
    class Database {
        private $user ;
        private $host;
        private $pass ;
        private $db;

        public function __construct() {
            $this->user = DB_USER;
            $this->host = DB_HOST;
            $this->pass = DB_PASS;
            $this->db = DB_BASE;
        }

        public function connect() {
            try {
                $db = new PDO('mysql:host='.$this->host.';dbname='.$this->db, $this->user, $this->pass);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false );
            } catch ( PDOException $Exception ) {
                print_r($Exception);
                die('Erro ao conectar na base de dados');
            }
            return $db;
        }
        /* limpa procedures executadas */
		public function clearStoredResults($db) {
			while($db->next_result()){
				if($l_result = $db->store_result()) {
					$l_result->free();
				}
			}
		}
    }
?>