<?php

$GLOBALS["wallet_ip"] = "ip"; //wallet IP
$GLOBALS["wallet_port"] = "port"; //Wallet Port
$GLOBALS["wallet_user"] = "un"; //Wallet Username
$GLOBALS["wallet_pass"] = "pw"; //wallet PW

function validatepubkey($pubkey) {
    $request_array["method"] = "validatepubkey";
    $request_array["params"][0] = $pubkey;
    $info = wallet_fetch ($request_array);
    return ($info);
}
function getblockbynumber($index) {					//
    $request_array["method"] = "getblockbynumber";	//
    $request_array["params"][0] = $index;			//
    $info = wallet_fetch ($request_array);			//
    return ($info);									//Get block by height
}
function gettx($txid) {
    $request_array["method"] = "gettransaction";	//
    $request_array["params"][0] = $txid;			//
    $info = wallet_fetch ($request_array);			//
    return ($info);									//get transaction by hash
}
function getblockcount() {
    $request_array["method"] = "getblockcount";		//
    $info = wallet_fetch ($request_array);			//
    return ($info);									//get blockcount
}
function getinfo() {
	$request_array["method"] = "getinfo";			//
	$info = wallet_fetch ($request_array);			//
    return ($info);									//get daemoninfo
}
function wallet_fetch ($request_array) {														//
    $request = json_encode ($request_array);													//json_encode request
    $coind = curl_init();																		//init curl
    curl_setopt ($coind, CURLOPT_URL, $GLOBALS["wallet_ip"]);									//set curl ip
    curl_setopt ($coind, CURLOPT_PORT, $GLOBALS["wallet_port"]);								//set curl port
    curl_setopt($coind, CURLOPT_HTTPAUTH, CURLAUTH_BASIC) ;										//set curl basic auth
    curl_setopt($coind, CURLOPT_USERPWD, $GLOBALS["wallet_user"].":".$GLOBALS["wallet_pass"]);	//set curl un/pw
    curl_setopt($coind, CURLOPT_HTTPHEADER, array ("Content-type: application/json"));			//set curl return json
    curl_setopt($coind, CURLOPT_POST, TRUE);													//set curl post true
    curl_setopt($coind, CURLOPT_POSTFIELDS, $request);											//set curl post fields
    curl_setopt($coind, CURLOPT_RETURNTRANSFER, TRUE);											//
    curl_setopt($coind, CURLOPT_SSL_VERIFYPEER, FALSE);											//
    curl_setopt($coind, CURLOPT_SSL_VERIFYHOST, FALSE);											//verify host
    $response_data = curl_exec($coind);															//execute curl
    curl_close($coind);																			//close curl
    $info = json_decode ($response_data, TRUE);													//json_decode response
    if (isset ($info["error"]) || $info["error"] != "")
    {
        return $info["error"]["message"]."(Error Code: ".$info["error"]["code"].")";
    }
    else
    {
        return $info["result"];
    }
}
?>
