<?php
include('info.php');
$mysqli = new mysqli('ip/url', 'mysqlUN', 'mysqlPW', 'DB');
$result=$mysqli->query("select tx_source_hash, txin_pubkey, tx_in from txin where txin_pubkey!='' and txin_address is null");
$arrays=mysqli_fetch_all($result, MYSQLI_ASSOC);
//$new=$mysqli->query("select tx_source_hash, txin_pubkey from txin where tx_in=782")->fetch_row();

foreach($arrays as $new){
    $pubkey=$new['txin_pubkey'];
    $previous=$new['tx_source_hash'];
    $id=$new['tx_in'];
    $tail=validatepubkey($pubkey);
    if(isset($tail['address'])){
        //echo $tail['address'];
        $addr=$tail['address'];
        //$mysqli->query("replace into txin(txin_address) values('$addr') where tx_in='$id'");
        $mysqli->query("update txin set txin_address='$addr' where tx_in='$id'");
    }else{
        $out=$mysqli->query("select address from txout where txin_hash='$previous'")->fetch_object()->address;
        //echo $out;
        //$mysqli->query("replace into txin(txin_address) values('$out') where tx_in='$id'");
        $mysqli->query("update txin set txin_address='$out' where tx_in='$id'");
    }
}
echo 'Public Key done'."\n";