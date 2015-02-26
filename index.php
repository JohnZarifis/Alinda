<?php
require_once ("includes/configs.php");
require_once ("includes/session.php");
require_once("includes/funcs.php");
require_once("includes/database.php");

if (!$session->is_logged_in()) { redirect_to("login.php"); }

$username = $_SESSION['user_name'];
$id = $_SESSION['user_id'];
$psw = $_SESSION['user_psw'];
//print_r($_SESSION); //for debugging reasons
///$sql = "select SLMID, SLMNAME, TRACODE, LEENAME,LEEAFM, ADRSFULLDEST, TZIROS08, TZIROS09, TZIROS10,TZIROS11,TZIROS12,XREOSI,PISTOSI,YPOLOIPO from Z_TSIROS_YPOLI";
//$sql.= " WHERE SLMID = {$id} ";
$text = 'tets';
$from = '01/01/2014';
$to =  date("d/m/Y");
//$date = str_replace('/', '-', $from);
//sql_from=date('d-m-Y', strtotime($date));
//$date = str_replace('/', '-', $to);
//sql_to=date('d-m-Y', strtotime($date));
if(isset($_POST['from']) && isset($_POST['to'])){
$from = $_POST['from'];
$to = $_POST['to'];}
$sql = <<<MARKER
SELECT MIN(TRNDATE), MAX(TRNDATE) , TRAID,sum(XΡΕΩΣΗ),sum(ΠΙΣΤΩΣΗ),sum(ΤΖΙΡΟΣ),LEENAME,LEEAFM,
SLMID
FROM TRN 
WHERE TRNDATE  BETWEEN 
STR_TO_DATE('{$from}', '%d/%m/%Y') AND STR_TO_DATE('{$to}', '%d/%m/%Y')
AND SLMID = {$id}
GROUP BY TRAID ,LEENAME,LEEAFM,SLMID
ORDER BY TRAID
MARKER;




//print_r($sql); //for debugging reasons

$result_set = $database->query($sql);
$MultiDimArray = array();
while ($row = mysql_fetch_assoc($result_set)) 
			{
                         $MultiDimArray[] = array ( 'Min Date' => $row['MIN(TRNDATE)'],
                                                    'Max Date' => $row['MAX(TRNDATE)'],
                                                    'TRAID'=>$row['TRAID'],
                                                    'XREOSI'=>$row['sum(XΡΕΩΣΗ)'],
                                                    'PISTOSI'=>$row['sum(ΠΙΣΤΩΣΗ)'],
                                                    'TZIROS'=>$row['sum(ΤΖΙΡΟΣ)'],
                                                    'LEENAME'=>$row['LEENAME'],
                                                    'LEEAFM'=>$row['LEEAFM'],
                                                    'SLMID'=>$row['SLMID'], 
                             );
			}
                        
//$nrows = oci_fetch_all($result_set,$res,0,-1,OCI_FETCHSTATEMENT_BY_ROW+ OCI_NUM);
//print_r($nrows);
//print_r($sql);
//print_r($res);
//print_r($MultiDimArray);

$graphSql = "SELECT MONTH(TRNDATE) as MONTH ,SUM(ΤΖΙΡΟΣ) AS TZIROS FROM alinda.trn "
        . " WHERE SLMID = {$id} AND TRNDATE  BETWEEN STR_TO_DATE('{$from}', '%d/%m/%Y')  AND STR_TO_DATE('{$to}', '%d/%m/%Y')  "
        . " GROUP BY MONTH(TRNDATE) ";
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
    //$ypoloipo += $result[6];
    $clientno +=1;
    
    }      
	

        //$name = 'John';
        $template = $twig->loadTemplate('index.html');  
        echo $template->render(array('username' => $username,
                                    'clientno'=>$clientno,
                                     'res'=>$MultiDimArray,
                                     'xreosi'=>$xreosi,
                                     'pistosi'=>$pistosi,
                                     'tziros' =>$tziros,
                                     'ypoloipo'=>$ypoloipo,
                                     'from'=>$from,
                                     'to' => $to,
                                     'graph'=>$graph
                                    ));
?>
