<?php require_once("sort_help.php") ?>
<?php
    $fcat1_id = isset($_POST["fcat1_id"]) ? $_POST["fcat1_id"] : 0;
    $faction = isset($_POST["faction"]) ? $_POST["faction"] : 0;
    $fid = isset($_POST["fid"]) ? $_POST["fid"] : 0;
    $fname = isset($_POST["fname"]) ? $_POST["fname"] : "";
    
    function getCat1Id()
    {
        if (isset($_GET["cat1_id"]) && $_GET["cat1_id"] != 0)
        {
            return $_GET["cat1_id"];
        }
        else if (isset($_POST["fcat1_id"]) && $_POST["fcat1_id"] != 0)
        {
            return $_POST["fcat1_id"];
        }
        else
        {
            $sql = "select min(cat1_id) as id from cat1";
            $rs = mysql_query($sql);
			if ($rs)
			{
				if ($row = mysql_fetch_array($rs))
				{
					return $row["id"];
				}
			}
        }
        return 0;
    }
    
    if ($faction != 0)
    {
        if ($faction == 1)
        {
            if ($fid != 0)
            {
                $sql = "select cat2_name from cat2 where cat2_id='" . $fid . "'";
                $rs = mysql_query($sql);
                if ($rs && $row = mysql_fetch_array($rs))
                {
                    $cat2_name = $row["cat2_name"];
                    $sql = "delete from cat2 where cat2_id='" . $fid . "'";
                    if (mysql_query($sql))
                    {
                        addHistory("删除", "小类", $cat2_name);
                    }
                    else
                    {
                        die($sql . "<br />" . $conn->connect_error . "<br />删除失败");
                    }
                }
                else
                {
                    die($sql . "<br />" . $conn->connect_error . "<br />删除失败");
                }
            }
        }
        else if ($faction == 2)
        {
            if ($fname != "")
            {
                $sql = "insert into cat2(cat2_cat1_id, cat2_name) values(" . $fcat1_id . ",'" . $fname ."')";
                if (mysql_query($sql))
                {
                    addHistory("添加", "小类", $fname);
                }
                else
                {
                    die($sql . "<br />" . $conn->connect_error . "<br />添加失败");
                }
            }
        }
        else if ($faction == 3)
        {
            if ($fid != 0 && $fname != "")
            {
                $sql = "select cat2_name from cat2 where cat2_id='" . $fid . "'";
                $rs = mysql_query($sql);
                if ($rs && $row = mysql_fetch_array($rs))
                {
                    $cat2_name = $row["cat2_name"];
                    $sql = "update cat2 set cat2_name='" . $fname . "' where cat2_id='" . $fid ."'";
                    if (mysql_query($sql))
                    {
                        addHistory("修改", "小类", $fname . "(" . $cat2_name . ")");
                    }
                    else
                    {
                        die($sql . "<br />" . $conn->connect_error . "<br />修改失败");
                    }
                }
                else
                {
                    die($sql . "<br />" . $conn->connect_error . "<br />修改失败");
                }
            }
        }
        else
        {
            die("未知操作");
        }
    }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-cn" />
<script type="text/javascript" src="./dxx.js" ></script>
<script type="text/javascript">
    function jsGetCat1Id()
    {
        return <?php echo getCat1Id(); ?>;
    }
    function deleteCat2(fid, fname)
    {
        if (confirm("确定要删除'" + fname + "'吗？"))
        {
            cat2.faction.value = 1;
            cat2.fid.value = fid;
            cat2.submit();
        }
    }
    function modifyCat2(fid, fname)
    {
        cat2.faction.value = 3;
        cat2.fid.value = fid;
        cat2.fname.value = fname;
        cat2.fsub.value = "修改";
        
        scroll(0, 0);
    }
    function clearForm()
    {
        cat2.faction.value = 2;
        cat2.fid.value = 0;
        cat2.fname.value = "";
        cat2.fsub.value = "增加";
    }
    function checkForm()
    {
        cat2.fname.value = cat2.fname.value.trim();
        if (cat2.fname.value == "")
        {
            alert("输入小类");
            return false;
        }
        return true;
    }
</script>
<title>小类</title>
</head>
<body>
<?php
    defineSortForm();
?>
    <lable>大类：
<?php
        $sql = "select cat1_name from cat1 where cat1_id=" . getCat1Id();
        $rs = mysql_query($sql);
        if ($rs)
        {
            if ($row = mysql_fetch_array($rs))
            {
                echo $row["cat1_name"];
            }
        }
?>
    </lable>
    <form method="post" name="cat2" action="./cat2.php" onsubmit="return checkForm()" >
        <lable>小类：</lable><input type="text" name="fname" />
        <input type="hidden" name="faction" value="2" />
        <input type="hidden" name="fid" value="0" />
        <input type="hidden" name="fcat1_id" value=<?php echo getCat1Id(); ?> />
        <input type="submit" name="fsub" value="增加" />
        <input type="reset" onclick="clearForm()" />
    </form>
    <table frame="border" rules="all">
        <tr>
<?php
            $page = "cat2";
            $default_dir = array(1, 2);
            $headers = array("*", "10", "小类", "300");
             generalHeader($page, $default_dir, $headers);
?>
        </tr>
<?php
        $sql = "select * from cat2 where cat2_cat1_id=" . getCat1Id() . " ";
        $sort_key = array("", "cat2_name");
        $sql = $sql . appendSortSql($page, $default_dir, $sort_key);
        $rs = mysql_query($sql);
		if ($rs)
		{
			while ($row = mysql_fetch_array($rs))
			{
				echo "<tr><td><a href='javascript:void(0)' onclick='deleteCat2(" 
					  . $row["cat2_id"]. ", \"" . $row["cat2_name"] . "\")'>X</a>";
				  echo "</td><td>";
				  echo "<a href='javascript:void(0)' onclick='modifyCat2("
					  . $row["cat2_id"] . ", \"" . $row["cat2_name"] . "\")'>"
					  . $row["cat2_name"] . "</a>";
				  echo "</td></tr>";
			}
		}
?>
    </table>
</body>
</html>