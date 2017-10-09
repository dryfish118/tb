<?php require_once("conn.php") ?>
<?php
    $page = isset($_POST["fpage"]) ? $_POST["fpage"] : "";
    $id = isset($_POST["fid"]) ? $_POST["fid"] : 0;
    $forder = isset($_POST["forder"]) ? $_POST["forder"] : 0;
    $delta = isset($_POST["fdelta"]) ? $_POST["fdelta"] : 0;
	
    $order = $forder - $delta;
    mysql_query("begin");
    $sql = "update $page set " . $page . "_order = " . $page . "_order";
    if ($delta >= 0)
    {
        $sql .= " + 1 where " . $page . "_order >= $order and " . $page . "_order < $forder";
    }
    else
    {
        $sql .= " - 1 where " . $page . "_order > $forder and " . $page . "_order <= $order";
    }
    if (mysql_query($sql))
    {
        $sql = "update $page set " . $page . "_order = $order where " . $page . "_id = $id";
        if (mysql_query($sql))
        {
            mysql_query("commit");
            echo "<script type='text/javascript'>document.location.href = './$page.php'</script>";
        }
        else
        {
            mysql_query("rollback");
            echo $sql . "<br />" . $conn->connect_error . "<br />更新失败";
        }
    }
    else
    {
        mysql_query("rollback");
        echo $sql . "<br />" . $conn->connect_error . "<br />更新失败";
    }
?>
