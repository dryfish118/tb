<?php
	$fout = isset($_POST["fout"]) ? $_POST["fout"] : 0;
	$fname = isset($_POST["fname"]) ? $_POST["fname"] : "";
	$redirect = 0;

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
		mysqli_query($conn, "set names utf8");
		$sql = "select * from user where user_name like '" . $fname . "'";
		if ($rs = $conn->query($sql))
		{
			if ($row = $rs->fetch_assoc())
			{
				setcookie("user", $row["user_id"], time() + 60 * 60 * 24 * 365);
				$redirect = 1;
			}
			$rs->free();
		}
		$conn->close();
	}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-cn" />
<title>登录</title>
<?php
	if ($redirect == 1)
	{
		echo "<script type=\"text/javascript\">document.location.href=\"./query_reserve.php\";</script>";
	}
?>
</head>
<body>
	<form method="post" action="./login.php">
		<label>人员：</label><input type="text" name="fname" />
	</form>
</body>
</html>