<?php
require_once ("includes/configs.php");
require_once ("includes/session.php");
require_once("includes/funcs.php");
require_once("includes/database.php");

if (!$session->is_logged_in()) { redirect_to("login.php"); }

$username = $_SESSION['user_name'];
$id = $_SESSION['user_id'];

if (isset($_POST['orderid'])){

// update  order status
 
$query= "UPDATE ORDERS SET PROCESSED = 'YES' WHERE  orderid = '$_POST[orderid]'"; 

$database -> query($query);

}
if (isset($_GET['orderid'])){

// update  order status
 
$query= "UPDATE ORDERS SET PROCESSED = 'YES' WHERE  orderid = '$_GET[orderid]'"; 

$database -> query($query);

}

header( 'Location: orderList.php' ) ;
