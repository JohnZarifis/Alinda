<?php
require_once ("includes/configs.php");
require_once ("includes/session.php");
require_once("includes/funcs.php");
require_once("includes/database.php");

if (!$session->is_logged_in()) { redirect_to("login.php"); }

$username = $_SESSION['user_name'];
$id = $_SESSION['user_id'];
//print_r($_SESSION); //for debugging reasons

$from = '01/01/2014';
$to =  'SYSDATE()'; 

$sql = <<< MARKER
SELECT distinct ORDERID,O.TRAID,ORDERDATE,O.SLMID,S.SLMNAME,PROCESSED,ΕΠΩΝΥΜΙΑ_ΠΕΛΑΤΗ,ΑΦΜ_ΣΥΝΑΛΛΑΣΣΟΜΕΝΟΥ FROM ORDERS O
       INNER JOIN SLM S
       ON S.SLMID = O.SLMID      
       INNER JOIN CUSTOMER C
       ON C.TRAID = O.TRAID
       
       WHERE PROCESSED = 'NO'
                
MARKER;

if($username != 'Admin')
    {
        //$traid =  $_GET['traid']; 
//if(isset($_POST['apo_value']) && isset($_POST['mexri_value'])){
//$from = $_POST['apo_value'];
//$to = $_POST['mexri_value'];
$sql = <<<MARKER
SELECT distinct ORDERID,O.TRAID,ORDERDATE,O.SLMID,S.SLMNAME,PROCESSED,ΕΠΩΝΥΜΙΑ_ΠΕΛΑΤΗ,ΑΦΜ_ΣΥΝΑΛΛΑΣΣΟΜΕΝΟΥ FROM ORDERS O
       INNER JOIN SLM S
       ON S.SLMID = O.SLMID             
       INNER JOIN CUSTOMER C
       ON C.TRAID = O.TRAID              
        WHERE O.SLMID = {$id}
        AND PROCESSED = 'NO'
MARKER;
}

  
//print_r($sql);
//print_r($_GET['traid']);

$result_set = $database->query($sql);
$MultiDimArray = array();
while ($row = mysql_fetch_assoc($result_set)) 
			{
                         $MultiDimArray[] = array ( 'TRAID' => $row['TRAID'],
                                                    'ORDERDATE' => $row['ORDERDATE'],
                                                    'SLMID'=>$row['SLMID'],
                                                    'SLMNAME'=>$row['SLMNAME'],
                                                    'PROCESSED'=>$row['PROCESSED'],
                                                    'LEENAME'=>$row['ΕΠΩΝΥΜΙΑ_ΠΕΛΑΤΗ'],
                                                    'LEEAFM'=>$row['ΑΦΜ_ΣΥΝΑΛΛΑΣΣΟΜΕΝΟΥ'],
                                                    'ORDERID'=>$row['ORDERID'],
                             );
			}

//$xreosi = 0;
//$pistosi = 0;
//$tziros = 0;
//$ypoloipo = 0;
//$clientno =0;
//foreach($MultiDimArray as $result){
//    $xreosi +=$result['XREOSI'];
//    $pistosi += $result['PISTOSI'];
//    $tziros += $result['TZIROS'];
//    $ypoloipo = $result['YPOLOIPO'];
//    $clientno +=1;
//    $afm = $result['LEEAFM'];
//    $leename = $result['LEENAME'];
//    
//    }   
       
//print_r($MultiDimArray);
 
        //$name = 'John';
        $template = $twig->loadTemplate('orderList.html');  
        echo $template->render(array('username' => $username,                                     
                                      'res'=>$MultiDimArray,
                                      
                                    
                                     ));

?>
