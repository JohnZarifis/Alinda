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

$from = '01-01-2015';

//$to =  'SYSDATE()';
$to =  date("d-m-Y");



if($isAdmin == 1){
$filter = "";
}
else if($isAdmin == 2){

$filter = "AND Supervisor = {$id}";	
}
else{
$filter = "AND commonPSW = {$commonPSW}";
}

if(isset($_POST['from']) && isset($_POST['to'])){
$from = $_POST['from'];
$to = $_POST['to'];
}



$fromLast =  strtotime($from .' -1 year');
$fromLast =  date('d-m-Y', $fromLast);
$toLast = strtotime($to .' -1 year');
$toLast = date('d-m-Y', $toLast);

$sql = <<< MARKER
SELECT  G.TRAID, LEENAME, G.CODCODE, G.ITMNAME, BCTGDESCR,CCTGDESCR,
                 SUM( case when YEAR(DOCEKDOSISDATE) = YEAR(CURDATE()) 
                                    then POSOTA
                                    else .00 END ) as POSOTITA_CURRENT_YEAR,
                 SUM( case when YEAR(DOCEKDOSISDATE) = YEAR(CURDATE()) 
                                    then AXIA
                                    else .00 END ) as AXIA_CURRENT_YEAR,
				 SUM( case when YEAR(DOCEKDOSISDATE) = YEAR(CURDATE()) -1
                                    then POSOTA
                                    else .00 END ) as POSOTITA_PAST_YEAR,
                                    SUM( case when YEAR(DOCEKDOSISDATE) = YEAR(CURDATE()) -1
                                    then AXIA
                                    else .00 END ) as AXIA_PAST_YEAR
                FROM GOODS G
	            INNER JOIN CUSTOMER C				
                ON C.TRAID = G.TRAID
                INNER JOIN SLM S               
                ON S.SLMID = C.SLMCODE
                INNER JOIN PRODUCT P
                ON P.CODCODE = G.CODCODE              
                WHERE 
                ( DOCEKDOSISDATE  BETWEEN 
                STR_TO_DATE('{$from}', '%d-%m-%Y')  AND STR_TO_DATE('{$to}', '%d-%m-%Y')
				OR (DOCEKDOSISDATE  BETWEEN STR_TO_DATE('{$fromLast}', '%d-%m-%Y')  AND STR_TO_DATE('{$toLast}', '%d-%m-%Y')))
				{$filter}
				GROUP BY G.TRAID, LEENAME, CODCODE, ITMNAME
MARKER;

if(isset($_GET['traid']))
    {
        $traid =  $_GET['traid']; 
//if(isset($_POST['apo_value']) && isset($_POST['mexri_value'])){
//$from = $_POST['apo_value'];
//$to = $_POST['mexri_value'];
$sql = <<<MARKER
SELECT G.TRAID, LEENAME, G.CODCODE, G.ITMNAME,BCTGDESCR,CCTGDESCR, 
                SUM( case when YEAR(DOCEKDOSISDATE) = YEAR(CURDATE()) 
                                    then POSOTA
                                    else .00 END ) as POSOTITA_CURRENT_YEAR,
                 SUM( case when YEAR(DOCEKDOSISDATE) = YEAR(CURDATE()) 
                                    then AXIA
                                    else .00 END ) as AXIA_CURRENT_YEAR,
				 SUM( case when YEAR(DOCEKDOSISDATE) = YEAR(CURDATE()) -1
                                    then POSOTA
                                    else .00 END ) as POSOTITA_PAST_YEAR,
                                    SUM( case when YEAR(DOCEKDOSISDATE) = YEAR(CURDATE()) -1
                                    then AXIA
                                    else .00 END ) as AXIA_PAST_YEAR
                FROM GOODS G
                INNER JOIN CUSTOMER C				
                ON C.TRAID = G.TRAID
                INNER JOIN PRODUCT P
                ON P.CODCODE = G.CODCODE
                WHERE C.TRAID = {$traid}
                AND ( DOCEKDOSISDATE  BETWEEN 
                STR_TO_DATE('{$from}', '%d-%m-%Y')  AND STR_TO_DATE('{$to}', '%d-%m-%Y')
				OR (DOCEKDOSISDATE  BETWEEN STR_TO_DATE('{$fromLast}', '%d-%m-%Y')  AND STR_TO_DATE('{$toLast}', '%d-%m-%Y'))) 
GROUP BY TRAID, LEENAME, CODCODE, ITMNAME
MARKER;
}   
  
    
    


//print_r($sql);
//print_r($_GET['traid']);


//goro
$result_set = $database->query($sql);
$MultiDimArray = array();
while ($row = mysql_fetch_assoc($result_set)) 
			{
                         $MultiDimArray[] = array ( 
                                                    'LEENAME' => $row['LEENAME'],
                                                    'TRAID'=>$row['TRAID'],
                                                    'CODCODE'=>$row['CODCODE'],
                                                    'ITMNAME'=>$row['ITMNAME'],
                                                    'POSOTITA_CURRENT_YEAR'=>$row['POSOTITA_CURRENT_YEAR'],
                                                    'POSOTITA_PAST_YEAR'=>$row['POSOTITA_PAST_YEAR'],
                                                    'AXIA_CURRENT_YEAR'=>$row['AXIA_CURRENT_YEAR'],
                                                    'AXIA_PAST_YEAR'=>$row['AXIA_PAST_YEAR'],
                                                    'BCTGDESCR'=>$row['BCTGDESCR'],
                                                    'CCTGDESCR'=>$row['CCTGDESCR'],
                                                   
                                                    
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
        $template = $twig->loadTemplate('goods.html');  
        echo $template->render(array('username' => $username,                                     
                                      'res'=>$MultiDimArray,
                                      'from'=>$from,
                                      'to' => $to,
                                      'fromLast'=>$fromLast,
                                      'toLast'=>$toLast,
                                      
                                      
                                    
                                     ));

?>
