<?php
require_once('info.php');
$mysqli = new mysqli('ip/url', 'mysqlUN', 'mysqlPW', 'DB');
$amount = $mysqli->query("select tx_id, tx_source_hash, txin_address from txin where tx_value is null and txin_address!=''");
$amount2= mysqli_fetch_all($amount,MYSQLI_ASSOC);
foreach($amount2 as $import){
    $tx_id=$import['tx_id'];
    $tx_hash=$import['tx_source_hash'];
    $address=$import['txin_address'];
    $value=$mysqli->query("select txout_value from txout where txin_hash='$tx_hash' and address='$address'")->fetch_object()->txout_value;
    $mysqli->query("update txin set tx_value='$value' where tx_id='$tx_id' and tx_source_hash='$tx_hash' and txin_address='$address'");
//    print_r($import);
}
echo 'done';
