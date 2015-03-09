<?php
require_once("configs.php");



class OracleDatabase{
	
	 private $connection;
         function __construct(){
		$this->open_connection();
		
	}
	//$c = oci_pconnect('S01002', 'S01002', '//alindaserver:1521/SEN');
	 public function open_connection(){
		$this->connection = oci_pconnect(ORADB_USER,ORADB_PASS,ORADB_NAME,"UTF8");
		if(!$this->connection){
                    $m = oci_error();
		    die("Database connection failed: ".$m['message']);
		} 
	 }
         
	 public function close_connection(){
		if(isset($this->connection)){
			oci_close($this->connection);
		}
	}
        
	 public function query($sql){
              set_time_limit(0);
              $stid = oci_parse($this->connection, $sql);
              $result = oci_execute($stid);
		if(!$result){
                        $m = oci_error();
			die("Database query failed: ".$m['message']);
			
		}
		return $stid;
	 }
	 

	 //database neutral methods
	 public function fetch_array($result_set){
	 	return oci_fetch_array($result_set);
	 }
	 
	 
	 
         public function test_input($data)
            {
              $data = trim($data);//i have names with spaces for now.
              $data = stripslashes($data);
              $data = htmlspecialchars($data);
              return $data;
            }

}

//$Oradatabase = new OracleDatabase();


class MySQLDatabase{
	
	 private $connection;
         function __construct(){
		$this->open_connection();
		
	}
	
	 public function open_connection(){
		$this->connection = mysql_connect(DB_SERVER,DB_USER,DB_PASS);
		if(!$this->connection){
			die("Database connection failed: ".mysql_error());
		} else{
		$db_select = mysql_select_db(DB_NAME,$this->connection);
		if(!$db_select){
			die("Database selection failed: ".mysql_error());
					}
	
	 
	          }
	 }
         
	 public function close_connection(){
		if(isset($this->connection)){
			mysql_close($this->connection);
		}
	}
        
	 public function query($sql){
              mysql_set_charset('utf8',$this->connection);
	 	$result=mysql_query($sql,$this->connection);
		if(!$result){
			die("Database query failed: ".mysql_error());
			
		}
		return $result;
	 }
	 

	 //database neutral methods
	 public function fetch_array($result_set){
	 	return mysql_fetch_array($result_set);
	 }
         
         public function mysql_prep( $value ) {
	$magic_quotes_active = get_magic_quotes_gpc();
	$new_enough_php = function_exists( "mysql_real_escape_string" ); // i.e. PHP >= v4.3.0
	if( $new_enough_php ) { // PHP v4.3.0 or higher
		// undo any magic quote effects so mysql_real_escape_string can do the work
		if( $magic_quotes_active ) { $value = stripslashes( $value ); }
		$value = mysql_real_escape_string( $value );
	} else { // before PHP v4.3.0
		// if magic quotes aren't already on then add slashes manually
		if( !$magic_quotes_active ) { $value = addslashes( $value ); }
		// if magic quotes are active, then the slashes already exist
	}
	return $value;
         }
	 
	 
         public function test_input($data)
            {
              $data = trim($data);//i have names with spaces for now.
              $data = stripslashes($data);
              $data = htmlspecialchars($data);
              return $data;
            }

}

$database = new MySQLDatabase();
?>








