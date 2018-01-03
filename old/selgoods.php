<?php require_once("sort_help.php"); ?>
<?php
	$goods = isset($_POST["fgoods"]) ? $_POST["fgoods"] : 0;
	$brand = isset($_POST["fbrand"]) ? $_POST["fbrand"] : 0;
	$cat1 = isset($_POST["fcat1"]) ? $_POST["fcat1"] : 0;
	$cat2 = isset($_POST["fcat2"]) ? $_POST["fcat2"] : 0;
	$type = isset($_POST["ftype"]) ? $_POST["ftype"] : "";
	$color = isset($_POST["fcolor"]) ? $_POST["fcolor"] : 0;
	$size = isset($_POST["fsize"]) ? $_POST["fsize"] : 0;
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-cn" />
<script type="text/javascript" src="./dxx.js"></script>
<script type="text/javascript">
	function onSelectedGoods(id)
	{
		addsell.fgoods2.value = id;
		addsell.submit();
	}
</script>
</head>
<body>
	<form method="post" name="addsell" action="./addsell.php" >
        <input type="hidden" name="foperator" value="4" />
        <input type="hidden" name="fgoods2" value="0" />
	</form>
	<form method="post" action="./selgoods.php">
		<div>编号：<input type="text" name="fgoods" value="<?php echo $goods ?>" /></div>
	</form>
	<form method="post" action="./selgoods.php">
		<div>品牌：<select name="fbrand" onchange="submit()">
<?php
						$sql = "select * from brand order by brand_id desc";
						$rs = mysql_query($sql);
						while ($row = mysql_fetch_array($rs))
						{
							echo "<option value=\"" . $row["brand_id"] . "\"";
							if ($row["brand_id"] == $brand)
							{
								echo " selected";
							}
							echo  ">" . $row["brand_name"];
						}
?>
					</select></div>
	</form>
	<form method="post" action="./selgoods.php">
		<div>大类：<select name="fcat1" onchange="submit()">
<?php
						$sql = "select * from cat1";
						$rs = mysql_query($sql);
						while ($row = mysql_fetch_array($rs))
						{
							echo "<option value=\"" . $row["cat1_id"] . "\"";
							if ($row["cat1_id"] == $cat1)
							{
								echo " selected ";
							}
							echo ">" . $row["cat1_name"];
						}
?>
					</select></div>
	</form>
	<form method="post" action="./selgoods.php">
		<div>大类：<select name="fcat2" onchange="submit()">
<?php
						$sql = "select * from cat2";
						$rs = mysql_query($sql);
						while ($row = mysql_fetch_array($rs))
						{
							echo "<option value=\"" . $row["cat2_id"] . "\"";
							if ($row["cat2_id"] == $cat2)
							{
								echo " selected ";
							}
							echo ">" . $row["cat2_name"];
						}
?>
					</select></div>
	</form>
	<form method="post" action="./selgoods.php">
		<div>型号：<select name="ftype" onchange="submit()">
<?php
						$sql = "select distinct(goods_type) as type from goods";
						$rs = mysql_query($sql);
						while ($row = mysql_fetch_array($rs))
						{
							if ($row["type"] != "")
							{
								echo "<option value=\"" . $row["type"] . "\"";
								if ($row["type"] == $type)
								{
									echo " selected ";
								}
								echo ">" . $row["type"];
							}
						}
?>
					</select></div>
	</form>
	<form method="post" action="./selgoods.php">
		<div>颜色：<select name="fcolor" onchange="submit()">
<?php
						$sql = "select * from color order by color_order";
						$rs = mysql_query($sql);
						while ($row = mysql_fetch_array($rs))
						{
							echo "<option value=\"" . $row["color_id"] . "\"";
							if ($row["color_id"] == $color)
							{
								echo " selected ";
							}
							echo ">" . $row["color_name"];
						}
?>
					</select></div>
	</form>
	<form method="post" action="./selgoods.php">
		<div>尺寸：<select name="fsize" onchange="submit()">
<?php
						$sql = "select * from size order by size_order";
						$rs = mysql_query($sql);
						while ($row = mysql_fetch_array($rs))
						{
							echo "<option value=\"" . $row["size_id"] . "\"";
							if ($row["size_id"] == $size)
							{
								echo " selected ";
							}
							echo ">" . $row["size_name"];
						}
?>
					</select></div>
	</form>
	<p></p>
	<table frame="border" rules="all">
		<tr>
<?php
			$page = "selgoods";
	    	$default_dir = array(1, 3, 2, 2, 2, 2, 2, 2);
	    	$headers = array("*", "30", "编号", "40", "品牌", "150", "大类", "80",
	    		"小类", "150", "型号", "100", "颜色", "100", "尺寸", "100");
		 	generalHeader($page, $default_dir, $headers);
?>
		</tr>
	<?php
		$sql = "select goods_id, brand_name, cat1_name, cat2_name, goods2_left, "
		    . "goods_type, color_order, color_name, size_order, size_name, goods2_id "
		    . "from size inner join (color inner join "
		    . "(((cat1 inner join cat2 on cat1.cat1_id = cat2.cat2_cat1_id) "
		    . "inner join (brand inner join goods on brand.brand_id = goods.goods_brand_id) "
		    . "on cat2.cat2_id = goods.goods_cat2_id) "
		    . "inner join goods2 on goods.goods_id = goods2.goods2_goods_id) "
		    . "on color.color_id = goods2.goods2_color_id) on size.size_id = goods2.goods2_size_id "
			. "where goods2_left > 0 ";

		if ($goods != 0)
		{
			$sql .= "and goods_id = $goods";
		}
		else if ($brand != 0)
		{
			$sql .= "and brand_id = $brand";
		}
		else if ($cat1 != 0)
		{
			$sql .= "and cat1_id = $cat1";
		}
		else if ($cat2 != 0)
		{
			$sql .= "and cat2_id = $cat2";
		}
		else if ($type != "")
		{
			$sql .= "and goods_type like '$type'";
		}
		else if ($color != 0)
		{
			$sql .= "and color_id = $color";
		}
		else if ($size != 0)
		{
			$sql .= "and size_id = $size";
		}

		$sort_key = array("", "goods_id", "brand_name", "cat1_name", 
			"cat2_name", "goods_type", "color_order", "size_order");
		$sql = $sql . appendSortSql($page, $default_dir, $sort_key);
		$rs = mysql_query($sql);
		
		while ($row = mysql_fetch_array($rs))
		{
	  		echo "<tr><td><input type=\"button\" value=\"选\" onclick=\"onSelectedGoods(" . $row["goods2_id"] . ")\" />";
	  		echo "</td><td>";
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
	  		echo "</td></tr>";
		}
	?>
	</table>
</body>
</html>