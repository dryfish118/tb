<?php
    date_default_timezone_set('PRC');

    if (!isset($_COOKIE["user"]))
    {
        echo "<script type=\"text/javascript\">document.location.href=\"./login.php\";</script>";
    }
    $conn = new mysqli("localhost", "root", "admin", "dxx");
    if ($conn->connect_error)
    {
        die($conn->connect_error);
    }
    function addHistory($action, $table, $contents)
    {
        // $sql = "insert into history(history_user_id,history_action,history_table,history_contents) "
        //     . "values(\"" . $_COOKIE["user"] . "\",\"" . $action 
        //     . "\",\"" . $table . "\",\"" . $contents . "\")";
        // global $conn;
        // if (!$conn->query($sql))
        // {
        //     die($sql . "<br />" . $conn->connect_error);
        // }
    }

    define("OPERATOR_INVALID",  0);
    define("OPERATOR_ADD",      1);
    define("OPERATOR_REMOVE",   2);
    define("OPERATOR_MODIFY",   3);
?>