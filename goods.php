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
$traid = '';
$from = '01-01-2015';
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
$traid = $_POST['traid'];
}
if(isset($_GET['traid']))
    {
        $traid =  $_GET['traid'];
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

if(isset($_GET['traid'])||(isset($_POST['traid']) && trim($traid) != '' ))
    {
        
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
                                    else .00 END ) as AXIA_PAST_YEAR,
                MAX(MAXPRICE) AS LASTPRICE
                FROM GOODS G
                INNER JOIN CUSTOMER C				
                ON C.TRAID = G.TRAID
                INNER JOIN PRODUCT P
                ON P.CODCODE = G.CODCODE
                INNER JOIN LASTPRICE L 
                ON (L.TRAID = C.TRAID AND L.CODCODE = P.CODCODE)
                WHERE C.TRAID = {$traid}
                AND ( DOCEKDOSISDATE  BETWEEN 
                STR_TO_DATE('{$from}', '%d-%m-%Y')  AND STR_TO_DATE('{$to}', '%d-%m-%Y')
				OR (DOCEKDOSISDATE  BETWEEN STR_TO_DATE('{$fromLast}', '%d-%m-%Y')  AND STR_TO_DATE('{$toLast}', '%d-%m-%Y'))) 
GROUP BY TRAID, LEENAME, CODCODE, ITMNAME
MARKER;
}   
  
    
    

// for debugging
//print_r($sql);
//print_r($_GET['traid']);
//print_r($traid.'--');
//print_r($_POST['traid']);



//goro
$result_set = $database->query($sql);
$MultiDimArray = array();
if(isset($_GET['traid']) || (isset($_POST['traid']) && trim($traid) != '' ))
    {
while ($row = mysql_fetch_assoc($result_set)) 
			{
                         $MultiDimArray[] = array ( 
                                                    'LEENAME' => $row['LEENAME'],
                                                    'TRAID'=>$row['TRAID'],
                                                    'CODCODE'=>$row['CODCODE'],
                                                    'ITMNAME'=>$row['ITMNAME'],
                                                    'POSOTITA_CURRENT_YEAR'=>number_format_clean($row['POSOTITA_CURRENT_YEAR']),
                                                    'POSOTITA_PAST_YEAR'=>number_format_clean($row['POSOTITA_PAST_YEAR']),
                                                    'AXIA_CURRENT_YEAR'=>number_format_clean($row['AXIA_CURRENT_YEAR']),
                                                    'AXIA_PAST_YEAR'=>number_format_clean($row['AXIA_PAST_YEAR']),
                                                    'BCTGDESCR'=>$row['BCTGDESCR'],
                                                    'CCTGDESCR'=>$row['CCTGDESCR'],
                                                    'LASTPRICE' =>number_format_lastprice($row['LASTPRICE']),
                                                    'POSOTITA_CURRENT'=>$row['POSOTITA_CURRENT_YEAR'],
                                                    'POSOTITA_PAST'=>$row['POSOTITA_PAST_YEAR'],
                                                    'AXIA_CURRENT'=>$row['AXIA_CURRENT_YEAR'],
                                                    'AXIA_PAST'=>$row['AXIA_PAST_YEAR'],
                                                    
                                                   
                                                    
                             );
			}
	}
while ($row = mysql_fetch_assoc($result_set)) 
			{
                         $MultiDimArray[] = array ( 
                                                    'LEENAME' => $row['LEENAME'],
                                                    'TRAID'=>$row['TRAID'],
                                                    'CODCODE'=>$row['CODCODE'],
                                                    'ITMNAME'=>$row['ITMNAME'],
                                                    'POSOTITA_CURRENT_YEAR'=>number_format_clean($row['POSOTITA_CURRENT_YEAR']),
                                                    'POSOTITA_PAST_YEAR'=>number_format_clean($row['POSOTITA_PAST_YEAR']),
                                                    'AXIA_CURRENT_YEAR'=>number_format_clean($row['AXIA_CURRENT_YEAR']),
                                                    'AXIA_PAST_YEAR'=>number_format_clean($row['AXIA_PAST_YEAR']),
                                                    'POSOTITA_CURRENT'=>$row['POSOTITA_CURRENT_YEAR'],
                                                    'POSOTITA_PAST'=>$row['POSOTITA_PAST_YEAR'],
                                                    'AXIA_CURRENT'=>$row['AXIA_CURRENT_YEAR'],
                                                    'AXIA_PAST'=>$row['AXIA_PAST_YEAR'],
                                                    'BCTGDESCR'=>$row['BCTGDESCR'],
                                                    'CCTGDESCR'=>$row['CCTGDESCR'],
                                                   
                                                    
                                                   
                                                    
                             );
			}
$posotitaCurrentYear = 0;
$posotitaLastYear = 0;
$axiaCurrentYear = 0;
$axiaPastYear = 0;
//$clientno =0;
foreach($MultiDimArray as $result){
    $posotitaCurrentYear +=$result['POSOTITA_CURRENT'];
    $posotitaLastYear += $result['POSOTITA_PAST'];
    $axiaCurrentYear += $result['AXIA_CURRENT'];
    $axiaPastYear += $result['AXIA_PAST'];
//    $clientno +=1;
//    $afm = $result['LEEAFM'];
//    $leename = $result['LEENAME'];
//    
   }   
       
//print_r($MultiDimArray);
 
        //$name = 'John';
        $template = $twig->loadTemplate('goods.html');  
        echo $template->render(array('username' => $username,                                     
                                      'res'=>$MultiDimArray,
                                      'from'=>$from,
                                      'to' => $to,
                                      'fromLast'=>$fromLast,
                                      'toLast'=>$toLast,
                                      'traid'=>$traid,
                                      'posotitaCurrentYear'=>$posotitaCurrentYear,
                                      'posotitaLastYear'=>$posotitaLastYear,
                                      'axiaCurrentYear'=>number_format_clean($axiaCurrentYear),
                                      'axiaPastYear'=>number_format_clean($axiaPastYear),
                                      
                                    
                                     ));

?>
