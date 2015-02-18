<?php
require_once("includes/funcs.php");
require_once("includes/session.php");
require_once("includes/database.php");
require_once("includes/user.php");
require_once("includes/configs.php");

if (!$session->is_logged_in()) { redirect_to("login.php"); }
$traid =  $_GET['traid'] ;
$sql = " SELECT DISTINCT LEENAME,LEEAFM FROM TRN
         WHERE TRAID = {$traid}";
$username = $_SESSION['user_name'];
$id = $_SESSION['user_id'];
$result_set = $database->query($sql);
while ($row = mysql_fetch_assoc($result_set)) 
			{
                         $leename = $row['LEENAME'];
                         $leeafm = $row['LEEAFM'];
			}



// Remember to give your form's submit tag a name="submit" attribute!
if (isset($_POST['submit'])) {} // Form has been submitted.

  

  $template = $twig->loadTemplate('order.html');
  echo $template->render(array('username' => $username,
                               'leename'=>$leename,
                               'leeafm'=>$leeafm));
  
//$name = 'John';
//$template = $twig->loadTemplate('login.html');
//echo $template->render(array('name' => $name,));

 //elseif (!isset($_POST['submit'])) {
    
//}


?>