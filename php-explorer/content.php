<?php
$GLOBALS["mysqli"] = new mysqli('ip/url', 'mysqlUN', 'mysqlPW', 'DB');

function site_header ($title)
{
    echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n\n";
    echo "<head>\n\n";
    echo "	<title>".$title."</title>\n\n";
    echo "	<link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\">\n\n";
    echo "</head>\n";
    echo "<body>\n";
    echo "\n";
    echo "		<div id=\"site_head_logo\">\n";
    echo "			<img src=\"logo.png\" height=\"64px\" width=\"64px\">\n";
    echo "		</div>\n";
    echo "		<div id=\"header\">\n";
    echo "\n";
    echo "			<h2><a href=\"".$_SERVER["PHP_SELF"]."\" title=\"Home Page\">\n";
    echo "				NoirShares Address Explorer\n";
    echo "			</a></h2>\n";
    echo "\n";
    echo "		</div>\n";
    echo "      <div id=\"content\">\n";
    echo "\n";
    echo "\n";
}

function site_footer ()
{
    echo "	</div>\n";
    echo "\n";

    echo "	<div class=\"footer\">\n";
    echo "\n";

    echo "<p>  	PHP Address Explorer by Russel Waters for NoirShares \n<br>";
    echo "		NRS Donations: 9fkmW1TuFG2RxzjbtEvLpqyQD5Eu9j4rLx \n<br>";
    echo "		BTC Donations: 19GGjd7Vn9J7fRpgsXVnQENN7pV4eJcqVD</p></div>";
    echo "\n";

    echo "</body>\n";
    echo "</html>";
    exit;
}

function start()
{
    site_header("PHP Address Explorer");
	echo "	<div class=\"menu_desc\">\n";
    echo "			<span>Enter Address</span><br>\n";
    echo "			<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"post\">\n";
    echo "			<input type=\"text\" name=\"address\" size=\"40\">\n";
    echo "			<input type=\"submit\" name=\"submit\" value=\"Jump To Address\">\n";
    echo "			</form>\n";
	echo " </div>";
    echo "\n";

}

function address_detail($addr)
{
    $amounts=tally($addr);
    $amount=$amounts[count($amounts)-1];
    $tx=count($amounts)-1;
    echo "	<div class=\"address_head\">\n";
    echo "\n";

    echo "		<div class=\"address_head_left\">\n";
    echo "			Address: ".$addr."<br>\n";
    echo "          Transactions: ".$tx."\n";
    echo "		</div>\n";
    echo "\n";

    echo "		<div class=\"address_head_right\">\n";
    echo $amount[0][0]."<br>".$amount[1][0]."<br>".$amount[2][0]."\n";
    echo "		</div>\n";
    echo "\n";
    echo "</div>\n";

    echo "      <div class=\"address_content\"> \n";
    echo "      <div class=\"address_detail\"> \n";
    echo "<table id=\"addr\">";
    echo "<tr><th>Transaction</th><th>Block</th><th>Time</th><th>Amount</th><th>Balance</th></tr>";
    $test=table($addr);
    $bal=0;
    foreach(array_keys($test) as $thi){
        foreach(array_keys($test[$thi]) as $tha){
            $$tha =$test[$thi][$tha];
        }
        $hash=$GLOBALS["mysqli"]->query("select tx_hash from block_tx where tx_id='$tx_id'")->fetch_object()->tx_hash;
        $d1=gettx($hash);
        $epoch= $d1['time'];
        $date = gmdate('r', $epoch);
        $block=$GLOBALS["mysqli"]->query("select block_id from block_tx where tx_id='$tx_id'")->fetch_object()->block_id;
        $blockhash=$GLOBALS["mysqli"]->query("select block_hash from block where block_id='$block'")->fetch_object()->block_hash;
        $txin=$GLOBALS["mysqli"]->query("select tx_id from txin where tx_source_hash = '$txin_hash' and txin_address='$addr'")->fetch_object()->tx_id;

        $bal=$bal+$txout_value;
        echo "<tr><td><a href=\"http://nrs.argakiig.us/index.php?transaction=".$hash."\">".truncate($hash)."</a></td><td><a href=\"http://nrs.argakiig.us/index.php?block_hash=".$blockhash."\">".$block."</a></td><td>".$date."</td><td>".$txout_value."</td><td>".$bal."</td></tr>\n";
    }
    echo "</table></div>\n</div>\n";

}

function address($addr){
    $result = $GLOBALS["mysqli"]->query("SELECT tx_id,txout_value,txin_hash from txout where address = '$addr'");
    $test=mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $test;
}

function truncate($text, $chars = 15) {
    if(strlen($text) <= $chars) return $text;
    $text = substr($text,0,$chars)."...";
    return $text;
}

function tally($addr)
{
    $sum=0;
    $out=0;
    $tot=0;
    $test=address($addr);
    foreach(array_keys($test) as $thi){
        foreach(array_keys($test[$thi]) as $tha){
            $$tha =$test[$thi][$tha];
        }
        $hash=$GLOBALS["mysqli"]->query("select tx_hash from block_tx where tx_id='$tx_id'")->fetch_object()->tx_hash;
        $d1=gettx($hash);
        $epoch= $d1['time'];
        $date = gmdate('r', $epoch);
        $outs= json_encode(array("Transaction" => $hash,
                     "Date:" => $date,
                     "Amount:" => $txout_value));


        $txin=$GLOBALS["mysqli"]->query("select tx_id from txin where tx_source_hash = '$txin_hash' and txin_address='$addr'");
        $tot=$tot+$txout_value;
        if(null!==$txin->fetch_row()){
            $num=$txin->fetch_row();
//        echo $outs." ".$num." spent";
            $final[] =array($outs." ".json_encode(array(" spent")));
            $out=$out+$txout_value;
        }else{
//        echo $outs." "."unspent";
            $sum= $sum+$txout_value;
            $final[] =array($outs." ".json_encode(array("unspent")));
        }
        //echo"\n";
    }
    $tally =array(array("Unspent ".$sum),array("Spent ".$out),array("Total ".$tot));
//echo $tally;
    $final[] =$tally;
    return $final;
}

function incoming($addr){
    $result = $GLOBALS["mysqli"]->query("SELECT tx_id,txout_value,txin_hash from txout where address = '$addr'");
    $test=mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $test;
}

function outgoing($addr){

    $result =$GLOBALS["mysqli"]->query("SELECT tx_id,tx_source_hash,tx_value from txin where txin_address = '$addr'");
    $test=mysqli_fetch_all($result, MYSQLI_ASSOC);
$size=count($test);
$a=0;
while($a!=$size){

        $tx_id=$test[$a]['tx_id'];
        $txin_hash2=$GLOBALS["mysqli"]->query("Select tx_hash as tx_hash from block_tx where tx_id='$tx_id'")->fetch_object()->tx_hash;
        $val=0-$test[$a]['tx_value'];
        $new=array("tx_id"=> $tx_id,
               "txout_value"=> (int)$val,
               "txin_hash"=> $txin_hash2);
    //print_r($new);
        $tests[]=$new;
    $a=$a+1;
    }
    return $tests;


}function mul_sort($a,$b)
{
    if($a['tx_id'] > $b['tx_id'])

        return 1;//here,if you return -1,return 1 below,the result will be descending

    if($a['tx_id'] < $b['tx_id'])

        return -1;

    if($a['tx_id'] == $b['tx_id'])

        return 0;
}
function table($addr){
    $in=incoming($addr);
    $out=outgoing($addr);
    $new=array_merge($in,$out);
    uasort($new,'mul_sort');
    return $new;
}