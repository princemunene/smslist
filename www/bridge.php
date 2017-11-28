<?php 
header("Access-Control-Allow-Origin: *"); 
function db_fns(){
    $database='qetcoke_smslist';
    $db = mysql_connect('localhost', 'qetcoke_qet', 'qet@123+',true) or die(mysql_error());
    //$database='smslist';
    //$db = mysql_connect('localhost', 'root', 'admin@123+',true) or die(mysql_error());
    mysql_select_db($database,$db);
}



if(isset($_POST['id'])){$id=$_POST['id'];}
else{$id=$_GET['id'];}
db_fns();


?>

<style>

body{font-size:11px; font-family:'Lucida Sans Unicode', 'Lucida Grande', sans-serif;}

</style>
<?php
switch($id){
     
case 1:
$smslist=$_POST['smslist'];


$smslist = $_SESSION['smslist']= json_decode( $_POST['smslist'], true );
$type=0;$typedesc='';
$filters=array();
$filters[1]='paid to';$filters[2]='Withdraw';$filters[3]='of airtime';$filters[4]='sent to';$filters[5]='have received';

$totuploaded=0;
$max=count($_SESSION['smslist']);
for ($i = 0; $i < $max; $i++){
$deviceid = $_SESSION['smslist'][$i]["sim_imsi"];
$address = $_SESSION['smslist'][$i]["address"];
$reply_path_present = $_SESSION['smslist'][$i]["reply_path_present"];
$body = $_SESSION['smslist'][$i]["body"];
$mil = $_SESSION['smslist'][$i]["date_sent"];
$body=mysql_real_escape_string(trim($body));



$pieces=explode(" ",$body);
foreach ($filters as $key => $val) {
    if (strpos($body, $val) !== false) {
        $type=$key;
        $typedesc=$val;
    }
}

switch($type){

    case 1:
    $refno=$pieces[0];
    $amount=filter_var( $pieces[2], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
    $date=$pieces[7];
    $time=$pieces[9];
    $balance=filter_var( $pieces[14], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
    $cost=filter_var( $pieces[17], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
    break;
     case 2:
    $refno=$pieces[0];
    $amount=filter_var( $pieces[6], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
    $balance=filter_var( $pieces[14], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
    $cost=filter_var( $pieces[17], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
    break;
     case 3:
    $refno=$pieces[0];
    $amount=filter_var( $pieces[3], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
    $balance=filter_var( $pieces[14], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
    $cost=filter_var( $pieces[17], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
    break;
     case 4:
    $refno=$pieces[0];
    $amount=filter_var( $pieces[2], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
    $balance=filter_var( $pieces[14], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
    $cost=filter_var( $pieces[17], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
    break;
     case 5:
    $refno=$pieces[0];
    $amount=filter_var( $pieces[4], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
    $balance=filter_var( $pieces[14], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
    $cost=filter_var( $pieces[17], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
    break;
    default:
    $refno=$pieces[0];
    $amount=filter_var( $pieces[3], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
    $balance=filter_var( $pieces[14], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
    $cost=filter_var( $pieces[17], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
    break;
}

$seconds = $mil / 1000;
$date= date("d/m/Y", $seconds);
$stamp= date("Ymd", $seconds);
$time= date("h:i A", $seconds);
$tdate=date('Ymd');
$next=date('Y-m-d', strtotime('-3 month')) ;
$next=preg_replace('~-~', '', $next);

//check if message is actually valid and if it is within the last 3 months and amount is larger than zero

 if($amount>0&&$reply_path_present==0&&$stamp>=$next){
  $totuploaded+=1;
  $resultd = mysql_query("insert into sms values('0','".$body."','".$address."','".$deviceid."','".$refno."','".$amount."','".$date."','".$time."','".$balance."','".$cost."','".$type."','".$typedesc."','".$stamp."')"); 


 }
}

echo 'Total Messages Uploaded:'.$totuploaded;

break;

case 2:
$deviceid=$_POST['deviceid'];
$tdate=date('Ymd');
$next=date('Y-m-d', strtotime('-3 month')) ;
$next=preg_replace('~-~', '', $next);
//give credit score
$result =mysql_query("select * from sms where stamp>='".$next."' and  deviceid='".$deviceid."'");
$num_results = mysql_num_rows($result);

if($num_results<=20){

    echo "You do not have enough M-PESA messages in your Inbox to calculate a credit score for you. Kindly Try again in a Month's time.";
    exit;
}

$aa=$bb=$cc=$dd=$ee=0;$ff=0;
$aaweight=20;$bbweight=15;$ccweight=15;$ddweight=15;$eeweight=15;$ffweight=20;//must be equal to 100
$aadiv=2000;$bbdiv=5000;$ccdiv=500;$dddiv=5000;$eediv=2000;$ffdiv=500;

for ($a=0; $a <$num_results; $a++) {
$row=mysql_fetch_array($result);
$type=stripslashes($row['type']);
$cost=stripslashes($row['cost']);
$amount=preg_replace('~,~', '', stripslashes($row['amount']));

if($type==1){$aa+=$amount;}if($type==2){$bb+=$amount;}if($type==3){$cc+=$amount;}if($type==4){$dd+=$amount;}if($type==5){$ee+=$amount;}
$ff+=$cost;

}
$aa=$aa/3;$bb=$bb/3;$cc=$cc/3;$dd=$dd/3;$ee=$ee/3;$ff=$ff/3;

$aascore=($aa/$aadiv)*$aaweight;if($aa>$aadiv){$aascore=$aaweight;}
$bbscore=($bb/$bbdiv)*$bbweight;if($bb>$bbdiv){$bbscore=$bbweight;}
$ccscore=($cc/$ccdiv)*$ccweight;if($cc>$ccdiv){$ccscore=$ccweight;}
$ddscore=($dd/$dddiv)*$ddweight;if($dd>$dddiv){$ddscore=$ddweight;}
$eescore=($ee/$eediv)*$eeweight;if($ee>$eediv){$eescore=$eeweight;}
$ffscore=($ff/$ffdiv)*$ffweight;if($ff>$ffdiv){$ffscore=$ffweight;}

$totscore=$aascore+$bbscore+$ccscore+$ddscore+$eescore+$ffscore;

if($totscore<30){$offer=500;$status='BRONZE';$col='#0ff';}
if($totscore>=30&&$totscore<70){$offer=1000;$status='SILVER';$col='#ff3';}
if($totscore>=70){$offer=2000;$status='GOLD';$col='#0f6';}

echo '<table id="datatable"  style="width:80%;text-align:center;font-weight:bold; padding:0;margin:0 1%;" >
     <tbody>
     <tr style="width:100%; height:20px;color:#fff; background:#333; padding:0">
        <td  style="width:70%;padding:5px">Description</td>
        <td  style="width:30%;padding:5px">Score</td>  
        </tr>

        <tr style="width:100%; height:20px;padding:0;  font-weight:normal  ">
        <td  style="width:4%;border-width:0.5px; border-color:#666; border-style:solid;padding:5px ">PAYBILL/BUY GOODS</td>
        <td  style="width:4%;border-width:0.5px; border-color:#666; border-style:solid;padding:5px ">'.$aascore.'</td>
        </tr>
        <tr style="width:100%; height:20px;padding:0;  font-weight:normal  ">
        <td  style="width:4%;border-width:0.5px; border-color:#666; border-style:solid;padding:5px ">WITHDRAWALS</td>
        <td  style="width:4%;border-width:0.5px; border-color:#666; border-style:solid;padding:5px ">'.$bbscore.'</td>
        </tr>
        <tr style="width:100%; height:20px;padding:0;  font-weight:normal  ">
        <td  style="width:4%;border-width:0.5px; border-color:#666; border-style:solid;padding:5px ">AIRTIME</td>
        <td  style="width:4%;border-width:0.5px; border-color:#666; border-style:solid;padding:5px ">'.$ccscore.'</td>
        </tr>
        <tr style="width:100%; height:20px;padding:0;  font-weight:normal  ">
        <td  style="width:4%;border-width:0.5px; border-color:#666; border-style:solid;padding:5px ">SENT TO</td>
        <td  style="width:4%;border-width:0.5px; border-color:#666; border-style:solid;padding:5px ">'.$ddscore.'</td>
        </tr>
        <tr style="width:100%; height:20px;padding:0;  font-weight:normal  ">
        <td  style="width:4%;border-width:0.5px; border-color:#666; border-style:solid;padding:5px ">RECEIVED</td>
        <td  style="width:4%;border-width:0.5px; border-color:#666; border-style:solid;padding:5px ">'.$eescore.'</td>
        </tr>
        <tr style="width:100%; height:20px;padding:0;  font-weight:normal  ">
        <td  style="width:4%;border-width:0.5px; border-color:#666; border-style:solid;padding:5px ">TRANSACTION COST</td>
        <td  style="width:4%;border-width:0.5px; border-color:#666; border-style:solid;padding:5px ">'.$ffscore.'</td>
        </tr>
        <tr style="width:100%; height:20px;color:#fff; background:#333; padding:0">
        <td  style="width:70%;padding:5px">TOTAL</td>
        <td  style="width:30%;padding:5px">'.$totscore.'%</td>  
        </tr>
        <tr style="width:100%; height:20px;color:#fff; background:#333; padding:0">
        <td  style="width:70%;padding:5px">CURRENT STATUS</td>
        <td  style="width:30%;padding:5px;color:#333;background:'.$col.'">'.$status.'</td>  
        </tr>
        <tr style="width:100%; height:20px;color:#fff; background:#333; padding:0">
        <td  style="width:70%;padding:5px">CURRENT OFFER</td>
        <td  style="width:30%;padding:5px">'.number_format($offer, 2, ".", "," ).'</td>  
        </tr>





        </tbody>
        </table>
';



break;
}
?>