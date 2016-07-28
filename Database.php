<?php
class Database {

    private $host;
    private $username;
    private $password;
    private $database;

    private $pdo;
    private $stmt;

	private static $instance;
	
    private function __construct($host,$user,$pass,$db) {
        $this->host = $host;
        $this->username = $user;
        $this->password = $pass;
        $this->database = $db;

        $dsn = 'mysql:dbname=' . $this->database .';host=' . $this->host;

        try {

            $this->pdo = new PDO($dsn, $this->username, $this->password);

        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }
	
	public static function getInstance(){
		if (!self::$instance){
			self::$instance = new Database(Flux::config('ServerAddress'),
											Flux::config('DatabaseUser'),
											Flux::config('DatabasePassword'),
											Flux::config('DatabaseName')
										);
		} 
		return self::$instance;
	}

    public function query($query){
        $this->stmt = $this->pdo->prepare($query);
        return $this;
    }

    public function execute($values = null){
		if (empty($values)) { 
			$this->stmt->execute(); 
		} else {
			$this->stmt->execute($values); 
		}
		return $this; 
    }
	
	public function getResults(){
		return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function rowCount(){
		return $this->stmt->rowCount();
	}
	
	public function debugDumpParams(){
		return $this->stmt->debugDumpParams();
	}

}

/*
	usage:
	$db = Database::getInstance();
	$db->query( INSERT QUERY STRING HERE ) parameterized query here
		->execute( dito mo bind ung parameters mo. )
		->getResults() associative array na matik to
		->rowCount() how many rows ung result ng query .
*/
