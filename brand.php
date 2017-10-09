<?php require_once("sort_help.php") ?>
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
                $sql = "select brand_name from brand where brand_id='" . $fid . "'";
                $rs = mysql_query($sql);
                if ($rs && $row = mysql_fetch_array($rs))
                {
                    $brand_name = $row["brand_name"];
                    $sql = "delete from brand where brand_id='" . $fid . "'";
                    if (mysql_query($sql))
                    {
                        addHistory("删除", "品牌", $brand_name);
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
                $sql = "insert into brand(brand_name) values('" . $fname ."')";
                if (mysql_query($sql))
                {
                    addHistory("添加", "品牌", $fname);
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
                $sql = "select brand_name from brand where brand_id='" . $fid . "'";
                $rs = mysql_query($sql);
                if ($rs && $row = mysql_fetch_array($rs))
                {
                    $brand_name = $row["brand_name"];
                    $sql = "update brand set brand_name='" . $fname . "' where brand_id='" . $fid ."'";
                    if (mysql_query($sql))
                    {
                        addHistory("修改", "品牌", $fname . "(" . $brand_name . ")");
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
    function deleteBrand(fid, fname)
    {
        if (confirm("确定要删除'" + fname + "'吗？"))
        {
            brand.faction.value = 1;
            brand.fid.value = fid;
            brand.submit();
        }
    }
    function modifyBrand(fid, fname)
    {
        brand.faction.value = 3;
        brand.fid.value = fid;
        brand.fname.value = fname;
        brand.fsub.value = "修改";
        
        scroll(0, 0);
    }
    function clearForm()
    {
        brand.faction.value = 2;
        brand.fid.value = 0;
        brand.fname.value = "";
        brand.fsub.value = "增加";
    }
    function checkForm()
    {
        brand.fname.value = brand.fname.value.trim();
        if (brand.fname.value == "")
        {
            alert("输入名字");
            return false;
        }
        return true;
    }
</script>
<title>品牌</title>
</head>
<body>
<?php
    defineSortForm();
?>
    <form method="post" name="brand" action="./brand.php" onsubmit="return checkForm()" >
        <lable>品牌：</lable><input type="text" name="fname" />
        <input type="hidden" name="faction" value="2" />
        <input type="hidden" name="fid" value="0" />
        <input type="submit" name="fsub" value="增加" />
        <input type="reset" onclick="clearForm()" />
    </form>
    <table frame="border" rules="all">
        <tr>
<?php
            $page = "brand";
            $default_dir = array(1, 2);
            $headers = array("*", "10", "品牌", "300");
             generalHeader($page, $default_dir, $headers);
?>
        </tr>
<?php
        $sql = "select * from brand ";
        $sort_key = array("brand_id", "brand_name");
        $sql = $sql . appendSortSql($page, $default_dir, $sort_key);
        $rs = mysql_query($sql);
		if ($rs)
		{
			while ($row = mysql_fetch_array($rs))
			{
				  echo "<tr><td><a href='javascript:void(0)' onclick='deleteBrand(" 
					  . $row["brand_id"]. ", \"" . $row["brand_name"] . "\")'>X</a>";
				  echo "</td><td>";
				  echo "<a href='javascript:void(0)' onclick='modifyBrand("
					  . $row["brand_id"] . ", \"" . $row["brand_name"] . "\")'>"
					  . $row["brand_name"] . "</a>";
				  echo "</td></tr>";
			}
		}
?>
    </table>
</body>
</html>
