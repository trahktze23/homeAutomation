<?php
class database
{

	private $servername = "localhost";
	private $username = "test";
	private $password = "1234";
	private $dbname= "temperatura";

	private $conn;  // conexiune actuala
	public  $conn_status;  // status conexiune true= conectat la baza de date
	public  $error;  // mesaj de eroare setat in cazul unei erori mysql
	public $query='';

public function __construct()// constructorul clasei realizeaza conexiunea la baza de date


{
		try {
			$this->conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);// creaza un obiect de tip PDO si o conexiune la baza de date
			$this->conn-> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->conn_status=true;
 			//echo('succsses');
		}
		catch(PDOException $e)
		{
			$this->error = $e->getMessage();
			$this->conn_status=false;
		}
}
public function disconnect()//deconectare la baza de date
{
	$this->conn=NULL;
	$this->conn_status=false;
}

public function insert($table , $param )// 1 nume tabel si 2 variabila coloana(array)
{
//$param = associative array ( 'column => value)
//	$sql= 'INSERT INTO setari (id_set, owner) VALUE (5, "tralal")';
// de ex $param =('id_set'=>5;'owner'=>'trelal');

		// if table exist
		$keys_arr = array_keys($param);
		$val_arr = array_values($param);
		$sql = "INSERT INTO ".$table;

		$keys=" (";
		$vals=" VALUES (";

		for ($i=0 ; $i<sizeof($param);$i++)
		{
			$key= $keys_arr[$i];
			$val = $val_arr[$i];

			if ($i< (sizeof($param)-1 ))
			{

				$keys= $keys.$key." ,";

				if (is_int($val))
					$vals=$vals.$val." , ";
				if (is_float($val))
					$vals=$vals.$val." , ";
				if (is_string($val))
					$vals=$vals." '".$val."' , ";
			}
			if ($i== ( sizeof($param)-1 ) )
			{
				$keys= $keys.$key." )";
				if (is_int($val))
					$vals=$vals.$val." )";
				if (is_float($val))
					$vals=$vals.$val." )";
				if (is_string($val))
					$vals=$vals." '".$val."' )";
			}
		}
		$sql= $sql.$keys.$vals;//punct inseamna concatenare
		//echo $sql;
		$this->query=$sql;
		try {// incearca sa scrie in baza de date
			$this->conn->exec($sql);
			return true;
			}
		catch(PDOException $e)
			{
				$this->error = $e->getMessage();
				return false;
			}

	}
	function delete($table,$column, $value)
	{
		if (is_string($value))
			$sql = "DELETE FROM ". $table ." WHERE ". $column ." = '". $value."'";
		if (is_int($value))
			$sql = "DELETE FROM ". $table ." WHERE ". $column ." = ". $value;
		//echo $sql."<br>";
		$this->query=$sql;
		try{
				$this->conn->exec($sql);
				return true;
			}
		catch(PDOException $e)
			{
				$this->error = $e->getMessage();
				return false;
			}
	}


	public function update ($table, $param, $column, $value)
	{   // update $param in the $table table where coumn $coumn has $value value
		//$param = associative array ( 'column => value' )

		$keys_arr = array_keys($param);
		$val_arr = array_values($param);

		$sql= "UPDATE ". $table." SET ";
		$str="";
		for ($i = 0; $i< sizeof($param); $i++)
		{
			$key= $keys_arr[$i];
			$val = $val_arr[$i];

			$str = $str.$key."= ";

			if ($i< (sizeof($param)-1) )
			{
				if (is_string($val))
					{
					$str = $str. "'".$val."' , ";
					}
				if (is_float($val))
					{
					$str = $str. $val." , ";
					}
				if (is_int($val))
					{
					$str = $str. $val." , ";
					}
			}
			if ($i== (sizeof($param)-1) )
			{
				if (is_string($val))
				{
					$str = $str. "'".$val."' ";
				}
				if (is_float($val))
				{
					$str = $str. $val;
				}
				if (is_int($val))
				{
					$str = $str. $val;
				}
			}

		}

		$sql= $sql.$str." WHERE ".$column."=";
		$sql= (is_int($value)) ?  $sql.$value : $sql."'". $value."' ";
//echo $sql."<br>";
		$this->query=$sql;
		try{
			$this->conn->exec($sql);
			return true;
		}
		catch(PDOException $e)
		{
			$this->error = $e->getMessage();
			return false;
		}


	}

	public function select($table="", $column="* " , $query=NULL)//selecteaza in baza de date
	{ // query is for more complex function of seletction
		if ($query==NULL)
		{
			$sql= " SELECT ". $column." FROM ".$table;

		}
		else
		{$sql = $query;}

		//echo $sql;
		$this->query=$sql;
		try{
			$res= $this->conn->query( $sql );   //
			return $res;
		}
		catch(PDOException $e)
		{
			$this->error = $e->getMessage();
			return false;
		}


	}




		public function select_v2($table="", $column="* " , $query=NULL)//selecteaza in baza de date
	{ // query is for more complex function of seletction
		if ($query==NULL)
		{
			$sql= " SELECT ". $column." FROM ".$table;

		}
		else
		{$sql = $query;}

		//echo $sql;
		$this->query=$sql;
		$data = array();
		try{
// 			$res= $this->conn->query( $sql );   //
// 			print_r($res);
// 			return $res;

			foreach($this->conn->query( $sql ) as $row)
			{
				array_push($data, $row);
			}
			return $data;

		}
		catch(PDOException $e)
		{
			$this->error = $e->getMessage();
			return false;
		}


	}


// 	public function set_dsn($databaseName='db_contacts', $host='localhost', $user='newuser', $password='')
// 	{
// 		$this->dbname     = $databaseName;
// 		$this->servername = $host;
// 		$this->username   = $user;
// 		$this->password   = $password;


// 	}

}

// 	}
