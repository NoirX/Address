<?php

include('info.php');
if (!isset ($_REQUEST["request"]) || $_REQUEST["request"] == "")
{
    bcapi_error (0, "address after equals");
}
$addr = urldecode ($_REQUEST["request"]);


$GLOBALS["mysqli"] = new mysqli('ip/url', 'mysqlUN', 'mysqlPW', 'DB');
$sum=0;
$out=0;
$tot=0;
function address($addr){
    $result = $GLOBALS["mysqli"]->query("SELECT tx_id,txout_value,txin_hash from txout where address = '$addr'");
    $test=mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $test;
}

$test=address($addr);
foreach(array_keys($test) as $thi){
    foreach(array_keys($test[$thi]) as $tha){
        $$tha =$test[$thi][$tha];
    }
    $hash=$mysqli->query("select tx_hash from block_tx where tx_id='$tx_id'")->fetch_object()->tx_hash;
    $d1=gettx($hash);
    $epoch= $d1['time'];
    $date = gmdate('r', $epoch);
    $outs= "Transaction: ".$hash." Date: ".$date." Amount: ".$txout_value;


    $txin=$mysqli->query("select tx_id from txin where tx_source_hash = '$txin_hash' and txin_address='$addr'");
    $tot=$tot+$txout_value;
    if(null!==$txin->fetch_row()){
        $num=$txin->fetch_row();
//        echo $outs." ".$num." spent";
        $final[] =array($outs." ".$num." spent");
        $out=$out+$txout_value;
    }else{
//        echo $outs." "."unspent";
        $sum= $sum+$txout_value;
        $final[] =array($outs." "."unspent");
    }
    //echo"\n";
}
$tally =array("Unspent ".$sum." Sent ".$out." Total".$tot);
//echo $tally;
$final[] =$tally;

print_r(json_encode($final));

function bcapi_error ($code, $message)
	{
		$error["code"] = $code;
		$error["message"] = $message;
		
		print_r (json_encode($error));
		exit;
	}
?>
