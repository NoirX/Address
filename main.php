<?php
include('info.php');
$mysqli = new mysqli('ip/url', 'mysqlUN', 'mysqlPW', 'DB');

$height=getblockcount(); //current height
$count=$mysqli->query("SELECT COUNT(*) as last FROM last")->fetch_object()->last; 	//
if($count!=3){																		//
    $mysqli->query("REPLACE INTO last(value, last) VALUES('tx',1)");					//
    $mysqli->query("REPLACE INTO last(value, last) VALUES('block',0)");					//	setup last value table if
    $mysqli->query("REPLACE INTO last(value, last) VALUES('address',1)");				//  not present
}
$blocklast = intval($mysqli->query("SELECT last FROM last WHERE value = 'block'")->fetch_object()->last); //lastblock
$txlast = intval($mysqli->query("SELECT last FROM last WHERE value = 'tx'")->fetch_object()->last); //lasttxid
//echo $blocklast."\n";

while($height!=$blocklast) { //start block loop
    //echo $blocklast."\n";
    $array = getblockbynumber($blocklast); //getblock info
    $b1 = $array['hash']; //gethash
    $b2 = $array['merkleroot']; //getmerkleroot
    $b3 = $array['flags']; //get flags
    $b4 = $array['tx']; //get txarray

    foreach($b4 as $tx) { //start transaction loop
        $mysqli->query("REPLACE INTO block_tx (block_id, tx_hash, tx_id) VALUES('$blocklast', '$tx', '$txlast')"); //insert block 
        $txarray = gettx($tx);
        $vin = $txarray['vin'];
        foreach(array_keys($vin) as $k1) { //txin loop
            $cell=array_keys($vin[$k1])[0]; //get key name
			$txin_pubkey = NULL;
			$tx_source_hash = $vin[$k1][$cell];
            if(isset($vin[$k1]['scriptSig'])){
			$txin_pubkey = substr($vin[$k1]['scriptSig']['asm'], strpos($vin[$k1]['scriptSig']['asm'], " "));
			} //if pubkey input
            $mysqli->query("REPLACE INTO txin (tx_id, txin_pubkey, tx_source_hash) VALUES('$txlast','$txin_pubkey','$tx_source_hash')");//store txin
        }
        foreach($txarray['vout'] as $voutarray){ //txout loop
            $txout_value = $voutarray['value']; //value
            $txout_n = $voutarray['n']; //n
            $address = $voutarray['scriptPubKey']['addresses'][0]; //address
            $mysqli->query("REPLACE INTO txout (tx_id, txin_hash, txout_value, tx_n, address) VALUES('$txlast','$tx','$txout_value','$txout_n','$address')"); //store txout
        }
        $txlast=$txlast+1; 													//increase and update txlast
        $mysqli->query("UPDATE last SET last='$txlast' WHERE value='tx'");  //
    }
    $mysqli->query("REPLACE INTO block (block_id,block_hash,block_hashMerkleRoot,block_type) VALUES('$blocklast','$b1','$b2','$b3')"); // store block info
    $blocklast=$blocklast+1; 												//increase and update lastblock
    $mysqli->query("UPDATE last SET last='$blocklast' WHERE value='block'");//
}
echo 'Block Update done'."\n";