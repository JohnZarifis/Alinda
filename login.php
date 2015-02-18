<?php
require_once("includes/funcs.php");
require_once("includes/session.php");
require_once("includes/database.php");
require_once("includes/user.php");
require_once("includes/configs.php");

if($session->is_logged_in()) {
  redirect_to("index.php");
}
 $message= "";
// Remember to give your form's submit tag a name="submit" attribute!
if (isset($_POST['submit'])) { // Form has been submitted.

  //$username = $_POST['username'];
 
  $password = trim($_POST['password']);
  $username = trim($_POST['username']);
  // Check database to see if username/password exist.
	$found_user = User::authenticate($username, $password);
//print_r($found_user);
	
  if ($found_user) {
    $session->login($found_user);
    //print_r($found_user); // for debugging reasons
    redirect_to("index.php");
  } else {
    // username/password combo was not found in the database
    $message = "Ο συνδυασμός Username/password δεν είναι σωστός.";
    $template = $twig->loadTemplate('login.html');
    echo $template->render(array('message' => $message,));
  }
  
} else { // Form has not been submitted.
  $username = "";
  $password = "";
  $message ="";
  $template = $twig->loadTemplate('login.html');
  echo $template->render(array('message' => $message,));
  
//$name = 'John';
//$template = $twig->loadTemplate('login.html');
//echo $template->render(array('name' => $name,));
}
 //elseif (!isset($_POST['submit'])) {
    
//}


?>
        
