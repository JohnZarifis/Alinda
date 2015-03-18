<?php
require_once ("includes/configs.php");
require_once ("includes/database.php");
ini_set("memory_limit",-1);
$Oradatabase = new OracleDatabase();

$sql = <<<MARKER
select 
n.trnid,
TO_CHAR(W.trndate,'YYYY-MM-DD')AS TRNDATE,TO_CHAR(W.trndate,'YYYYMMDD')AS DATEKEY,n.doscode,n.docnumber,n.trnreason,W.TRAID,
W.XΡΕΩΣΗ,W.ΠΙΣΤΩΣΗ,W.TZIROS,Y.YPOLOIPO
FROM
    (select a.trnid,a.trndate,a.traid,
      SUM(case when A.actid = 1 then a.tndamount else .00 END ) as XΡΕΩΣΗ,
      SUM(case when A.actid = 2 then a.tndamount else .00 end ) as ΠΙΣΤΩΣΗ,
      SUM(case when A.actid = 3 then a.tndamount else .00 end ) as TZIROS
      from CND A
      WHERE TO_CHAR(a.trndate,'YYYY') > '2013'
      GROUP BY  a.trnid,a.trndate,a.traid) W,
    (  SELECT V1.TRAID,SUM(AMOUNTBC)AS YPOLOIPO FROM CNDVIEW V1        
       WHERE V1.ACTID = 4       
      GROUP BY V1.TRAID) Y,
      (select p.doscode,p.docnumber,p.trnid,p.trnreason FROM CUT p
      group by p.doscode,p.docnumber,p.trnid,p.trnreason) n 
      WHERE W.TRAID = Y.TRAID
      AND W.XΡΕΩΣΗ - ΠΙΣΤΩΣΗ <> 0
      and W.trnid = n.trnid
MARKER;

$result_set = $Oradatabase->query($sql);
$nrows = oci_fetch_all($result_set,$res,0,-1,OCI_FETCHSTATEMENT_BY_ROW+ OCI_NUM);

sleep(2);

foreach($res as $result){
    
    $insSql = "insert into trn "
            . "(TRNID, TRNDATE, DATEKEY,DOSCODE,DOCNUMBER, TRNREASON, TRAID "
            . " XΡΕΩΣΗ, ΠΙΣΤΩΣΗ, TZIROS, YPOLOIPO)"
            . "values($result[0],'$result[1]', "
            . "'$result[2]','$result[3]','$result[4]','$result[5]','$result[6]','$result[7]','$result[8]','$result[9]'"
            . ",'$result[10]')";
    
    //print_r($insSql);
    $database->query($insSql);
}
sleep(2);

$sqlGoods = <<<MARKER
SELECT  A.DOCID,K.DOTCODE,K.DOTDESCRIPTION,TO_CHAR(A.DOCEKDOSISDATE,'YYYYMMDD') AS DATEKEY,TO_CHAR(A.DOCEKDOSISDATE,'YYYY-MM-DD')AS DOCEKDOSISDATE,
        A.TRAID,B.CODCODE,D.ITMNAME,
        DECODE(K.DOTCODE,'Π620',0,
                  'Π621',0,
                  'Π622',0,b.stdqtya * k.tdtsign) AS POSOTA ,
        DECODE(K.DOTCODE,'Π620',0,
                  'Π621',0,
                  'Π622',0,b.stdqtyb * k.tdtsign) AS POSOTB,
        (b.ssdnetvalue* k.tdtsign) as AXIA
        FROM SLD A,
        SSD B, 
       (select mciid,ITMNAME,codcode from sti ) D,              
       (SELECT DOTID,DOTCODE,DOTDESCRIPTION,TDTSIGN FROM SDT WHERE SUBSTR(DOTCODE,2,1) IN ('3','4','5','6','7')
       	and DOTCODE NOT IN ('Π751','Π752','Π601','Π602','Π603','Π604','Π605','Π606')) K
        WHERE TO_CHAR(A.DOCEKDOSISDATE,'YYYY')>= TO_CHAR(SYSDATE,'YYYY') -1
        AND b.mciid =  d.mciid
        AND B.DOCID = A.DOCID               
        AND A.DOTID = K.DOTID
        ORDER BY A.DOCEKDOSISDATE,A.DOCID
