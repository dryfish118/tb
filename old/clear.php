<?php require_once("conn.php") ?>
<?php
	$fpass = isset($_GET["fpass"]) ? $_GET["fpass"] : "";
	
	$rc = 0;
	if ($fpass == "abcd")
	{
		set_time_limit(1800);
		
		mysql_query("begin");
		$sql = "update goods2 set goods2_in=0, goods2_out=0, goods2_left=0";
		if (!mysql_query($sql))
		{
			die($sql . "<br />" . $conn->connect_error);
			mysql_query("rollback");
		}
		$sql = "select * from stock";
		$rs = mysql_query($sql);
		while ($row = mysql_fetch_array($rs))
		{
			$sql = "update goods2 set goods2_in=goods2_in+" . $row["stock_amount"] . " where goods2_id=" . $row["stock_goods2_id"];
			if (!mysql_query($sql))
			{
				die($sql . "<br />" . $conn->connect_error);
				mysql_query("rollback");
			}
		}
		$sql = "select * from item";
		$rs = mysql_query($sql);
		while ($row = mysql_fetch_array($rs))
		{
			$sql = "update goods2 set goods2_out=goods2_out-" . $row["item_amount"] . " where goods2_id=" . $row["item_goods2_id"];
			if (!mysql_query($sql))
			{
				die($sql . "<br />" . $conn->connect_error);
				mysql_query("rollback");
			}
		}
		$sql = "update goods2 set goods2_left=goods2_in+goods2_out";
		if (!mysql_query($sql))
		{
			die($sql . "<br />" . $conn->connect_error);
			mysql_query("rollback");
		}
		
		$sql = "update sell set sell_total_in=0, sell_total_out=0, sell_Profit=0";
		if (!mysql_query($sql))
		{
			die($sql . "<br />" . $conn->connect_error);
			mysql_query("rollback");
		}
		$sql = "select sell_id from sell";
		$rs = mysql_query($sql);
		if (!$rs)
		{
			die($sql . "<br />" . $conn->connect_error);
			mysql_query("rollback");
		}
		while ($row = mysql_fetch_array($rs))
		{
			$sql = "select item_price, item_amount, goods_price "
				. "from (goods inner join goods2 "
				. "on goods.goods_id=goods2.goods2_goods_id) "
				. "inner join item "
				. "on goods2.goods2_id=item.item_goods2_id "
				. "where item_sell_id=" . $row["sell_id"];
			$rsItem = mysql_query($sql);
			if (!$rsItem)
			{
				die($sql . "<br />" . $conn->connect_error);
				mysql_query("rollback");
			}
			while ($rowItem = mysql_fetch_array($rsItem))
			{
				$sql = "update sell set sell_total_in=sell_total_in+"
					. $rowItem["item_price"] * $rowItem["item_amount"]
					. " where sell_id=" . $row["sell_id"];
				if (!mysql_query($sql))
				{
					die($sql . "<br />" . $conn->connect_error);
					mysql_query("rollback");
				}
				$sql = "update sell set sell_total_out=sell_total_out-"
					. $rowItem["goods_price"] * $rowItem["item_amount"]
					. " where sell_id=" .$row["sell_id"];
				if (!mysql_query($sql))
				{
					die($sql . "<br />" . $conn->connect_error);
					mysql_query("rollback");
				}
			}
			$sql = "update sell set sell_Profit="
				. "sell_freight_in+sell_freight_out+"
				. "sell_total_in+sell_total_out";
			if (!mysql_query($sql))
			{
				die($sql . "<br />" . $conn->connect_error);
				mysql_query("rollback");
			}
		}
		mysql_query("commit");
		$rc = 1;
	}
	else if ($fpass != "")
	{
		$rc = 2;
	}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
	<form action = "./clear.php" method = "get">
		密码：<input type = "text" name = "fpass" />
		<input type = "submit" />
		<input type = "reset" />
	</form>
	<?php
	if ($rc == 1)
	{
		echo "成功";
	}
	else if ($rc == 2)
	{
		echo "密码错误";
	}
	?>
</body>
</html>