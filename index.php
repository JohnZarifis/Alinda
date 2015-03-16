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

$from = '01/01/2014';
$to =  date("d/m/Y");
//$date = str_replace('/', '-', $from);
//sql_from=date('d-m-Y', strtotime($date));
//$date = str_replace('/', '-', $to);
//sql_to=date('d-m-Y', strtotime($date));
if(isset($_POST['from']) && isset($_POST['to'])){
$from = $_POST['from'];
$to = $_POST['to'];}

if($isAdmin == 1){
$filter = "";
}
else if($isAdmin == 2){

$filter = "AND Supervisor = {$id}";	
}
else if($isAdmin == 2){
	
}
else{
$filter = "AND commonPSW = {$commonPSW}";
}

$sql = <<<MARKER
SELECT MIN(TRNDATE), MAX(TRNDATE) , T.TRAID ,sum(XΡΕΩΣΗ),sum(ΠΙΣΤΩΣΗ),sum(TZIROS),YPOLOIPO,LEENAME,LEEAFM,S.SLMID,C.SLMNAME
FROM TRN T
INNER JOIN CUSTOMER C
ON T.TRAID = C.TRAID
INNER JOIN SLM S
ON S.SLMID = C.SLMCODE
WHERE TRNDATE  BETWEEN 
STR_TO_DATE('{$from}', '%d/%m/%Y') AND STR_TO_DATE('{$to}', '%d/%m/%Y')
{$filter}
GROUP BY T.TRAID ,LEENAME,LEEAFM,S.SLMID,YPOLOIPO,C.SLMNAME
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
                                                    'TZIROS'=>$row['sum(TZIROS)'],
                                                    'LEENAME'=>$row['LEENAME'],
                                                    'LEEAFM'=>$row['LEEAFM'],
                                                    'SLMID'=>$row['SLMID'], 
                                                    'YPOLOIPO'=>$row['YPOLOIPO'],
                                                    'SLMNAME'=>$row['SLMNAME']
                             );
			}
                        
//$nrows = oci_fetch_all($result_set,$res,0,-1,OCI_FETCHSTATEMENT_BY_ROW+ OCI_NUM);
//print_r($nrows);
//print_r($sql);
//print_r($res);
//print_r($MultiDimArray);

$graphSql = <<<MARKER
				SELECT MONTH(TRNDATE) as MONTH ,SUM(TZIROS) AS TZIROS FROM TRN T
                INNER JOIN  CUSTOMER C 
                ON T.TRAID = C.TRAID
                INNER JOIN   SLM S 
                ON  S.SLMID = C.SLMCODE
        	    WHERE TRNDATE  BETWEEN STR_TO_DATE('{$from}', '%d/%m/%Y')  AND STR_TO_DATE('{$to}', '%d/%m/%Y')
				{$filter}
        	    GROUP BY MONTH(TRNDATE) 
MARKER;
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
