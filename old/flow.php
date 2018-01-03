<?php require_once("sort_help.php"); ?>
<?php
    $operator = isset($_POST["foperator"]) ? $_POST["foperator"] : OPERATOR_INVALID;
    $id = isset($_POST["fid"]) ? $_POST["fid"] : 0;
    $issue = isset($_POST["fissue"]) ? $_POST["fissue"] : "";
    $user = isset($_POST["fuser"]) ? $_POST["fuser"] : "";
    $money = isset($_POST["fmoney"]) ? $_POST["fmoney"] : 0;
    $time = isset($_POST["ftime"]) ? $_POST["ftime"] : "";
    $remark = isset($_POST["fremark"]) ? $_POST["fremark"] : "";

	if ($operator == OPERATOR_REMOVE)
	{
		if ($id != 0)
		{
			$sql = "select issue_out, issue_name, flow_money, flow_remark "
				. "from issue inner join flow on issue.issue_id = flow_issue_id "
				. "where flow_id = $id";
			$rs = mysql_query($sql);
			if ($rs && $row = mysql_fetch_array($rs))
			{
				$issue_out = $row["issue_out"] == 1 ? "-" : "+";
				$issue_name = $row["issue_name"];
				$flow_money = $row["flow_money"];
				$flow_remark = $row["flow_remark"];
				$sql = "delete from flow where flow_id = $id";
				if (mysql_query($sql))
				{
					addHistory("删除", "流水", "$flow_remark($issue_name$issue_out) $flow_money");
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
	else if ($operator == OPERATOR_ADD)
	{
		if ($money != 0)
		{
			$sql = "select * from issue where issue_id = $issue";
			$rs = mysql_query($sql);
			if ($rs && $row = mysql_fetch_array($rs))
			{
				$sql = "insert into flow(flow_issue_id, flow_user_id, flow_money, flow_time, flow_remark) "
					. "values('$issue', '$user', '$money', '$time', '$remark')";
				if (mysql_query($sql))
				{
					$issue_out = $row["issue_out"] == 1 ? "-" : "+";
					$issue_name = $row["issue_name"];
					addHistory("添加", "流水", "$remark($issue_name$issue_out) $money");
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
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-cn" />
<link type="text/css" rel="stylesheet" href="./dtctrl/calendar.css" >
<script type="text/javascript" src="./dtctrl/calendar.js" ></script>
<script type="text/javascript" src="./dtctrl/calendar-zh.js" ></script>
<script type="text/javascript" src="./dtctrl/calendar-setup.js"></script>
<script type="text/javascript" src="./dxx.js" ></script>
<script type="text/javascript">
    function onRemove(id, name)
    {
        if (confirm("确定要删除'" + name + "'吗？"))
        {
            flow.foperator.value = <?php echo OPERATOR_REMOVE; ?>;
            flow.fid.value = id;
            flow.submit();
        }
    }
    function onClear()
    {
        flow.foperator.value = <?php echo OPERATOR_ADD; ?>;
        flow.fid.value = 0;
        flow.fissue[0].selected = true;
        flow.fuser[0].selected = true;
        flow.ftime.value = today();
        flow.fmoney.value = 0;
        flow.fremark.value = "";
        flow.fsub.value = "增加";
    }
    function onCheck()
    {
        flow.fmoney.value = Math.abs(flow.fmoney.value.trim());
        if (flow.fmoney.value == 0 || flow.fmoney.value == "")
        {
            alert("输入金额");
            flow.fmoney.value = 0;
            return false;
        }
        if (flow.ftime.value == "")
        {
            alert("输入时间");
            return false;
        }
        var name = flow.fissue.options[flow.fissue.selectedIndex].text;
        if (name.substring(0, 4) == "[支出]")
        {
            flow.fmoney.value = -flow.fmoney.value;
        }
        return true;
    }
</script>
<title>流水</title>
</head>
<body>
<?php
    defineSortForm();
?>
    <form method="post" name="flow" action="./flow.php" onsubmit="return onCheck()" >
        <lable>条目：</lable><select name="fissue">
<?php
        $sql = "select * from issue order by issue_id desc";
        $rs = mysql_query($sql);
		if ($rs)
		{
			while ($row = mysql_fetch_array($rs))
			{
				echo "<option value=" . $row["issue_id"] . ">[" . ($row["issue_out"] ? "支出" : "收入") . "]" . $row["issue_name"];
			}
		}
?>
        </select>
        <lable>人员：</lable><select name="fuser">
<?php
        $sql = "select * from user";
        $rs = mysql_query($sql);
		if ($rs)
		{
			while ($row = mysql_fetch_array($rs))
			{
				echo "<option value=" . $row["user_id"] . ">" . $row["user_name"];
			}
		}
?>
        </select>
        <lable>金额：</lable><input type="text" name="fmoney" value="0" />
        <lable>时间：</lable><input type="text" id="ftime" name="ftime" onclick="return showCalendar('ftime', 'y-mm-dd');" />
        <br />
        <lable>说明：</lable><textarea name="fremark" rows="4" cols="80"></textarea>
        <input type="hidden" name="foperator" value="<?php echo OPERATOR_ADD; ?>" />
        <input type="hidden" name="fid" value="0" />
        <input type="submit" name="fsub" value="增加" />
        <input type="reset" onclick="onClear()" />
    </form>
    <p>
    <table frame="border" rules="all">
        <tr>
<?php
            $page = "flow";
            $default_dir = array(1, 2, 2, 3, 2, 0);
            $headers = array("*", "10", "条目", "100", "人员", "50", "时间", "80", "金额", "50", "说明", "600");
             generalHeader($page, $default_dir, $headers);
?>
        </tr>
<?php
        $sql = "select flow_id, issue_id, issue_name, user_id, user_name, "
            . "date_format(flow_time, '%Y-%m-%d') as flow_time, "
            . "flow_money, flow_remark "
            . "from user inner join (issue inner join flow "
            . "on issue.issue_id = flow.flow_issue_id) "
            . "on user.user_id = flow.flow_user_id ";
        $sort_key = array("", "issue_name", "user_name", "flow_time", "flow_money", "");
        $sql .= appendSortSql($page, $default_dir, $sort_key);
        $rs = mysql_query($sql);
		if ($rs)
		{
			while ($row = mysql_fetch_array($rs))
			{
				echo "        <tr>\n            <td>\n                ";
				echo "<a href='javascript:void(0)' onclick='onRemove(" 
				  . $row["flow_id"]. ", \"" . $row["issue_name"] . "\")'>X</a>";
				echo "\n            </td>\n            <td>\n                ";
				echo $row["issue_name"];
				echo "\n            </td>\n            <td>\n                ";
				echo $row["user_name"];
				echo "\n            </td>\n            <td>\n                ";
				echo $row["flow_time"];
				echo "\n            </td>\n            <td>\n                ";
				echo $row["flow_money"];
				echo "\n            </td>\n            <td>\n                ";
				echo $row["flow_remark"];
				echo "\n            </td>\n        </tr>\n";
			}
		}
?>
    </table>
</body>
<script type="text/javascript">
    flow.ftime.value = jsToday();
</script>
</html>
