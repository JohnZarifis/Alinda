<?php
require_once ("database.php");
class User{
	
	public $SLMID;
	public $SLMNAME;
	//public $SLMISSALESMAN;
        //public $SLMISCOLLECTOR;
        //public $SLMISAGENT;
        //public $SLMACTIVE;
        //public $SLMCODE;
        //public $VERID;  
        public $PSW;
        public $USERNAME;
        	
	
	
	public static function find_all() {
		return self::find_by_sql("SELECT * FROM SLM");
  }
  
  	public static function find_by_id($id=0) {
    $result_array = self::find_by_sql("SELECT * WHERE SLMID={$id}");
		return !empty($result_array) ? array_shift($result_array) : false;
  }
  
	public static function find_by_sql($sql="") {
         global $database;
         $result_set = $database->query($sql);
         $object_array = array();
         while ($row = $database->fetch_array($result_set)) {
         $object_array[] = self::instantiate($row);
         }
         return $object_array;
      }
	
	
		private static function instantiate($record) {
		// We  check that $record exists and is an array with has_attribute
    // Simple, long-form approach:
		$object = new self; // else can use new User
		// $object->id 				= $record['id'];
		// $object->username 	= $record['username'];
		// $object->password 	= $record['password'];
		// $object->first_name = $record['first_name'];
		// $object->last_name 	= $record['last_name'];
		
		// More dynamic, short-form approach:
		foreach($record as $attribute=>$value){
		  if($object->has_attribute($attribute)) {
		    $object->$attribute = $value;
		  }
		}
		return $object;
	}
	
	private function has_attribute($attribute) {
	  // get_object_vars returns an associative array with all attributes 
	  // (incl. private ones!) as the keys and their current values as the value
	  $object_vars = get_object_vars($this);
	  // We don't care about the value, we just want to know if the key exists
	  // Will return true or false
	  return array_key_exists($attribute, $object_vars);
	}
	
	
	public static function authenticate($username="", $password="") {
            global $database;
            $username = $database->mysql_prep($username);
            $password = $database->mysql_prep($password);

            $sql  = "SELECT SLMID, SLMNAME,PSW,USERNAME FROM SLM ";
            $sql .= " WHERE USERNAME = '{$username}' ";
            $sql .= " AND slmid = '{$password}' ";
//print_r ($sql);
//exit();
            $result_array = self::find_by_sql($sql);
                        return !empty($result_array) ? array_shift($result_array) : false;
            //return $result_array;
	}
}
    
?>