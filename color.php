<?php require_once("colordata.php") ?>
<?php
    $operator = isset($_POST["foperator"]) ? $_POST["foperator"] : OPERATOR_INVALID;
    $id = isset($_POST["fid"]) ? $_POST["fid"] : 0;
    $name = isset($_POST["fname"]) ? $_POST["fname"] : "";

    if ($operator == OPERATOR_REMOVE)
    {
        if ($id != 0)
        {
            $sql = "select color_name, color_order from color where color_id = $id";
            $rs = mysql_query($sql);
            if ($rs && $row = mysql_fetch_array($rs))
            {
                $color_name = $row["color_name"];
                $color_order = $row["color_order"];
                mysql_query("begin");
                $sql = "update color set color_order = color_order - 1 where color_order > $color_order";
                if (!mysql_query($sql))
                {
                    mysql_query("rollback");
                    die($sql . "<br />" . $conn->connect_error . "<br />删除失败");
                }
                $sql = "delete from color where color_id = $id";
                if (mysql_query($sql))
                {
                    mysql_query("commit");
                    addHistory("删除", "颜色", $color_name);
                }
                else
                {
                    mysql_query("rollback");
                    die($sql . "<br />" . $conn->connect_error . "<br />删除失败");
                }
            }
            else
            {
                die($sql . "<br />" . $conn->connect_error . "<br />删除失败");
            }
        }
    }
    else if ($operator == OPERATOR_ADD)
    {
        if ($name != "")
        {
            $sql = "select max(color_order) + 1 as color_max from color";
            $rs = mysql_query($sql);
            if ($rs)
            {
                $row = mysql_fetch_array($rs);
				$color_max = $row["color_max"];
                if ($color_max == "")
                {
					$color_max = 1;
                }
                $sql = "insert into color(color_name, color_order) values('$name', '$color_max')";
                if (mysql_query($sql))
                {
                    addHistory("添加", "颜色", $name);
                }
                else
                {
                    die($sql . "<br />" . $conn->connect_error . "<br />添加失败");
                }
            }
            else
            {
                die($sql . "<br />" . $conn->connect_error . "<br />添加失败");
            }
        }
    }
    else if ($operator == OPERATOR_MODIFY)
    {
        if ($id != 0 && $name != "")
        {
            $sql = "select color_name from color where color_id = $id";
            $rs = mysql_query($sql);
            if ($rs && $row = mysql_fetch_array($rs))
            {
                $color_name = $row["color_name"];
                $sql = "update color set color_name = '$name' where color_id = $id";
                if (mysql_query($sql))
                {
                    addHistory("修改", "颜色", "$name($color_name)");
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
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-cn" />
<script type="text/javascript" src="./dxx.js" ></script>
<script type="text/javascript">
<?php
    defineSortFunction("color");
?>
    function onRemove(fid, fname)
    {
        if (confirm("确定要删除\"" + fname + "\"吗？"))
        {
            color.foperator.value = <?php echo OPERATOR_REMOVE; ?>;
            color.fid.value = fid;
            color.submit();
        }
    }
    function onModify(fid, fname)
    {
        color.foperator.value = <?php echo OPERATOR_MODIFY; ?>;
        color.fid.value = fid;
        color.fname.value = fname;
        color.fsub.value = "修改";

        scroll(0, 0);
    }
    function onClear()
    {
        color.foperator.value = <?php echo OPERATOR_ADD; ?>;
        color.fid.value = 0;
        color.fname.value = "";
        color.fsub.value = "增加";
    }
    function onCheck()
    {
        color.fname.value = color.fname.value.trim();
        if (color.fname.value == "")
        {
            alert("输入颜色");
            return false;
        }
        else
        {
            return true;
        }
    }
</script>
<title>颜色</title>
</head>
<body>
<?php
    defineSort2Form();
?>
    <form method="post" name="color" action="./color.php" onsubmit="return onCheck()" >
        <lable>颜色：</lable><input type="text" name="fname" />
        <input type="hidden" name="foperator" value="<?php echo OPERATOR_ADD; ?>" />
        <input type="hidden" name="fid" value="0" />
        <input type="submit" name="fsub" value="增加" />
        <input type="reset" onclick="onClear()" />
    </form>
    <table frame="border" rules="all">
        <tr>
            <th width="10"></th>
            <th width="300">颜色</th>
            <th width="100">排序</th>
        </tr>
    <?php
        $cs = new CColorSet;
        foreach ($cs->m_items as $v)
        {
            echo "        <tr>\n            <td>\n                ";
            echo "<a href='javascript:void(0)' onclick='onRemove("
                  . $v->id. ", \"" . $v->name . "\")'>X</a>";
            echo "\n            </td>\n            <td>\n                ";
            echo "<a href='javascript:void(0)' onclick='onModify("
                  . $v->id . ", \"" . $v->name . "\")'>"
                  . $v->name . "</a>";
            echo "\n            </td>\n            <td align = 'center'>\n                ";
            generalOrderHtml($v, $cs->m_items);
            echo "\n            </td>\n        </tr>\n";
        }
    ?>
    </table>
</body>
</html>