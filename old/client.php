<?php require_once("sort_help.php") ?>
<?php
	$operator = isset($_POST["foperator"]) ? $_POST["foperator"] : 0;
	$id = isset($_POST["fid"]) ? $_POST["fid"] : 0;
	$name = isset($_POST["fname"]) ? $_POST["fname"] : "";
	$taobao = isset($_POST["ftaobao"]) ? $_POST["ftaobao"] : "";
	if ($taobao == "")
	{
		$taobao = $name;
	}
	$tel1 = isset($_POST["ftel1"]) ? $_POST["ftel1"] : "";
	$tel2 = isset($_POST["ftel2"]) ? $_POST["ftel2"] : "";
	$code = isset($_POST["fcode"]) ? $_POST["fcode"] : "";
	$addr = isset($_POST["faddr"]) ? $_POST["faddr"] : "";

	if ($operator == OPERATOR_REMOVE)
	{
		if ($id != 0)
		{
			$sql = "select client_taobao, client_name from client where client_id = $id";
			$rs = mysql_query($sql);
			if ($rs && $row = mysql_fetch_array($rs))
			{
				$client_name = $row["client_name"] . "(" . $row["client_taobao"] . ")";
				$sql = "delete from client where client_id = $id";
				if (mysql_query($sql))
				{
					addHistory("删除", "客户", $client_name);
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
		if ($name != "")
		{
			$sql = "insert into client(client_taobao, client_name, client_tel, client_tel2, client_code, client_addr)"
			. " values('$taobao', '$name', '$tel1', '$tel2', '$code', '$addr')";
			if (mysql_query($sql))
			{
				addHistory("添加", "客户", $name . "(" . $taobao . ")");
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
			$sql = "select client_taobao, client_name, client_tel, client_tel2, "
			. "client_code, client_addr from client where client_id = $id";
			$rs = mysql_query($sql);
			if ($rs && $row = mysql_fetch_array($rs))
			{
				$client_taobao = $row["client_taobao"];
				$client_name = $row["client_name"];
				$client_tel = $row["client_tel"];
				$client_tel2 = $row["client_tel2"];
				$client_code = $row["client_code"];
				$client_addr = $row["client_addr"];
				$sql = "update client set client_taobao = '$taobao', client_name = '$name', "
				. "client_tel = '$tel1', client_tel2 = '$tel2', client_code = '$code', "
				. "client_addr = '$addr' where client_id = $id";
				if (mysql_query($sql))
				{
					$modify_string = "$name($taobao){";
					if ($client_taobao != $taobao)
					{
						$modify_string = "$modify_string [taobao: $taobao ($client_taobao)]";
					}
					if ($client_name != $name)
					{
						$modify_string = "$modify_string [name: $name ($client_name)]";
					}
					if ($client_tel != $tel1)
					{
						$modify_string = "$modify_string [tel1: $tel1 ($client_tel)]";
					}
					if ($client_tel2 != $tel2)
					{
						$modify_string = "$modify_string [tel2: $tel2 ($client_tel2)]";
					}
					if ($client_code != $code)
					{
						$modify_string = "$modify_string [code: $code ($client_code)]";
					}
					if ($client_addr != $addr)
					{
						$modify_string = "$modify_string [addr: $addr ($client_addr)]";
					}
					$modify_string = "$modify_string}";
					addHistory("修改", "客户", $modify_string);
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
function onDeleteClient(fid, fname)
{
	if (confirm("确定要删除\"" + fname + "\"吗？"))
	{
		client.foperator.value = <?php echo OPERATOR_REMOVE; ?>;
		client.fid.value = fid;
		client.submit();
	}
}
function onModifyClient(fid, ftaobao, fname, ftel1, ftel2, fcode, faddr)
{
	client.foperator.value = <?php echo OPERATOR_MODIFY; ?>;
	client.fid.value = fid;
	client.ftaobao.value = ftaobao;
	client.fname.value = fname;
	client.ftel1.value = ftel1;
	client.ftel2.value = ftel2;
	client.fcode.value = fcode;
	client.faddr.value = faddr;
	client.fsub.value = "修改";

	scroll(0,0);
}
function onClearForm()
{
	client.foperator.value = <?php echo OPERATOR_ADD; ?>;
	client.fid.value = 0;
	client.ftaobao.value = "";
	client.fname.value = "";
	client.ftel1.value = "";
	client.ftel2.value = "";
	client.fcode.value = "";
	client.faddr.value = "";
	client.fsub.value = "增加";
}
function onCheckForm()
{
	client.ftaobao.value = client.ftaobao.value.trim();
	client.fname.value = client.fname.value.trim();
	client.ftel1.value = client.ftel1.value.trim();
	client.ftel2.value = client.ftel2.value.trim();
	client.fcode.value = client.fcode.value.trim();
	client.faddr.value = client.faddr.value.trim();
	if (client.fname.value == "")
	{
		alert("输入名称");
		return false;
	}
	else
	{
		return true;
	}
}
function onParser()
{
	var reg = /^([^，]+)，([^，]+)，([^，]*)，([^，]+)，([^，]+)$/;
	var result = reg.exec(document.getElementById("fclient").value);
	if (result.length == 6)
	{
		client.fname.value = result[1].trim();
		client.ftel1.value = result[2].trim();
		client.ftel2.value = result[3].trim();
		client.faddr.value = result[4].trim();
		client.fcode.value = result[5].trim();
	}
}

function onQueryClient()
{
	var taobao = client.ftaobao.value.trim();
	if (taobao.length == 0)
	{
		return;
	}
	
	var send_string = "taobao=" + taobao;
	send_string = encodeURI(encodeURI(send_string));
	
	var xh = new XMLHttpRequest();
	
	xh.onreadystatechange = function()
	{
		if (xh.readyState == 4 && xh.status == 200)
		{
			if (xh.responseText != "")
			{
				alert("已记录");
			}
		}
	}
	  
	xh.open("POST", "./query_order.php", true);
	xh.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xh.send(send_string);
}
</script>
<title>客户</title>
</head>
<body>
<?php
defineSortForm();
?>
<table border="0">
	<tr>
		<td>
			<form method="post" name="client" action="./client.php" onsubmit="return onCheckForm()" >
				<label>淘宝：</label><input type="text" name="ftaobao" onchange="onQueryClient()" />
				<label>姓名：</label><input type="text" name="fname" /><br />
				<label>电话：</label><input type="text" name="ftel1" />
				<label>座机：</label><input type="text" name="ftel2" /><br />
				<label>邮编：</label><input type="text" name="fcode" /><br />
				<label>地址：</label><textarea name="faddr" rows="4" cols="50"></textarea><br />
				<input type="hidden" name="foperator" value="<?php echo OPERATOR_ADD; ?>;" />
				<input type="hidden" name="fid" value="0" />
				<input type="submit" name="fsub" value="增加" />
				<input type="reset" onclick="onClearForm()" />
			</form>
		</td>
		<td width="30"></td>
		<td>
			<table border="0">
				<tr>
					<td>
						<textarea name="fclient" id="fclient" rows="8" cols="50"></textarea>
					</td>
				</tr>
				<tr>
					<td align="right">
						<input type="button" value="自动解析客户地址信息" onclick="onParser()"/>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<p>
<table frame="border" rules="all">
<tr>
<?php
$page = "client";
$default_dir = array(1, 2, 2, 2, 2, 2, 2);
$headers = array("*", "10", "姓名", "60", 
"淘宝名", "80", "电话", "80", "座机", "100", "邮编", "80", "地址", "400");
 generalHeader($page, $default_dir, $headers);
?>
</tr>
<?php
$sql = "SELECT * from client ";
$sort_key = array("client_id desc", "client_name", "client_taobao", 
"client_tel", "client_tel2",
"client_code", "client_addr");
$sql = $sql . appendSortSql($page, $default_dir, $sort_key);
$rs = mysql_query($sql);
		if ($rs)
		{
			while ($row = mysql_fetch_array($rs))
			{
				echo "<tr><td>";
				  echo "<a href='javascript:void(0)' onclick='onDeleteClient(" 
					  . $row["client_id"]. ", \"" . $row["client_name"] . "\")'>X</a>";
				echo "</td><td>";
				  echo "<a href='javascript:void(0)' onclick='onModifyClient("
					  . $row["client_id"] . ",\"" . $row["client_taobao"] . "\",\"" 
					  . str_replace("\r\n", "\\n", $row["client_name"]) 
					  . "\",\"" . $row["client_tel"] . "\",\"" . $row["client_tel2"]
					. "\", \"" . $row["client_code"] . "\", \"" 
					  . str_replace("\r\n", "\\n", $row["client_addr"]) . "\")'>"
					  . $row["client_name"] . "</a>";
				echo "</td><td>";
				  echo $row["client_taobao"];
				echo "</td><td>";
				  echo $row["client_tel"];
				echo "</td><td>";
				  echo $row["client_tel2"];
				echo "</td><td>";
				  echo $row["client_code"];
				echo "</td><td>";
				  echo $row["client_addr"];
				echo "</td></tr>";
			}
		}
?>
</table>
</body>
</html>