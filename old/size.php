<?php require_once("sizedata.php") ?>
<?php
    $operator = isset($_POST["foperator"]) ? $_POST["foperator"] : OPERATOR_INVALID;
    $id = isset($_POST["fid"]) ? $_POST["fid"] : 0;
    $name = isset($_POST["fname"]) ? $_POST["fname"] : "";

    if ($operator == OPERATOR_REMOVE)
    {
        if ($id != 0)
        {
            $sql = "select size_name, size_order from size where size_id = $id";
            $rs = mysql_query($sql);
            if ($rs && $row = mysql_fetch_array($rs))
            {
                $size_name = $row["size_name"];
                $size_order = $row["size_order"];
                mysql_query("begin");
                $sql = "update size set size_order = size_order - 1 where size_order > $size_order";
                if (!mysql_query($sql))
                {
                    mysql_query("rollback");
                    die($sql . "<br />" . $conn->connect_error . "<br />删除失败");
                }
                $sql = "delete from size where size_id = $id";
                if (mysql_query($sql))
                {
                    mysql_query("commit");
                    addHistory("删除", "尺寸", $size_name);
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
            $sql = "select max(size_order) + 1 as size_max from size";
            $rs = mysql_query($sql);
            if ($rs)
            {
                $row = mysql_fetch_array($rs);
				$size_max = $row["size_max"];
                if ($size_max == "")
                {
					$size_max = 1;
                }
                $sql = "insert into size(size_name, size_order) values('$name', '$size_max')";
                if (mysql_query($sql))
                {
                    addHistory("添加", "尺寸", $name);
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
            $sql = "select size_name from size where size_id = $id";
            $rs = mysql_query($sql);
            if ($rs && $row = mysql_fetch_array($rs))
            {
                $size_name = $row["size_name"];
                $sql = "update size set size_name = '$name' where size_id = $id";
                if (mysql_query($sql))
                {
                    addHistory("修改", "尺寸", "$name($size_name)");
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
    defineSortFunction("size");
?>
    function onRemove(fid, fname)
    {
        if (confirm("确定要删除\"" + fname + "\"吗？"))
        {
            size.foperator.value = <?php echo OPERATOR_REMOVE; ?>;
            size.fid.value = fid;
            size.submit();
        }
    }
    function onModify(fid, fname)
    {
        size.foperator.value = <?php echo OPERATOR_MODIFY; ?>;
        size.fid.value = fid;
        size.fname.value = fname;
        size.fsub.value = "修改";

        scroll(0, 0);
    }
    function onClear()
    {
        size.foperator.value = <?php echo OPERATOR_ADD; ?>;
        size.fid.value = 0;
        size.fname.value = "";
        size.fsub.value = "增加";
    }
    function onCheck()
    {
        size.fname.value = size.fname.value.trim();
        if (size.fname.value == "")
        {
            alert("输入尺寸");
            return false;
        }
        else
        {
            return true;
        }
    }
</script>
<title>尺寸</title>
</head>
<body>
<?php
    defineSort2Form();
?>
    <form method="post" name="size" action="./size.php" onsubmit="return onCheck()" >
        <lable>尺寸：</lable><input type="text" name="fname" />
        <input type="hidden" name="foperator" value="<?php echo OPERATOR_ADD; ?>" />
        <input type="hidden" name="fid" value="0" />
        <input type="submit" name="fsub" value="增加" />
        <input type="reset" onclick="onClear()" />
    </form>
    <table frame="border" rules="all">
        <tr>
            <th width="10"></th>
            <th width="300">尺寸</th>
            <th width="100">排序</th>
        </tr>
    <?php
        $ss = new CSizeSet;
        foreach ($ss->m_items as $v)
        {
            echo "        <tr>\n            <td>\n                ";
            echo "<a href='javascript:void(0)' onclick='onRemove("
                  . $v->id. ", \"" . $v->name . "\")'>X</a>";
            echo "\n            </td>\n            <td>\n                ";
            echo "<a href='javascript:void(0)' onclick='onModify("
                  . $v->id . ", \"" . $v->name . "\")'>"
                  . $v->name . "</a>";
            echo "\n            </td>\n            <td align = 'center'>\n                ";
            generalOrderHtml($v, $ss->m_items);
            echo "\n            </td>\n        </tr>\n";
        }
    ?>
    </table>
</body>
</html>