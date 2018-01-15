<?php
	$fout = isset($_GET["fout"]) ? $_GET["fout"] : 0;
	$fname = isset($_POST["fname"]) ? $_POST["fname"] : "";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-cn" />
<title>登录</title>
<?php
	if ($fout == 1)
	{
		setcookie("user", "", time());
	}
	if ($fname != "")
	{
		date_default_timezone_set('PRC');
		$conn = mysql_connect("localhost", "root", "admin");
		mysql_select_db("dxx", $conn);
		mysql_query("set character set 'utf8'");

		$sql = "select * from user where user_name like '$fname'";
		$rs = mysql_query($sql, $conn);
		if ($rs)
		{
			$row = mysql_fetch_assoc($rs);
			if ($row)
			{
				setcookie("user", $row["user_id"]);
				echo "<script type=\"text/javascript\">document.location.href=\"./dxx.php\";</script>";
			}
			else
			{
				echo "failed to mysql_fetch_assoc.  " . $sql;
			}
		}
		else
		{
			echo("failed to login.   " . $sql);
		}
	}
?>
</head>
<body>
	<form method="post" action="./login.php">
		<label>人员：</label><input type="text" name="fname" />
	</form>
</body>
</html>