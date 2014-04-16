<?php
require_once("info.php");
require_once("content.php");



if (isset ($_REQUEST["address"]))
{
    site_header ("Address Details");

    address_detail($_REQUEST["address"]);
}
elseif (isset ($_REQUEST["BagHolder"]))
{
	site_header ("BagHolders");
	bagholder();
}
else
{
    start();
}
site_footer();
?>