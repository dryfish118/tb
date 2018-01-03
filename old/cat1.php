<?php require_once("sort_help.php"); ?>
<?php
    $fname = isset($_POST["fname"]) ? $_POST["fname"] : "";
    $faction = isset($_POST["faction"]) ? $_POST["faction"] : 0;
    $fid = isset($_POST["fid"]) ? $_POST["fid"] : 0;
    
    if ($faction != 0)
    {
        if ($faction == 1)
        {
            if ($fid != 0)
            {
                $sql = "select cat1_name from cat1 where cat1_id='" . $fid . "'";
                $rs = mysql_query($sql);
                if ($rs && $row = mysql_fetch_array($rs))
                {
                    $cat1_name = $row["cat1_name"];
                    $sql = "delete from cat1 where cat1_id='" . $fid . "'";
                    if (mysql_query($sql))
                    {
                        addHistory("删除", "大类", $cat1_name);
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
                $sql = "insert into cat1(cat1_name) values('" . $fname ."')";
                if (mysql_query($sql))
                {
                    addHistory("添加", "大类", $fname);
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
                $sql = "select cat1_name from cat1 where cat1_id='" . $fid . "'";
                $rs = mysql_query($sql);
                if ($rs && $row = mysql_fetch_array($rs))
                {
                    $cat1_name = $row["cat1_name"];
                    $sql = "update cat1 set cat1_name='" . $fname . "' where cat1_id='" . $fid ."'";
                    if (mysql_query($sql))
                    {
                        addHistory("修改", "大类", $fname . "(" . $cat1_name . ")");
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
    function deleteCat1(fid, fname)
    {
        if (confirm("确定要删除'" + fname + "'吗？"))
        {
            cat1.faction.value = 1;
            cat1.fid.value = fid;
            cat1.submit();
        }
    }
    function modifyCat1(fid, fname)
    {
        cat1.faction.value = 3;
        cat1.fid.value = fid;
        cat1.fname.value = fname;
        cat1.fsub.value = "修改";
        
        scroll(0, 0);
    }
    function clearForm()
    {
        cat1.faction.value = 2;
        cat1.fid.value = 0;
        cat1.fname.value = "";
        cat1.fsub.value = "增加";
    }
    function checkForm()
    {
        cat1.fname.value = cat1.fname.value.trim();
        if (cat1.fname.value == "")
        {
            alert("输入大类");
            return false;
        }
        return true;
    }
</script>
<title>大类</title>
</head>
<body>
<?php
    defineSortForm();
?>
    <form method="post" name="cat1" action="./cat1.php" onsubmit="return checkForm()" >
        <lable>大类：</lable><input type="text" name="fname" />
        <input type="hidden" name="faction" value="2" />
        <input type="hidden" name="fid" value="0" />
        <input type="submit" name="fsub" value="增加" />
        <input type="reset" onclick="clearForm()" />
    </form>
    <table frame="border" rules="all">
        <tr>
<?php
            $page = "cat1";
            $default_dir = array(1, 2, 0);
            $headers = array("*", "10", "大类", "300", "小类", "40");
             generalHeader($page, $default_dir, $headers);
?>
        </tr>
<?php
        $sql = "select * from cat1 ";
        $sort_key = array("", "cat1_name");
        $sql = $sql . appendSortSql($page, $default_dir, $sort_key);
        $rs = mysql_query($sql);
		if ($rs)
		{
			while ($row = mysql_fetch_array($rs))
			{
				echo "<tr><td><a href='javascript:void(0)' onclick='deleteCat1(" 
					  . $row["cat1_id"]. ", \"" . $row["cat1_name"] . "\")'>X</a>";
				  echo "</td><td>";
				  echo "<a href='javascript:void(0)' onclick='modifyCat1("
					  . $row["cat1_id"] . ", \"" . $row["cat1_name"] . "\")'>" . $row["cat1_name"] . "</a>";
				  echo "</td><td>";
				  echo "<a href='./cat2.php?cat1_id=" . $row["cat1_id"] . "' target='cat2'>进入</a>";
				  echo "</td></tr>";
			}
		}
?>
    </table>
</body>
</html>