<?php
require_once ("includes/configs.php");
require_once ("includes/session.php");
require_once("includes/funcs.php");
require_once("includes/database.php");

if (!$session->is_logged_in()) { redirect_to("login.php"); }

$username = $_SESSION['user_name'];
$id = $_SESSION['user_id'];
$psw = $_SESSION['user_psw'];
$Supervisor = $_SESSION['user_Supervisor'];
$commonPSW = $_SESSION['user_commonPSW'];
$isAdmin = $_SESSION['user_isAdmin'];
$account = $_SESSION['user_account'];
//$email = $_SESSION['user_email'];
//print_r($_SESSION); //for debugging reasons

if(isset($_GET['orderid'])){
    $orderid =  $_GET['orderid'] ;
}

$from = '01/01/2014';
$to =  'SYSDATE()'; 

$sql = <<< MARKER
SELECT distinct ORDERID,O.TRAID,ORDERDATE,O.SLMID,S.SLMNAME,PROCESSED,LEENAME,LEEAFM,TRACODE,ADRCITY,ADRSTREET, ADRPHONE1,slmEmail FROM ORDERS O
       INNER JOIN SLM S
       ON S.SLMID = O.SLMID      
       INNER JOIN CUSTOMER C
       ON C.TRAID = O.TRAID
       WHERE ORDERID = {$orderid}      
MARKER;

// if($username != 'Admin')
    // {
        // //$traid =  $_GET['traid']; 
// //if(isset($_POST['apo_value']) && isset($_POST['mexri_value'])){
// //$from = $_POST['apo_value'];
// //$to = $_POST['mexri_value'];
// $sql = <<<MARKER
// SELECT distinct ORDERID,O.TRAID,ORDERDATE,O.SLMID,S.SLMNAME,PROCESSED,LEENAME,LEEAFM,TRACODE,ADRCITY,ADRSTREET,ADRPHONE1 FROM ORDERS O
       // INNER JOIN SLM S
       // ON S.SLMID = O.SLMID      
       // INNER JOIN CUSTOMER C
       // ON C.TRAID = O.TRAID
       // WHERE ORDERID = {$orderid}   
// MARKER;
// }   
  
//print_r($sql);
//print_r($_GET['traid']);

$result_set = $database->query($sql);
$MultiDimArray = array();
while ($row = mysql_fetch_assoc($result_set)) 
			{
    
                         $leename = $row['LEENAME'];
                         $leeafm = $row['LEEAFM'];
                         $orderdate =$row['ORDERDATE'];
                         $slmname = $row['SLMNAME'];
                         $processed = $row['PROCESSED'];
						 $tracode = $row['TRACODE'];
						 $adrcity = $row['ADRCITY'];
						 $adrstreet = $row['ADRSTREET'];
						 $adrphone1 = $row['ADRPHONE1'];
						 $slmEmail = $row['slmEmail'];
                         
                         
                         $MultiDimArray[] = array ( 'TRAID' => $row['TRAID'],
                                                    'ORDERDATE' => $row['ORDERDATE'],
                                                    'SLMID'=>$row['SLMID'],
                                                    'SLMNAME'=>$row['SLMNAME'],
                                                    'PROCESSED'=>$row['PROCESSED'],
                                                    'LEENAME'=>$row['LEENAME'],
                                                    'LEEAFM'=>$row['LEEAFM'],
                                                    'ORDERID'=>$row['ORDERID'],
                                                    'TRACODE'=>$row['TRACODE'],
                                                    'ADRCITY'=>$row['ADRCITY'],
                                                    'ADRSTEET'=>$row['ADRSTREET'],
                                                    'ADRPHONE1'=>$row['ADRPHONE1'],
                                                    'SLMEMAIL'=>$row['slmEmail'],
													
                             );
			}

                        
$sqlDetail = <<< MARKER
        SELECT CODCODE,QTYA
        FROM ORDERS 
        WHERE ORDERID = {$orderid}
MARKER;
        
//print_r($sqlDetail);
$resultDetail = $database->query($sqlDetail);
$MultiDetailArray = array();
$tmpCount = 1;
while ($row = mysql_fetch_assoc($resultDetail)) 
			{
                         $MultiDetailArray[] = array ( 'CODCODE' => $row['CODCODE'],
                                                       'QTYA' => $row['QTYA'],
                                                       'rowCount'=>$tmpCount,
                                                    
                             );
					     $tmpCount ++;
			
			}       
  
       
//print_r($MultiDimArray);
 
        //$name = 'John';
        $template = $twig->loadTemplate('invoice.html');  
        echo $template->render(array('username' => $username,                                     
                                      'res'=>$MultiDimArray,
                                      'detail'=>$MultiDetailArray,
                                      'leename'=>$leename,
                                      'leeafm'=>$leeafm,
                                      'orderid'=>$orderid,
                                      'orderdate'=>$orderdate,
                                      'processed'=>$processed,
                                      'slmname' => $slmname,
                                      'slmid' =>$id,
                                      'tracode'=>$tracode,
                                      'adrcity'=>$adrcity,
                                      'adrstreet'=>$adrstreet,
                                      'adrphone1'=>$adrphone1,
                                      'slmemail'=>$slmEmail
            
                                     ));

?>
