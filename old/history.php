<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-cn" />
<title>历史操作</title>
<?php require_once("conn.php"); ?>
</head>
<body>
	<table frame="border" rules="all">
		<tr>
			<th width="60">人员</th>
			<th width="180">时间</th>
			<th width="500">内容</th>
		</tr>
<?php
		$sql = "select history_id, history_time, history_action, history_table, "
			. "history_contents, user_name "
			. "from user inner join history "
			. "on history.history_user_id = user.user_id "
			. "order by history_id desc limit 0, 300";
		$rs = mysql_query($sql);
		if ($rs)
		{
			while ($row = mysql_fetch_array($rs))
			{
				$contents = $row["history_action"];
				$contents = $contents . "[" . $row["history_table"] . "]: ";
				$contents = $contents . $row["history_contents"];
				echo "<tr><td>" . $row["user_name"] . "</td><td>" 
					. $row["history_time"] . "</td><td>" 
					. $contents . "</td></tr>";
			}
		}
?>
	</table>
</body>
</html>