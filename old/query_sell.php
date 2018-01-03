<?php require_once("sort_help.php") ?>
<?php
	$goods = isset($_GET["fgoods"]) ? $_GET["fgoods"] : 0;
	$sell_id = isset($_GET["fsell_id"]) ? $_GET["fsell_id"] : 0;
	$brand = isset($_GET["fbrand"]) ? $_GET["fbrand"] : 0;
	$cat1 = isset($_GET["fcat1"]) ? $_GET["fcat1"] : 0;
	$cat2 = isset($_GET["fcat2"]) ? $_GET["fcat2"] : 0;
	$type = isset($_GET["ftype"]) ? $_GET["ftype"] : "";
	$color = isset($_GET["fcolor"]) ? $_GET["fcolor"] : 0;
	$size = isset($_GET["fsize"]) ? $_GET["fsize"] : 0;
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-cn" />
<script type="text/javascript" src="./dxx.js" ></script>
<title>销售查询</title>
</head>
<body>
<?php
    defineSortForm();
?>
<form method="get" action="./query_sell.php">
    编号：<input type="text" name="fgoods" value = "<?php echo $goods; ?>" />
</form>
<form method="get" action="./query_sell.php">
    品牌：<select name="fbrand" onchange="submit()">
<?php
    $sql = "select * from brand order by brand_id desc";
    $rs = mysql_query($sql);
    while ($row = mysql_fetch_array($rs))
    {
        echo "<option value=\"" . $row["brand_id"] . "\"";
        if ($brand == $row["brand_id"])
        {
            echo " selected ";
        }
        echo  ">" . $row["brand_name"];
    }
?>
    </select>
</form>
<form method="get" action="./query_sell.php">
    型号：<select name = "ftype" onchange="submit()">
<?php
    $sql = "select distinct(goods_type) as type from goods order by type";
    $rs = mysql_query($sql);
    while ($row = mysql_fetch_array($rs))
    {
        echo "<option value=\"" . $row["type"] . "\"";
        if ($type == $row["type"])
        {
            echo " selected ";
        }
        echo ">" . $row["type"];
    }
?>
    </select>
</form>
<form method="get" action="./query_sell.php">
    大类：<select name="fcat1" onchange="submit()">
<?php
    $sql = "select * from cat1";
    $rs = mysql_query($sql);
    while ($row = mysql_fetch_array($rs))
    {
        echo "<option value=\"" . $row["cat1_id"] . "\"";
        if ($cat1 == $row["cat1_id"])
        {
            echo " selected ";
        }
        echo ">" . $row["cat1_name"];
    }
?>
    </select>
</form>
<form method="get" action="./query_sell.php">
    小类：<select name="fcat2" onchange="submit()">
<?php
    $sql = "select cat1_name, cat2_id, cat2_name from cat1 inner join "
        ."cat2 on cat1.cat1_id = cat2.cat2_cat1_id";
    $rs = mysql_query($sql);
    while ($row = mysql_fetch_array($rs))
    {
        echo "<option value=\"" . $row["cat2_id"] . "\"";
        if ($cat2 == $row["cat2_id"])
        {
            echo " selected ";
        }
        echo ">[" . $row["cat1_name"] . "]" . $row["cat2_name"];
    }
?>
    </select>
</form>
<form method="get" action="./query_sell.php">
    颜色：<select name = "fcolor" onchange="submit()">
<?php
    $sql = "select * from color order by color_order";
    $rs = mysql_query($sql);
    while ($row = mysql_fetch_array($rs))
    {
        echo "<option value=\"" . $row["color_id"] . "\"";
        if ($color == $row["color_id"])
        {
            echo " selected ";
        }
        echo ">" . $row["color_name"];
    }
?>
    </select>
</form>
<form method="get" action="./query_sell.php">
    尺寸：<select name = "fsize" onchange="submit()">
<?php
    $sql = "select * from size order by size_order";
    $rs = mysql_query($sql);
    while ($row = mysql_fetch_array($rs))
    {
        echo "<option value=\"" . $row["size_id"] . "\"";
        if ($size == $row["size_id"])
        {
            echo " selected ";
        }
        echo ">" . $row["size_name"];
    }
