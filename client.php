<?php
require_once ("includes/configs.php");
require_once ("includes/session.php");
require_once("includes/funcs.php");
require_once("includes/database.php");

if (!$session->is_logged_in()) { redirect_to("login.php"); }

$username = $_SESSION['user_name'];
$id = $_SESSION['user_id'];
//print_r($_SESSION); //for debugging reasons
$traid =  $_GET['traid'] ;
//print_r($traid);
//print_r($_GET['traid']);

$from = '01/01/2014';
//$to =  'SYSDATE()';
$to =  date("d/m/Y");

if(isset($_POST['from']) && isset($_POST['to'])){
$from = $_POST['from'];
$to = $_POST['to'];
}

$sql = <<<MARKER
SELECT TRNDATE,TRAID,DOSCODE,DOCNUMBER,TRNREASON,XΡΕΩΣΗ,ΠΙΣΤΩΣΗ,ΤΖΙΡΟΣ,YPOL_2,LEENAME,LEEAFM,SLMID,ADRCITY
FROM TRN 
WHERE TRNDATE  BETWEEN 
STR_TO_DATE('{$from}', '%d/%m/%Y')  AND STR_TO_DATE('{$to}', '%d/%m/%Y')
AND TRAID = {$traid}
AND DOSCODE NOT IN ('ΠΑΡΟ','ΠΑΡΑ')
ORDER BY TRNID
MARKER;





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
                                                    'TZIROS'=>$row['ΤΖΙΡΟΣ'],
                                                    'LEENAME'=>$row['LEENAME'],
                                                    'LEEAFM'=>$row['LEEAFM'],
                                                    'DOCNUMBER'=>$row['DOCNUMBER'], 
                                                    'TRNREASON'=>$row['TRNREASON'], 
                                                    'YPOLOIPO'=>$row['YPOL_2'], 
                                                    'SLMID'=>$row['SLMID'], 
                                                    'ADRCITY'=>$row['ADRCITY'],
                             );
			}

$graphSql = "SELECT MONTH(TRNDATE) as MONTH ,SUM(ΤΖΙΡΟΣ) AS TZIROS FROM alinda.trn GROUP BY MONTH(TRNDATE) ";
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
                                     ));

?>
