<?php

require_once ("includes/configs.php");
require_once ("includes/session.php");
require_once("includes/funcs.php");
require_once("includes/database.php");

if (!$session->is_logged_in()) { redirect_to("login.php"); }

//get the hidden posts. userid and date?
$userid = $_POST['hidden_post_'];
print $userid;
print "<hr/>";
$date = $_POST['hidden_post_2'];
print $date;
print "<hr/>";
$test = $_POST['hidden_post_3'];
print $test;
print "<hr/>";
print_r($_POST);
print "<hr/>";


$number=1;
$item_name="item_name_".$number;
while (isset($_POST[$item_name]))
{
	$item_quantity="item_quantity_".$number;
	$item_price="item_price_".$number;
	$item_currency="item_currency_".$number;
	$item_tax_rate="item_tax_rate_".$number;
	$item_description="item_description_".$number;
	$items[]=array(
		"item_name"=> $_POST[$item_name],
		"item_quantity"=> $_POST[$item_quantity],
		"item_price"=> $_POST[$item_price],
		"item_currency"=> $_POST[$item_currency],
		"item_tax_rate"=> $_POST[$item_tax_rate],
		"item_description"=> $_POST[$item_description]
	);
$number++;
$item_name="item_name_".$number;
}

foreach($items as $item ) {
//DO THE SQL QUERY.INSTEAD OF print_r do the sql add entry.
print_r ($item);
print_r ("<hr/>");
}

//REDIRECT TO INDEX. FOR NOW THIS IS COMENTED OUT FOR TESTS.
//redirect_to("index.php");


?>
