<?php
require_once ("includes/configs.php");
require_once ("includes/session.php");
require_once("includes/funcs.php");
require_once("includes/database.php");
require_once("includes/user.php");



if (!$session->is_logged_in()) { echo "not Loged in"; }

//$username = $_SESSION['user_name'];
//$id = $_SESSION['user_id'];
//$psw = $_SESSION['user_psw'];



$password = 9031;
$username = 'ΠΑΥΛΟΥ';
echo $password;
echo "<br>";
echo $username;
  // Check database to see if username/password exist.
$found_user = User::authenticate($username, $password);
echo "<br>";
print_r($found_user);
//echo $found_user;
echo "<br>";
echo $found_user -> SLMID;
$session->login($found_user);
echo $session-> user_id;
?>