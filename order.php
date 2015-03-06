<?php
require_once("includes/funcs.php");
require_once("includes/session.php");
require_once("includes/database.php");
require_once("includes/user.php");
require_once("includes/configs.php");

if (!$session->is_logged_in()) { redirect_to("login.php"); }
if (isset($_GET['traid'])){
$traid =  $_GET['traid'] ;
$sql = " SELECT TRAID,ΚΩΔ_ΠΕΛΑΤΗ,LEEID,ΕΝΕΡΓΟΣ,ΠΟΣΟΣΤΟ_ΕΚΠΤΩΣΗΣ,ΜΗΝΥΜΑ_ΤΙΜΟΛΟΓΗΣΗΣ,"
        . "ΕΠΩΝΥΜΙΑ_ΠΕΛΑΤΗ,ΑΦΜ_ΣΥΝΑΛΛΑΣΣΟΜΕΝΟΥ,ΝΟΜΙΣΜΑ,ΠΕΡΙΓΡΑΦΗ_ΤΡΟΠΟΥ_ΠΛΗΡΩΜΗΣ,ΤΡΟΠΟΣ_ΜΕΤΑΦΟΡΑΣ,"
        . "SLMID,ΠΩΛΗΤΗΣ,ADRIDMAIN,ADRID,ADRSTREET,ADRNUMBER,ADRCITY,ADRPHONE1,ADRPHONE2,ADREMAIL "
        . "FROM CUSTOMER WHERE TRAID = {$traid}";
$username = $_SESSION['user_name'];
$id = $_SESSION['user_id'];
$result_set = $database->query($sql);
while ($row = mysql_fetch_assoc($result_set)) 
			{
                         $leename = $row['ΕΠΩΝΥΜΙΑ_ΠΕΛΑΤΗ'];
                         $leeafm = $row['ΑΦΜ_ΣΥΝΑΛΛΑΣΣΟΜΕΝΟΥ'];
                         $leephone1 = $row['ADRPHONE1'];
                         $leephone2 = $row['ADRPHONE2'];
                         $leeEmail = $row['ADREMAIL'];
                         $leeCode = $row['ΚΩΔ_ΠΕΛΑΤΗ'];
			}

}
//$sqlProd = "SELECT productid ,productcode, ,package category,productname ,TAB, TABNAME FROM product";
$sqlProd = "SELECT MCIID as productid ,"
        . "CODCODE productcode,DCTGDESCR as category,ITMNAME as productname,BCTGID as TAB, BCTGDESCR as TABNAME "
        . "FROM product";
$resultProd = $database->query($sqlProd);

while ($row = mysql_fetch_assoc($resultProd)) 
			{
                         
                         $MultiDimArray[] = array ( 'productid' => $row['productid'],
                                                    'productcode' => $row['productcode'],
                                                    'category'=>$row['category'],
                                                    'productname'=>$row['productname'],                                               
                                                    'tab'=>$row['TAB'],
                                                    'tabname'=>$row['TABNAME'],
                             
                                                    
                                                    
                                                    
                             );
			}
                        
 $catno =0;
 $categories = array();
 $tabnames = array();
foreach($MultiDimArray as $result){
    $categories[]=$result['category'];
    $tabnames[]=$result['tabname'];
    $catno +=1;
}
 $unique_cat = array_unique($categories);
 $unique_tab = array_unique($tabnames);
 
 
 $sqlDistCatTabs = "SELECT DISTINCT DCTGDESCR as CATEGORY, BCTGDESCR as TABNAME FROM PRODUCT";
 $resultCat = $database->query($sqlDistCatTabs);
 $unCat = 0;
 while ($row = mysql_fetch_assoc($resultCat)) 
			{
                         $unCat+=1;
                         $MultiCatArray[] = array ( 
                                                    'Catid'=>$unCat,
                                                    'category'=>$row['CATEGORY'],                                                    
                                                    'tabname'=>$row['TABNAME'],
                                                     
                                                    
                                                    
                                                    
                             );
			}
 
 
//print_r($unique_cat);
//print_r($clientno);
//var_dump($unique_cat);
// Remember to give your form's submit tag a name="submit" attribute!
//if (isset($_POST['submit'])) {} // Form has been submitted.

  


 $template = $twig->loadTemplate('order.html');
  echo $template->render(array('username' => $username,
                               'leename'=>$leename,
                               'leeafm'=>$leeafm,
                               'categories'=>$unique_cat,
                               'tabnames'=>$unique_tab,
                               'res'=>$MultiDimArray,
                               'catTab'=>$MultiCatArray,
                               'traid'=>$traid,
                               'slmid'=>$id,
      ));
  



?>