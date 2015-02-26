<?php
require_once ("includes/configs.php");
require_once ("includes/session.php");
require_once("includes/funcs.php");
require_once("includes/database.php");

if (!$session->is_logged_in()) { redirect_to("login.php"); }

$username = $_SESSION['user_name'];
$id = $_SESSION['user_id'];
//print_r($_SESSION); //for debugging reasons

if(isset($_GET['orderid'])){
    $orderid =  $_GET['orderid'] ;
}

$from = '01/01/2014';
$to =  'SYSDATE()'; 

$sql = <<< MARKER
SELECT distinct ORDERID,O.TRAID,ORDERDATE,O.SLMID,S.SLMNAME,PROCESSED,LEENAME,LEEAFM FROM ORDERS O
       INNER JOIN SLM S
       ON S.SLMID = O.SLMID      
       INNER JOIN 
       (SELECT DISTINCT SLMID,TRAID,LEENAME,LEEAFM FROM TRN) T
                ON T.TRAID = O.TRAID
        WHERE ORDERID = {$orderid}      
MARKER;

if($username != 'Admin')
    {
        //$traid =  $_GET['traid']; 
//if(isset($_POST['apo_value']) && isset($_POST['mexri_value'])){
//$from = $_POST['apo_value'];
//$to = $_POST['mexri_value'];
$sql = <<<MARKER
SELECT distinct ORDERID,O.TRAID,ORDERDATE,O.SLMID,S.SLMNAME,PROCESSED,LEENAME,LEEAFM FROM ORDERS O
       INNER JOIN SLM S
       ON S.SLMID = O.SLMID      
       INNER JOIN 
       (SELECT DISTINCT SLMID,TRAID,LEENAME,LEEAFM FROM TRN) T
                ON T.TRAID = O.TRAID              
        WHERE ORDERID = {$orderid}
MARKER;
}   
  
//print_r($sql);
//print_r($_GET['traid']);

$result_set = $database->query($sql);
$MultiDimArray = array();
while ($row = mysql_fetch_assoc($result_set)) 
			{
    
                         $leename = $row['LEENAME'];
                         $leeafm = $row['LEEAFM'];
                         $orderdate =$row['ORDERDATE'];
                         $slmaname = $row['SLMNAME'];
                         $processed = $row['PROCESSED'];
                         
                         
                         $MultiDimArray[] = array ( 'TRAID' => $row['TRAID'],
                                                    'ORDERDATE' => $row['ORDERDATE'],
                                                    'SLMID'=>$row['SLMID'],
                                                    'SLMNAME'=>$row['SLMNAME'],
                                                    'PROCESSED'=>$row['PROCESSED'],
                                                    'LEENAME'=>$row['LEENAME'],
                                                    'LEEAFM'=>$row['LEEAFM'],
                                                    'ORDERID'=>$row['ORDERID'],
                             );
			}

                        
$sqlDetail = <<< MARKER
        SELECT CODECODE,QTYA
        FROM ORDERS 
        WHERE ORDERID = {$orderid}
MARKER;
        
//print_r($sqlDetail);
$resultDetail = $database->query($sqlDetail);
$MultiDetailArray = array();
while ($row = mysql_fetch_assoc($resultDetail)) 
			{
                         $MultiDetailArray[] = array ( 'CODECODE' => $row['CODECODE'],
                                                    'QTYA' => $row['QTYA'],
                                                    
                             );
			}       
  
       
//print_r($MultiDimArray);
 
        //$name = 'John';
        $template = $twig->loadTemplate('orderDetail.html');  
        echo $template->render(array('username' => $username,                                     
                                      'res'=>$MultiDimArray,
                                      'detail'=>$MultiDetailArray,
                                      'leename'=>$leename,
                                      'leeafm'=>$leeafm,
                                      'orderid'=>$orderid,
                                      'orderdate'=>$orderdate,
                                      'processed'=>$processed,
                                      'slmname' => $slmaname,
                                      'slmid' =>$id,
            
                                     ));

?>
