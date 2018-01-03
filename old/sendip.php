<?php
	date_default_timezone_set("PRC");

	if (isset($_FILES["file"]["error"]) && $_FILES["file"]["error"] == 0 && isset($_FILES["file"]["size"]) && $_FILES["file"]["size"] != 0)
	{
		$exe_command = "mysql -uroot -padmin dxx < " . $_FILES["file"]["tmp_name"];
		exec($exe_command);
	}
	else if (isset($_GET["fpass"]) && $_GET["fpass"] == "1234")
	{
		$bk_name = date('Ymd') . ".bak";
		$exe_command = "mysqldump -uroot -padmin dxx > ./" . $bk_name ;
		exec($exe_command);
		
		$cab_name = date('Ymd') . ".cab";
		$exe_command = "makecab " . $bk_name . " " . $cab_name;
		exec($exe_command);
		unlink($bk_name);
		
		//$contents = file_get_contents('http://www.ip138.com/ip2city.asp');
		$contents = file_get_contents('http://www.cz88.net/ip/viewip468_25.aspx');
		if (!$contents)
		{
			unlink($cab_name);
			echo "Faileld to get contents";
			die();
		}
		$begin_mark = "<span id=\"IPMessage\">";
		$contents = strstr($contents, $begin_mark);
		if (!$contents)
		{
			unlink($cab_name);
			echo "Unexpect contents";
			die();
		}
		$pos = strpos($contents, "</span>");
		if (!$pos)
		{
			unlink($cab_name);
			echo "Unexpect format";
			die();
		}
		$contents = substr($contents, strlen($begin_mark), $pos - strlen($begin_mark));
		if (!$contents)
		{
			unlink($cab_name);
			echo "Unknown ip";
			die();
		}
		$url = "http://" . $contents . ":2012/dxx.php";
		
		$file = fopen("mail.lst", "r");
		if (!$file)
		{
			unlink($cab_name);
			echo "Failed to open mail.lst";
			die();
		}
		$maillst = array();
		while(!feof($file))
		{
			$addr = fgets($file);
			$addr = $addr.trim(" ");
			if (substr($addr, 0, 1) != ";")
			{
				array_push($maillst, $addr);
			}
		}
		fclose($file);
		if (!$maillst)
		{
			unlink($cab_name);
			echo "Failed to read mail.lst";
			die();
		}

		require_once("./PHPMailer/class.phpmailer.php");
		require_once("./PHPMailer/class.smtp.php");
		
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->CharSet = "UTF-8";
		$mail->Host = "smtp.163.com";
		$mail->Port = 25;
		$mail->SMTPAuth = true;
		$mail->Username = "dxxbook@163.com";
		$mail->Password = "0duo_duo0";
		$mail->SetFrom("dxxbook@163.com", "[" . $_SERVER["SERVER_ADMIN"] . "]Webmaster");
		for ($i = 0; $i < count($maillst); $i++)
		{
			if (strlen($maillst[$i]))
			{
				$mail->AddAddress($maillst[$i]);
			}
		}
		$mail->Subject = $url;
		$body = "<a href=\"" . $url . "\">" . $url . "</a>";
		$mail->MsgHTML($body);
		$mail->AddAttachment("./" . $cab_name, $cab_name); 
		if(!$mail->Send())
		{
			unlink($cab_name);
			echo "Faile to send mail";
			die();
		}
		unlink($cab_name);
		echo "成功";
		echo "<script type=\"text/javascript\">
				window.opener=null;
				window.open('','_parent');
				window.close();
			</script>";
	}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-cn" />
<script type="text/javascript">
	function onParseOrder()
	{
		var txtCode = document.getElementById("txtCode").value;
		
		var reg = /订单编号：(\d+) 成交时间：/g;
		var orders = new Array();
		var rt;
		while (rt = reg.exec(txtCode))
		{
			orders.push(rt[1]);
		}
		
		var send_string = "orders=" + orders.join("-");
		send_string = encodeURI(send_string);
		
		//document.getElementById("result").innerHTML = orders.join("<br/>");
		
		var xh = new XMLHttpRequest();
		
		xh.onreadystatechange = function()
		{
			if (xh.readyState == 4 && xh.status == 200)
			{
				document.getElementById("result").innerHTML = xh.responseText;
				//alert(xh.responseText);
			}
		}
		  
		xh.open("POST", "./query_order.php", true);
		xh.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xh.send(send_string);
	}
</script>
<title>系统</title>
</head>
<body>
	<form action="./sendip.php" method="get">
		<div>备份密码：<input type="text" name="fpass" />
		<input type="submit" value="备份" />
		<input type="reset" /></div>
	</form>
	<form action="./sendip.php" method="post" enctype="multipart/form-data">
		<div>还原文件：<input type="file" name="file" id="file" />
		<input type="submit" value="提交" />
		<input type="reset" /></div>
	</form>
	<form action="./clear.php" method="get">
		<input type="hidden" name="f" value="abcd" />
		<input type="submit" value="清理" />
	</form>
	<p><p>
	<textarea id="txtCode" rows="10" cols="180"></textarea><br/>
	<input type="button" value="解析" onClick="onParseOrder()">
	<p>
	<div id="result"></div>
</body>
</html>
