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
//print_r($_SESSION); //for debugging reasons
if(isset($_GET['traid'])){
    $traid =  $_GET['traid'] ;
}
//$traid =  $_GET['traid'] ;
//print_r($traid);
//print_r($_GET['traid']);

$from = '01/01/2014';
//$to =  'SYSDATE()';
$to =  date("d/m/Y");

if(isset($_POST['from']) && isset($_POST['to']) && isset($_POST['traid']) ){
$from = $_POST['from'];
$to = $_POST['to'];
$traid = $_POST['traid'];
//print_r($_POST);
}

$sql = <<<MARKER
SELECT TRNDATE,T.TRAID,DOSCODE,DOCNUMBER,TRNREASON,XΡΕΩΣΗ,ΠΙΣΤΩΣΗ,TZIROS,YPOLOIPO,LEENAME,LEEAFM,C.SLMCODE,ADRCITY
FROM TRN T
INNER JOIN CUSTOMER C
ON C.TRAID = T.TRAID
WHERE
TRNDATE  BETWEEN 
STR_TO_DATE('{$from}', '%d/%m/%Y')  AND STR_TO_DATE('{$to}', '%d/%m/%Y')
AND T.TRAID = {$traid}
ORDER BY TRNID
MARKER;

//print_r($sql);



//goro
$result_set = $database->query($sql);
$MultiDimArray = array();
while ($row = mysql_fetch_assoc($result_set)) 
			{
                         $MultiDimArray[] = array ( 'TRNDATE' => $row['TRNDATE'],
                                                    'DOSCODE' => $row['DOSCODE'],
                                                    'TRAID'=>$row['TRAID'],
                                                    'XREOSI'=>$row['XΡΕΩΣΗ'],
                                                    'PISTOSI'=>$row['ΠΙΣΤΩΣΗ'],
                                                    'TZIROS'=>$row['TZIROS'],
                                                    'LEENAME'=>$row['LEENAME'],
                                                    'LEEAFM'=>$row['LEEAFM'],
                                                    'DOCNUMBER'=>$row['DOCNUMBER'], 
                                                    'TRNREASON'=>$row['TRNREASON'], 
                                                    'YPOLOIPO'=>$row['YPOLOIPO'], 
                                                    'SLMCODE'=>$row['SLMCODE'], 
                                                    'ADRCITY'=>$row['ADRCITY'],
                             );
			}

$graphSql = "SELECT MONTH(TRNDATE) as MONTH ,SUM(TZIROS) AS TZIROS FROM alinda.trn "
        . " WHERE TRNDATE  BETWEEN STR_TO_DATE('{$from}', '%d/%m/%Y')  AND STR_TO_DATE('{$to}', '%d/%m/%Y') "
        . "AND TRAID = {$traid} "
        . "GROUP BY MONTH(TRNDATE) ";
$result_graph = $database->query($graphSql);
$Grapharray;
while ($row = mysql_fetch_assoc($result_graph)) {
    $Grapharray[] = array (
                     $row['TZIROS'],
    );
}
$graph =  json_encode($Grapharray);
			
$xreosi = 0;
$pistosi = 0;
$tziros = 0;
$ypoloipo = 0;
$clientno =0;
foreach($MultiDimArray as $result){
    $xreosi +=$result['XREOSI'];
    $pistosi += $result['PISTOSI'];
    $tziros += $result['TZIROS'];
    $ypoloipo = $result['YPOLOIPO'];
    $clientno +=1;
    $afm = $result['LEEAFM'];
    $leename = $result['LEENAME'];
    
    }   
       

 
        //$name = 'John';
        $template = $twig->loadTemplate('client.html');  
        echo $template->render(array('username' => $username,
                                      'clientno'=>$clientno,
                                      'res'=>$MultiDimArray,
                                      'xreosi'=>$xreosi,
                                      'pistosi'=>$pistosi,
                                      'tziros' =>$tziros,
                                      'ypoloipo'=>$ypoloipo,
                                      'LEENAME'=>$leename,
                                      'graph'=>$graph,
                                      'from'=>$from,
                                      'to' => $to,
                                      'traid'=>$traid,
                                     ));

?>
