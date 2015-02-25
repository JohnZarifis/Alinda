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
//$to =  'SYSDATE()';
$to =  date("d/m/Y");

if(isset($_POST['from']) && isset($_POST['to'])){
$from = $_POST['from'];
$to = $_POST['to'];
}

$sql = <<< MARKER
SELECT  G.TRAID, G.LEENAME, CODCODE, ITMNAME, 
                 SUM( case when G.ETOS = YEAR(CURDATE()) 
                                    then POSOTA
                                    else .00 END ) as POSOTITA_CURRENT_YEAR,
				SUM( case when G.ETOS = YEAR(CURDATE()) -1
                                    then POSOTA
                                    else .00 END ) as POSOTITA_PAST_YEAR
                FROM GOODS G
				INNER JOIN 
				(SELECT DISTINCT SLMID,TRAID FROM TRN) T
                ON T.TRAID = G.TRAID
                WHERE SLMID = {$id}
                AND  DOCEKDOSISDATE  BETWEEN 
STR_TO_DATE('{$from}', '%d/%m/%Y')  AND STR_TO_DATE('{$to}', '%d/%m/%Y')
GROUP BY G.TRAID, G.LEENAME, CODCODE, ITMNAME
MARKER;

if(isset($_GET['traid']))
    {
        $traid =  $_GET['traid']; 
//if(isset($_POST['apo_value']) && isset($_POST['mexri_value'])){
//$from = $_POST['apo_value'];
//$to = $_POST['mexri_value'];
$sql = <<<MARKER
SELECT MHNAS ,TRAID, LEENAME, CODCODE, ITMNAME, 
                 SUM( case when ETOS = YEAR(CURDATE()) 
                                    then POSOTA
                                    else .00 END ) as POSOTITA_CURRENT_YEAR,
				SUM( case when ETOS = YEAR(CURDATE()) -1
                                    then POSOTA
                                    else .00 END ) as POSOTITA_PAST_YEAR
                FROM GOODS
                WHERE TRAID = {$traid}
                AND  DOCEKDOSISDATE  BETWEEN 
                STR_TO_DATE('{$from}', '%d/%m/%Y')  AND STR_TO_DATE('{$to}', '%d/%m/%Y')
GROUP BY MHNAS ,TRAID, LEENAME, CODCODE, ITMNAME
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
                                      
                                    
                                     ));

?>
