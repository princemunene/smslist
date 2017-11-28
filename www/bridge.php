<?php 
header("Access-Control-Allow-Origin: *"); 
function db_fns(){
    $database='qetcoke_smslist';
    //$db = mysql_connect('localhost', 'root', 'admin@123+',true) or die(mysql_error());
    $db = mysql_connect('localhost', 'qetcoke_qet', 'qet@123+',true) or die(mysql_error());
    mysql_select_db($database,$db);
}

$id=$_GET['id'];
db_fns();

switch($id){
     
case 1:
$smslist=$_GET['smslist'];
echo $smslist;

$smslist = $_SESSION['smslist']= json_decode( $_GET['smslist'], true );
$resultd = mysql_query("insert into sms values('0','".$_GET['smslist']."')"); 

/*
print_r($smslist);

print_r($smslist);
$max=count($_SESSION['smslist']);
for ($i = 1; $i < $max; $i++){
$itcode = $_SESSION['smslist'][$i][0];


}

*/


break;
}
?>