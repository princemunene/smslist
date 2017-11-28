<?php 
header("Access-Control-Allow-Origin: *"); 
function db_fns(){
    $database='qetcoke_smslist';
    $db = mysql_connect('localhost', 'qetcoke_qet', 'qet@123+',true) or die(mysql_error());
    //$database='smslist';
    //$db = mysql_connect('localhost', 'root', 'admin@123+',true) or die(mysql_error());
    mysql_select_db($database,$db);
}



$id=$_POST['id'];
db_fns();

switch($id){
     
case 1:
$smslist=$_POST['smslist'];
//echo $smslist;

$smslist = $_SESSION['smslist']= json_decode( $_POST['smslist'], true );
$type=0;$typedesc='';
$filters=array();
$filters[1]='paid to';$filters[2]='withdraw';$filters[3]='of airtime';$filters[4]='paid to';$filters[5]='sent to';$filters[6]='have received';

$max=count($_SESSION['smslist']);
for ($i = 0; $i < $max; $i++){
$deviceid = $_SESSION['smslist'][$i]["sim_imsi"];
$address = $_SESSION['smslist'][$i]["address"];
$body = $_SESSION['smslist'][$i]["body"];
$body=mysql_real_escape_string(trim($body));

$pieces=explode(" ",$body);
$refno=$pieces[0];
$amount=filter_var( $pieces[3], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
$date=$pieces[7];
$time=$pieces[9];
$balance=filter_var( $pieces[14], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
$cost=filter_var( $pieces[17], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
foreach ($filters as $key => $val) {
    if (strpos($body, $val) !== false) {
        $type=$key;
        $typedesc=$val;
    }
}
$resultd = mysql_query("insert into sms values('0','".$body."','".$address."','".$deviceid."','".$refno."','".$amount."','".$date."','".$time."','".$balance."','".$cost."','".$type."','".$typedesc."')"); 

}




break;
}
?>