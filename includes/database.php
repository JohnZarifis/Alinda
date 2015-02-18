<?php
require_once("configs.php");



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
$db =& $database;

?>






<?php

//// Create connection to Oracle
//$conn = oci_connect("phphol", "welcome", "//localhost/orcl");
//
//$query = 'select * from departments';
//$stid = oci_parse($conn, $query);
//$r = oci_execute($stid);
//
//// Fetch each row in an associative array
//print '<table border="1">';
//while ($row = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC)) {
//   print '<tr>';
//   foreach ($row as $item) {
//       print '<td>'.($item !== null ? htmlentities($item, ENT_QUOTES) : '&nbsp').'</td>';
//   }
//   print '</tr>';
//}
//print '</table>';

?>

