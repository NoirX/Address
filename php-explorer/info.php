<?php

$GLOBALS["wallet_ip"] = "ip"; //wallet IP
$GLOBALS["wallet_port"] = "port"; //Wallet Port
$GLOBALS["wallet_user"] = "un"; //Wallet Username
$GLOBALS["wallet_pass"] = "pw"; //wallet PW

function output($x) {
    $f = sprintf('%0.08f', $x);
    $f = rtrim($f,'0');
    $f = rtrim($f,'.');
    return $f;
}

function getblockbynumber($index) {
    $request_array["method"] = "getblockbynumber";
    $request_array["params"][0] = $index;
    $info = wallet_fetch ($request_array);
    return ($info);
}

function gettx($txid) {
    $request_array["method"] = "gettransaction";
    $request_array["params"][0] = $txid;
    $info = wallet_fetch ($request_array);
    return ($info);
}

function validatepubkey($pubkey) {
    $request_array["method"] = "validatepubkey";
    $request_array["params"][0] = $pubkey;
    $info = wallet_fetch ($request_array);
    return ($info);
}

function getblockcount() {
    $request_array["method"] = "getblockcount";
    $info = wallet_fetch ($request_array);
    return ($info);
}
function wallet_fetch ($request_array) {
    $request = json_encode ($request_array);
    $coind = curl_init();
    curl_setopt ($coind, CURLOPT_URL, $GLOBALS["wallet_ip"]);
    curl_setopt ($coind, CURLOPT_PORT, $GLOBALS["wallet_port"]);
    curl_setopt($coind, CURLOPT_HTTPAUTH, CURLAUTH_BASIC) ;
    curl_setopt($coind, CURLOPT_USERPWD, $GLOBALS["wallet_user"].":".$GLOBALS["wallet_pass"]);
    curl_setopt($coind, CURLOPT_HTTPHEADER, array ("Content-type: application/json"));
    curl_setopt($coind, CURLOPT_POST, TRUE);
    curl_setopt($coind, CURLOPT_POSTFIELDS, $request);
    curl_setopt($coind, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($coind, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($coind, CURLOPT_SSL_VERIFYHOST, FALSE);
    $response_data = curl_exec($coind);
    curl_close($coind);
    $info = json_decode ($response_data, TRUE);
    if (isset ($info["error"]) || $info["error"] != "")
    {
        return $info["error"]["message"]."(Error Code: ".$info["error"]["code"].")";
    }
    else
    {
        return $info["result"];
    }
}
	function getblock ($block_hash)
	{
	//	The JSON-RPC request starts with a method name
		$request_array["method"] = "getblock";
	
	//	For getblock a block hash is required	
		$request_array["params"][0] = $block_hash;
	
	//	Send the request to the wallet
		$info = wallet_fetch ($request_array);
		
	//	This function returns an array containing the block 
	//	data for the specified block hash
		return ($info);
	}
	
	function getblockhash ($block_index)
	{
	//	The JSON-RPC request starts with a method name
		$request_array["method"] = "getblockhash";
	
	//	For getblockhash a block index is required	
		$request_array["params"][0] = $block_index;
	
	//	Send the request to the wallet
		$info = wallet_fetch ($request_array);
		
	//	This function returns a string containing the block 
	//	hash value for the specified block in the chain
		return ($info);
	}
	
	function getinfo () 
	{
	//	The JSON-RPC request starts with a method name
		$request_array["method"] = "getinfo";
	
	//	getinfo has no parameters
	
	//	Send the request to the wallet
		$info = wallet_fetch ($request_array);
		
	//	This function returns an array containing information
	//	about the wallet's network and block chain
		return ($info);
	}
	function getdifficulty()
	{
	//	The JSON-RPC request starts with a method name
		$request_array["method"] = "getdifficulty";

	//	This function returns an array containing information
	//	about the wallet's network and block chain
		return($info);
	}
	function getnetworkhashps ($block_index=NULL)
	{
	//	The JSON-RPC request starts with a method name
		$request_array["method"] = "getnetworkhashps";
	
	//	block index is an optional parameter. If no block
	//	index is specified you get the network hashrate for 
	//	the latest block
		
		if (isset ($block_index))
		{
			$request_array["params"][0] = $block_index;
		}
		
	//	Send the request to the wallet
		$info = wallet_fetch ($request_array);
		
	//	This function returns a string containing the calculated
	//	network hash rate for the latest block
		return ($info);
	}
	
	function getrawtransaction ($tx_id, $verbose=1)
	{
	//	The JSON-RPC request starts with a method name
		$request_array["method"] = "getrawtransaction";
	
	//	For getrawtransaction a txid is required	
		$request_array["params"][0] = $tx_id;
		$request_array["params"][1] = $verbose;
	
	//	Send the request to the wallet
		$info = wallet_fetch ($request_array);
		
	//	This function returns a string containing the block 
	//	hash value for the specified block in the chain
		return ($info);
	}
	function getPrice()
	{
	$opts = array('http' =>
                                array(
                                        'method'  => 'GET',
                                        'timeout' => 10
                                )
                        );
                        $context = stream_context_create($opts);
                        $feed = file_get_contents('https://poloniex.com/public?command=returnTicker', false, $context);
                        $json = json_decode($feed, true);
						$jdata = $json['BTC_NRS']['last'];
                        return $jdata;
	}
	function btc()
	{
	$opts = array('http' =>
                                array(
                                        'method'  => 'GET',
                                        'timeout' => 10
                                )
                        );
                        $context = stream_context_create($opts);
                        $feed = file_get_contents('https://btc-e.com/api/2/btc_usd/ticker', false, $context);
                        $json = json_decode($feed, true);
						$jdata = $json['ticker']['last'];
                        return $jdata;
	}
?>
