<?php 
    require_once("conn.php");
    require_once("sort_help.php");

    // 初始化变量
    $page = isset($_POST["fpage"]) ? $_POST["fpage"] : "";
    $column = isset($_POST["fcolumn"]) ? $_POST["fcolumn"] : "";
    $direction = isset($_POST["fdirection"]) ? $_POST["fdirection"] : "";
    
    // 查找排序数据
    $sql = "select fld_sort_id, fld_value from tbl_sort where fld_user_id = " 
        . $_COOKIE["user"] . " and fld_table_name = '$page'";
    $rs = mysql_query($sql);
    $sort_string;
    if ($rs && $row = mysql_fetch_array($rs))
    {
        $sort_string = $row["fld_value"];
        $sort_data = explode(";", $sort_string);
        $sort_string = "$column,$direction";
        for ($i = 0; $i < count($sort_data); $i++)
        {
            $sort_head = explode(",", $sort_data[$i]);
            if ($sort_head[0] != $column)
            {
                $sort_string = "$sort_string;" . $sort_data[$i];
            }
        }
        $sql = "update tbl_sort set fld_value = '$sort_string' where fld_sort_id = " . $row["fld_sort_id"];
        mysql_query($sql);
    }
?>
<script type='text/javascript'>
    document.location.href = "./<?php echo $page; ?>.php";
</script>
