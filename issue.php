<?php require_once("sort_help.php") ?>
<?php
    $fname = isset($_POST["fname"]) ? $_POST["fname"] : "";
    $fout = (isset($_POST["fout"]) && $_POST["fout"] == "on") ? true : false;
    $faction = isset($_POST["faction"]) ? $_POST["faction"] : 0;
    $fid = isset($_POST["fid"]) ? $_POST["fid"] : 0;
    
    if ($faction != 0)
    {
        if ($faction == 1)
        {
            if ($fid != 0)
            {
                $sql = "select issue_name from issue where issue_id='" . $fid . "'";
                $rs = mysql_query($sql);
                if ($rs && $row = mysql_fetch_array($rs))
                {
                    $issue_name = $row["issue_name"];
                    $sql = "delete from issue where issue_id='" . $fid . "'";
                    if (mysql_query($sql))
                    {
                        addHistory("删除", "条目", $issue_name);
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
                $sql = "insert into issue(issue_name, issue_out) values('" 
                    . $fname ."','" 
                    . ($fout == "on" ? 1 : 0)
                    . "')";
                if (mysql_query($sql))
                {
                    addHistory("添加", "条目", $fname 
                        . "(" . ($fout == "on" ? "支出" : "收入") . ")");
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
                $sql = "select issue_name from issue where issue_id='" . $fid . "'";
                $rs = mysql_query($sql);
                if ($rs && $row = mysql_fetch_array($rs))
                {
                    $issue_name = $row["issue_name"];
                    $sql = "update issue set issue_name='" . $fname 
                        . "', issue_out=" . ($fout == "on" ? 1 : 0)
                        . " where issue_id='" . $fid ."'";
                    if (mysql_query($sql))
                    {
                        addHistory("修改", "条目", $fname . "(" . $issue_name . ")");
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
    function deleteIssue(fid, fname)
    {
        if (confirm("确定要删除'" + fname + "'吗？"))
        {
            issue.faction.value = 1;
            issue.fid.value = fid;
            issue.submit();
        }
    }
    function modifyIssue(fid, fname, fout)
    {
        issue.faction.value = 3;
        issue.fid.value = fid;
        issue.fname.value = fname;
        issue.fout.checked = fout == 1 ? true : false;
        issue.fsub.value = "修改";
        
        scroll(0, 0);
    }
    function clearForm()
    {
        issue.faction.value = 2;
        issue.fid.value = 0;
        issue.fname.value = "";
        issue.fout.checked = true;
        issue.fsub.value = "增加";
    }
    function checkForm()
    {
        issue.fname.value = issue.fname.value.trim();
        if (issue.fname.value == "")
        {
            alert("输入条目");
            return false;
        }
        else
        {
            return true;
        }
    }
</script>
<title>条目</title>
</head>
<body>
<?php
    defineSortForm();
?>
    <form method="post" name="issue" action="./issue.php" onsubmit="return checkForm()" >
        <label>条目：</label><input type="text" name="fname" />
        <label>支出：</label><input type="checkbox" name="fout" CHECKED />
        <input type="hidden" name="faction" value="2" />
        <input type="hidden" name="fid" value="0" />
        <input type="submit" name="fsub" value="增加" />
        <input type="reset" onclick="clearForm()" />
    </form>
    <p>
    <table frame="border" rules="all">
        <tr>
<?php
            $page = "issue";
            $default_dir = array(1, 2, 2);
            $headers = array("*", "10", "条目", "300", "收支", "100");
             generalHeader($page, $default_dir, $headers);
?>
        </tr>
<?php
        $sql = "select * from issue";
        $sort_key = array("", "issue_name", "issue_out");
        $sql = $sql . appendSortSql($page, $default_dir, $sort_key);
        $rs = mysql_query($sql);
		if ($rs)
		{
			while ($row = mysql_fetch_array($rs))
			{
				  echo "<tr><td><a href='javascript:void(0)' onclick='deleteIssue(" 
					  . $row["issue_id"]. ", \"" . $row["issue_name"] . "\")'>X</a>";
				  echo "</td><td>";
				  echo "<a href='javascript:void(0)' onclick='modifyIssue("
					  . $row["issue_id"] . ", \"" . $row["issue_name"] . "\", " . $row["issue_out"] . ")'>" 
					  . $row["issue_name"] . "</a>";
				  echo "</td><td>";
				  echo $row["issue_out"] == 1 ? "支出" : "收入";
				  echo "</td></tr>";
			}
		}
?>
    </table>
</body>
</html>