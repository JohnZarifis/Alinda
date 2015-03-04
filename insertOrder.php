<?php

require_once ("includes/configs.php");
require_once ("includes/session.php");
require_once("includes/funcs.php");
require_once("includes/database.php");

if (!$session->is_logged_in()) { redirect_to("login.php"); }

$sql = "SELECT MAX(ORDERID) FROM ORDERS";
$result_set = $database->query($sql);
$row = mysql_fetch_row($result_set);
$orderid = $row[0] +1;

/// orderid
print $orderid;
print "<hr/>";
$orderdate = date("Y-m-d");
////orderdate
print $orderdate;
print "<hr/>";

//get the hidden posts. userid and date?
$userid = $_POST['hidden_post_'];
print $userid;
print "<hr/>";
$traid = $_POST['hidden_post_2'];
print $traid;
print "<hr/>";
$slmid = $_POST['hidden_post_3'];
print $slmid;
print "<hr/>";
//print_r($_POST);
print "<hr/>";
$processed = 'NO';


$number=1;
$item_name="item_name_".$number;
while (isset($_POST[$item_name]))
{
	$item_quantity="item_quantity_".$number;
	$item_price="item_price_".$number;
	$item_currency="item_currency_".$number;
	$item_tax_rate="item_tax_rate_".$number;
	$item_description="item_description_".$number;
        $item_extras="item_extas_".$number;
	$items[]=array(
		"item_name"=> $_POST[$item_name],
		"item_quantity"=> $_POST[$item_quantity],
		"item_price"=> $_POST[$item_price],
		"item_currency"=> $_POST[$item_currency],
		"item_tax_rate"=> $_POST[$item_tax_rate],
		"item_description"=> $_POST[$item_description],
                "item_extras"=>$_POST[$item_extras]
	);
$number++;
$item_name="item_name_".$number;
}

foreach($items as $item ) {
    
 $description = filter_var($item['item_description'], FILTER_SANITIZE_STRING);
//DO THE SQL QUERY.INSTEAD OF print_r do the sql add entry.
$sqlIns = <<<MARKER
        INSERT INTO ORDERS (orderid, traid, orderdate, slmid, codecode, qtyA, processed) VALUES 
            ({$orderid},{$traid},'{$orderdate}',{$slmid},'{$description}',{$item['item_quantity']},'{$processed}')
MARKER;
print_r ($sqlIns);
print_r ("<hr/>");             
            
$database->query($sqlIns);  
                 
            
print_r ($item);
print_r ("<hr/>");
}

header( 'Location: client.php?traid='.$traid ) ;

//REDIRECT TO INDEX. FOR NOW THIS IS COMENTED OUT FOR TESTS.
//redirect_to("index.php");


?>
