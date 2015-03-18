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



$sql = <<< MARKER
SELECT  MCIID,CODCODE,ITMNAME,
				
                case when wrhid = '22'
                                    then QTYA
                                    else .00 END  as POSOT_22,
                
                case when wrhid = '43'
                                    then QTYA
                                    else .00 END  as POSOT_43
                 
                                    
                FROM WRH
                
				GROUP BY MCIID,CODCODE,ITMNAME
MARKER;

//print_r($sql);


$result_set = $database->query($sql);
$MultiDimArray = array();
while ($row = mysql_fetch_assoc($result_set)) 
			{
                         $MultiDimArray[] = array ( 
                                                    'MCIID' => $row['MCIID'],                                                   
                                                    'CODCODE'=>$row['CODCODE'],
                                                    'ITMNAME'=>$row['ITMNAME'],
                                                    'POSOT_22'=>$row['POSOT_22'],
                                                    'POSOT_43'=>$row['POSOT_43'],
                                                    
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
        $template = $twig->loadTemplate('warehouse.html');  
        echo $template->render(array('username' => $username,                                     
                                      'res'=>$MultiDimArray,
                                     
                                      
                                    
                                     ));

?>