?>
    </select>
</form>
    <p>
    <table frame="border" rules="all">
        <tr>
<?php
            $page = "query_sell";
            $default_dir = array(3, 2, 2, 2, 2, 2, 2, 2, 2, 3, 2, 2);
            $headers = array("编号", "50", "品牌", "100", "大类", "100", 
                "小类", "150", "型号", "100", "颜色", "80", "尺寸", "80", 
                "人员", "80", "客户", "180", "时间", "100", "价格", "50", "销量", "40");
             generalHeader($page, $default_dir, $headers);
?>
        </tr>
<?php
	$sql = "select goods_id, brand_name, cat1_name, cat2_name, color_name, color_order, "
		. "size_name, size_order, goods_type, user_name, client_taobao, client_name, "
		. "date_format(sell_time, '%Y-%m-%d') as sell_time, "
		. "item_id, item_price, item_amount "
		. "from (cat1 inner join cat2 on cat1.cat1_id = cat2.cat2_cat1_id) "
		. "inner join (brand inner join "
		. "(size inner join "
		. "((user inner join "
		. "(client inner join sell on client.client_id = sell.sell_client_id) "
		. "on user.user_id = sell.sell_sell_user_id) "
		. "inner join (goods inner join "
		. "((color inner join goods2 on color.color_id = goods2.goods2_color_id) "
		. "inner join item on goods2.goods2_id = item.item_goods2_id) "
		. "on goods.goods_id = goods2.goods2_goods_id) "
		. "on sell.sell_id = item.item_sell_id) "
		. "on size.size_id = goods2.goods2_size_id) "
		. "on brand.brand_id = goods.goods_brand_id) "
		. "on cat2.cat2_id = goods.goods_cat2_id ";

	if ($sell_id != 0)
	{
		$sql .= "where sell_id = $sell_id";
	}
	elseif ($goods != 0)
	{
		$sql .= "where goods_id = $goods";
	}
	else if ($brand != 0)
	{
		$sql .= "where brand_id = $brand";
	}
	else if ($cat1 != 0)
	{
		$sql .= "where cat1_id = $cat1";
	}
	else if ($cat2 != 0)
	{
		$sql .= "where cat2_id = $cat2";
	}
	else if ($type != "")
	{
		$sql .= "where goods_type like '$type'";
	}
	else if ($color != 0)
	{
		$sql .= "where color_id = $color";
	}
	else if ($size != 0)
	{
		$sql .= "where size_id = $size";
	}

	$sort_key = array("goods_id", "brand_name", "cat1_name", "cat2_name",
		"goods_type", "color_order", "size_order", "user_name",
		"client_taobao", "sell_time", "item_price", "item_amount");
	$sql = $sql . appendSortSql($page, $default_dir, $sort_key);
	$rs = mysql_query($sql);
	if ($rs)
	{
		while ($row = mysql_fetch_array($rs))
		{
			  echo "<tr><td>";
			  echo $row["goods_id"];
			  echo "</td><td>";
			  echo $row["brand_name"];
			  echo "</td><td>";
			  echo $row["cat1_name"];
			  echo "</td><td>";
			  echo $row["cat2_name"];
			  echo "</td><td>";
			  echo $row["goods_type"];
			  echo "</td><td>";
			  echo $row["color_name"];
			  echo "</td><td>";
			  echo $row["size_name"];
			  echo "</td><td>";
			  echo $row["user_name"];
			  echo "</td><td>";
			  if (!empty($row["client_taobao"]))
			  {
				  echo "[" . $row["client_taobao"] . "]" . $row["client_name"];
			  }
			  else
			  {
				  echo $row["client_name"];
			  }
			  echo "</td><td>";
			  echo $row["sell_time"];
			  echo "</td><td>";
			  echo $row["item_price"];
			  echo "</td><td>";
			  echo $row["item_amount"];
			  echo "</td></tr>";
		}
	}
?>
    </table>
</body>
</html>