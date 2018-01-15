<?php

	date_default_timezone_set('PRC');
	
    if (!isset($_COOKIE["user"]))
    {
        echo "<script type=\"text/javascript\">document.location.href=\"./login.php\";</script>";
    }
    $conn = mysql_connect("localhost", "root", "admin");
	mysql_select_db("dxx", $conn);
    mysql_query("set character set 'utf8'");
    
    function addHistory($action, $table, $contents)
    {

    }
    
    define("OPERATOR_INVALID",  0);
    define("OPERATOR_ADD",      1);
    define("OPERATOR_REMOVE",   2);
    define("OPERATOR_MODIFY",   3);
?>
