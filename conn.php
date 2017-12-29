<?php
    date_default_timezone_set('PRC');

    $conn = new mysqli("localhost", "root", "admin", "dxx");
    if ($conn->connect_error)
    {
        die($conn->connect_error);
    }
    mysqli_query($conn, "set names utf8");
    
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
?>