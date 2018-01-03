<?php require_once("conn.php") ?>
<?php
	$fcontent = isset($_POST["fcontent"]) ? $_POST["fcontent"] : "";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-cn" />
<?php
	$content = "";
	$sql = "select diary_id, diary_content, user_name from user "
		. "inner join diary on diary.diary_user_id = user.user_id "
		. "where diary_user_id = '" . $_COOKIE["user"] 
		. "' and diary_date = date(now())";
	$rs = mysql_query($sql);
	if ($rs && $row = mysql_fetch_array($rs))
	{
		$content = $row["diary_content"];
	}
	
	if ($fcontent != "")
	{
		$content = $fcontent;
		$hasUpdate = false;
		if ($row)
		{
			$sql = "update diary set diary_content='" . $fcontent 
				. "' where diary_id='" . $row["diary_id"] . "'";
			if (mysql_query($sql))
			{
				$hasUpdate = true;
				addHistory("修改", "日志", "");
			}
			else
			{
				die($sql . "<br />" . $conn->connect_error . "<br />修改失败");
			}
		}
					
		if (!$hasUpdate)
		{
			$sql = "insert into diary(diary_user_id, diary_date, diary_content) "
				. "values('" . $_COOKIE["user"] 
				. "',now(),'" . $fcontent ."')";
			if (mysql_query($sql))
			{
				addHistory("添加", "日志", "");
			}
			else
			{
				die($sql . "<br />" . $conn->connect_error . "<br />添加失败");
			}
		}
		
		$fcontent = "";
	}
?>
<script type="text/javascript" src="./dxx.js" ></script>
<script type="text/javascript">
	function checkForm()
	{
		diary.fcontent.value = diary.fcontent.value.trim();
		if (diary.fcontent.value == "")
		{
			alert("输入内容");
			return false;
		}
		else
		{
			return true;
		}
	}
</script>
<title>日志</title>
</head>
<body>
	<form method="post" name="diary" action="./diary.php" onsubmit="return checkForm()">
		<textarea rows="20" cols="80" name="fcontent"><?php echo $content; ?></textarea><br />
		<input type="submit" />
		<input type="reset" />
	</form>
<?php
	$sql = "select diary_date, diary_content, user_name from user "
		. "inner join diary on diary.diary_user_id = user.user_id "
		. "order by diary_date desc";
	$rs = mysql_query($sql);
	if ($rs)
	{
		while ($row = mysql_fetch_array($rs))
		{
			echo "<hr />";
			echo "<h1>" . $row["user_name"] . "(" . $row["diary_date"] . ")" . "</h1>";
			echo "<pre>" . $row["diary_content"] . "</pre><br />";
		}
	}
?>
</body>
</html>
