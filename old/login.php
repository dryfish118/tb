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
		$conn = new mysqli("localhost", "root", "admin", "dxx");
		if ($conn->connect_error)
		{
			die($conn->connect_error);
		}
		$sql = "select * from user where user_name like '" . $fname . "'";
		$rs = $conn->query($sql);
		if ($rs->num_rows > 0)
		{
			$row = $rs->fetch_assoc();
			if ($row)
			{
				setcookie("user", $row["user_id"], time() + 60 * 60 * 24 * 365);
				echo "<script type=\"text/javascript\">document.location.href=\"./dxx.php\";</script>";
			}
			else
			{
				die($conn->connect_error);
			}
		}
		else
		{
			echo("failed to login.   " . $sql);
		}
		$conn->close();
	}
?>
</head>
<body>
	<form method="post" action="./login.php">
		<label>人员：</label><input type="text" name="fname" />
	</form>
</body>
</html>