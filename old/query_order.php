<?php require_once("conn.php") ?>
<?php

function xmlEncode($tag)
{
	$tag = str_replace("&", "&amp;", $tag);
	$tag = str_replace("<", "&lt;", $tag);
	$tag = str_replace(">", "&gt;", $tag);
	$tag = str_replace("'", "&apos;", $tag);
	$tag = str_replace('"', "&quot;", $tag);
	return iconv("GB2312", "UTF-8", $tag);
}

if (isset($_POST["taobao"]))
{
	$taobao = $_POST["taobao"];
	if (strlen($taobao))
	{
		$taobao = urldecode($taobao);
		$taobao = iconv("UTF-8", "GB2312", $taobao);
		$sql = "select client_id from client where client_taobao like '$taobao'";
		$rs = mysql_query($sql);
		while ($row = mysql_fetch_array($rs))
		{
			echo $row["client_id"];
		}
	}
}
else if (isset($_POST["order"]))
{
	if (strlen($_POST["order"]))
	{
		$sql = "select * from sell where sell_card = '" . $_POST["order"] . "'";
		$rs = mysql_query($sql);
		if ($rs && mysql_num_rows($rs))
		{
			echo "1";
		}
	}
}
else if (isset($_POST["orders"]))
{
	if (strlen($_POST["orders"]))
	{
		$orders = explode("-", $_POST["orders"]);
		foreach ($orders as $order)
		{
			$sql = "select * from sell where sell_card = '$order'";
			$rs = mysql_query($sql);
			if ($rs && mysql_num_rows($rs))
			{
				echo "$order<br/>";
			}
			else
			{
				echo "<strong>$order</strong><br/>";
			}
		}
	}
}
else if (isset($_POST["goods_id"]))
{
	$goods_id = $_POST["goods_id"];
	if (strlen($goods_id))
	{
		$sql = "SELECT goods2_id, brand_name, color_name, size_name, goods2_left "
			. "FROM size "
			. "INNER JOIN ("
				. "color "
				. "INNER JOIN ("
					. "brand "
					. "INNER JOIN ("
						. "goods "
						. "INNER JOIN goods2 ON goods.goods_id = goods2.goods2_goods_id"
					. ") ON brand.brand_id = goods.goods_brand_id"
				. ") ON color.color_id = goods2.goods2_color_id"
			. ") ON size.size_id = goods2.goods2_size_id "
			. "WHERE goods2_left > 0 "
			. "AND goods_id = $goods_id";
		$rs = mysql_query($sql);
		if ($rs && mysql_num_rows($rs))
		{
			echo '<?xml version="1.0" encoding="utf-8"?>';
			echo "<goods>";
			while ($row = mysql_fetch_array($rs))
			{
				echo "<goods2>";
				echo "<id>" . $row["goods2_id"] . "</id>";
				echo "<brand>" . xmlEncode($row["brand_name"]) . "</brand>";
				echo "<color>" . xmlEncode($row["color_name"]) . "</color>";
				echo "<size>" . xmlEncode($row["size_name"]) . "</size>";
				echo "<left>" . xmlEncode($row["goods2_left"]) . "</left>";
				echo "</goods2>";
			}
			echo "</goods>";
		}
	}
}


?>