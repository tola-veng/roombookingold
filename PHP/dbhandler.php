<?php
class dbhandler{
	private $connection, $host, $dbName, $user, $pass;
	public $error;
	public $sql;
	public $lastInsertId;

	public function __construct($dbName){
		$this->error = "";
		$this->host = "localhost";
		//$this->dbName = $dbName;
		$this->dbName = "roombooking";
		$this->user = "root";
		$this->pass = "";
		$this->connect();
	} // end construct
	
	public function __destruct(){
		$this->connection = null;
	} // end destruct

	private function connect(){
		try{
			$this->connection = new PDO("mysql:host=".$this->host.";dbname=".$this->dbName,$this->user,$this->pass);
			// set error mode
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
			return true;
		}catch(PDOException $e){
			$this->error = $e->getMessage();
			$this->connection = null;
			return false;
		}
	} // end connect
	
	public function quote($str){
		return $this->connection->quote($str);
	}
	
	/*
		param Sql statement
		return array of data
	*/
	public function selectQuery($sql){
		$this->error = '';
		$data = array();
		$this->sql = $sql;
		// check connection
		if($this->connection==null || $this->connection==false){
			$this->error = 'connection failed';
			return false;
		}
		// query object
		$query = $this->connection->query($this->sql);
		if($query==false){
			// error
			$errInfo = $this->connection->errorInfo();
			$this->error = $errInfo[2];
		}else{			
			while($record = $query->fetch(PDO::FETCH_ASSOC)){
				array_push($data,$record);
			}
		}
		return $data;
	} // end select query
	
	public function insertQuery($sql){
		$this->error = '';
		$this->sql = $sql;
		// check connection
		if($this->connection==null || $this->connection==false){
			$this->error = 'connection failed';
			return false;
		}
		$query = $this->connection->prepare($this->sql);
		$query->execute();
		$errInfo = $query->errorInfo();
		$this->error = $errInfo[2];
		if($this->error==''){
			$this->lastInsertId = $this->connection->lastInsertId();
			return $this->lastInsertId;
		}
		return false;
	}
	
	public function updateQuery($sql){
		$this->error = '';
		$this->sql = $sql;
		// check connection
		if($this->connection==null || $this->connection==false){
			$this->error = 'connection failed';
			return false;
		}
		$query = $this->connection->prepare($this->sql);
		$query->execute();
		$errInfo = $query->errorInfo();
		$this->error = $errInfo[2];
		if($this->error==''){
			return $query->rowCount();
		}
		return false;
	}
	
	public function deleteQuery($sql){
		$this->error = '';
		$this->sql = $sql;
		// check connection
		if($this->connection==null || $this->connection==false){
			$this->error = 'connection failed';
			return false;
		}
		$query = $this->connection->prepare($this->sql);
		$query->execute();
		$errInfo = $query->errorInfo();
		$this->error = $errInfo[2];
		if($this->error==''){
			return $query->rowCount();
		}
		return false;
	}
	
	// Additional function 
	
	

} // end DbHandler
?>