<?php require_once("conn.php"); ?>
<?php
    $operator = isset($_POST["foperator"]) ? $_POST["foperator"] : OPERATOR_INVALID;
    $id = isset($_POST["fid"]) ? $_POST["fid"] : 0;
    $name = isset($_POST["fname"]) ? $_POST["fname"] : "";

    if ($operator == OPERATOR_REMOVE)
    {
        if ($id != 0)
        {
            $sql = "select user_name from user where user_id = $id";
            $rs = $conn->query($sql);
            if ($rs)
            {
                if ($row = $rs->fetch_assoc())
                {
                    $user_name = $row["user_name"];
                    $sql = "delete from user where user_id = $id";
                    if ($conn->query($sql))
                    {
                        addHistory("删除", "人员", $user_name);
                    }
                    else
                    {
                        die($sql . "<br />" . $conn->connect_error . "<br />删除失败");
                    }
                }
                $rs->free();
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
            $sql = "insert into user(user_name) values('$name')";
            if ($conn->query($sql))
            {
                addHistory("添加", "人员", $name);
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
            $sql = "select user_name from user where user_id = $id";
            $rs = $conn->query($sql);
            if ($rs)
            {
                if ($row = $rs->fetch_assoc())
                {
                    $user_name = $row["user_name"];
                    $sql = "update user set user_name = '$name' where user_id = $id";
                    if ($conn->query($sql))
                    {
                        addHistory("修改", "人员", "$name($user_name)");
                    }
                    else
                    {
                        die($sql . "<br />" . $conn->connect_error . "<br />修改失败");
                    }
                }
                $rs->free();
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
<link rel="stylesheet" type="text/css" href="./tb.css" />
<script type="text/javascript" src="./dxx.js" ></script>
<script type="text/javascript">
    function onRemove(id, name)
    {
        if (confirm("确定要删除\"" + name + "\"吗？"))
        {
            user.foperator.value = <?php echo OPERATOR_REMOVE; ?>;
            user.fid.value = id;
            user.submit();
        }
    }
    function onModify(id, name)
    {
        user.foperator.value = <?php echo OPERATOR_MODIFY; ?>;
        user.fid.value = id;
        user.fname.value = name;
        user.fsub.value = "修改";
        
        scroll(0,0);
    }
    function onClear()
    {
        user.foperator.value = <?php echo OPERATOR_ADD; ?>;
        user.fid.value = 0;
        user.fname.value = "";
        user.fsub.value = "增加";
    }
    function onCheck()
    {
        user.fname.value = user.fname.value.trim();
        if (user.fname.value == "")
        {
            alert("输入名字");
            return false;
        }
        else
        {
            return true;
        }
    }
</script>
<title>人员</title>
</head>
<body>
<?php
    require_once("nav.php");
?>
    <form method="post" name="user" action="./user.php" onsubmit="return onCheck()" >
        <label>人员：</label><input type="text" name="fname"/>
        <input type="hidden" name="foperator" value="<?php echo OPERATOR_ADD; ?>" />
        <input type="hidden" name="fid" value="0" />
        <input type="submit" name="fsub" value="增加" />
        <input type="reset" onclick="onClear()" />
    </form>

<?php
        $sql = "select * from user ";
        $sort_key = array("", "user_name");
        $rs = $conn->query($sql);
		if ($rs)
		{
			while ($row = $rs->fetch_assoc())
			{
                echo "<tr><td>";
                echo $row["user_name"];
				// echo "<a href='javascript:void(0)' onclick='onRemove(" 
				// 	. $row["user_id"] . ", \"" . $row["user_name"] . "\")'>X</a>";
				// echo "</td><td>";
				// echo "<a href='javascript:void(0)' onclick='onModify("
				// 	. $row["user_id"] . ", \"" . $row["user_name"] . "\")'>"
				// 	. $row["user_name"] . "</a>";
				echo "</td></tr>";
            }
            $rs->free();
		}
?>
</body>
</html>