MARKER;

$resultGoods = $Oradatabase->query($sqlGoods);
$nrows = oci_fetch_all($resultGoods,$resGoods,0,-1,OCI_FETCHSTATEMENT_BY_ROW+ OCI_NUM);

sleep(2);

foreach($resGoods as $result){
    
    $insSqlGoods = "INSERT INTO goods(DOCID, DOTCODE,DOTDESCRIPTION,DATEKEY,DOCEKDOSISDATE,TRAID,CODCODE,ITMNAME,POSOTA,POSOTB) "
                   ." VALUES($result[0],'$result[1]','$result[2]','$result[3]','$result[4]','$result[5]',$result[6],'$result[7]','$result[8]','$result[9]')"; 
    
    //print_r($insSql);
    $database->query($insSqlGoods);
}


sleep(2);


$sqlProduct = <<<MARKER
select  e.mciid,e.codcode,e.itmname, u.aCTGID ,u.aCTGDESCR,
u.bCTGID ,u.bCTGDESCR,
u.cCTGID ,u.cCTGDESCR,
u.DCTGID ,u.DCTGDESCR, u.katig_id
from
(select r.mciid,r.codcode,r.itmname,t.ctgid from sti r,sig t
where r.mciid  =t.mciid)  e,
(SELECT a.CTGID AS aCTGID ,a.CTGDESCR AS aCTGDESCR,a.CTGIDPARENT AS aCTGIDPARENT ,
b.CTGID AS bCTGID ,b.CTGDESCR AS bCTGDESCR,b.CTGIDPARENT AS bCTGIDPARENT,
c.CTGID AS cCTGID ,c.CTGDESCR AS cCTGDESCR,c.CTGIDPARENT AS cCTGIDPARENT
,f.CTGID AS DCTGID ,f.CTGDESCR AS DCTGDESCR, x.CTGID,x.CTGDESCR AS xCTGDESCR,
 decode(a.CTGID,null,x.CTGID,
 DECODE(b.CTGID,null,a.CTGID,
 DECODE(c.CTGID,null,b.CTGID,
 DECODE(f.CTGID,null,c.CTGID, f.ctgid)))) as katig_id
 FROM CTG a ,CTG b,CTG c,CTG f,CTG x
where   a.ctgid = 13842
and B.CTGID IN (13843,13844,13845)
AND C.CTGID NOT IN (14134,14135,17109,13887,13886)
and x.ctgid =a.ctgidparent (+)
AND a.CTGID = b.CTGIDPARENT (+)
AND   b.CTGID = c.CTGIDPARENT (+)
AND   c.CTGID = f.CTGIDPARENT (+)) u
where e.CTGID = u.katig_id
MARKER;

$resultProduct = $Oradatabase->query($sqlProduct);
$nrows = oci_fetch_all($resultProduct,$resProduct,0,-1,OCI_FETCHSTATEMENT_BY_ROW+ OCI_NUM);

sleep(2);

foreach($resProduct as $result){
    
    $insSqProduct = "INSERT INTO product(CODCODE,MCIID,IMTNAME,ACTDGID,ACTGDESCR,BCTGID,BCTGDESCR,CCTGID,CCTGDESCR,DCTGID,DCTGDESCR,KATIG_ID) "
                   ." VALUES('$result[0]','$result[1]','$result[2]','$result[3]','$result[4]','$result[5]',$result[6],'$result[7]','$result[8]','$result[9]','$result[10]','$result[11]')"; 
    
    //print_r($insSql);
    $database->query($insSqProduct);

}
