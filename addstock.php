<?php require_once("conn.php") ?>
<?php
	$fgoods = isset($_GET["fgoods"]) ? $_GET["fgoods"] : 0;
	$fuser = isset($_GET["fuser"]) ? $_GET["fuser"] : 0;
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="./dtctrl/calendar.css" >
<script type="text/javascript" src="./dtctrl/calendar.js" ></script>  
<script type="text/javascript" src="./dtctrl/calendar-zh.js" ></script>
<script type="text/javascript" src="./dtctrl/calendar-setup.js"></script>
<script type="text/javascript">
	function checkForm()
	{
		stock.famount.value = stock.famount.value.trim();
		if (stock.famount.value == 0)
		{
			alert("输入数量");
			return false;
		}
		stock.ftime.value = stock.ftime.value.trim();
		if (stock.ftime.value == "")
		{
			alert("输入时间");
			return false;
		}
		
		var chked = false;
  		var obj = document.getElementsByName("fcolor[]");
    	for (var i = 0; i < obj.length; i++)
  		{
  			if (obj[i].checked)
  			{
  				chked = true;
  				break;
  			}
  		}
  		if (!chked)
  		{
			alert("选择颜色");
			return false;
  		}
  		
  		chked = false;
  		var obj = document.getElementsByName("fsize[]");
    	for (var i = 0; i < obj.length; i++)
  		{
  			if (obj[i].checked)
  			{
  				chked = true;
  				break;
  			}
  		}
  		if (!chked)
  		{
			alert("选择尺寸");
			return false;
  		}
  		
		return true;
	}
</script>
</head>
<body>
<form method="post" name="stock" action="./stock.php" onsubmit="return checkForm()" >
	商品：<select name="fgoods">
	<?php
		$sql = "select goods_id, brand_name, cat1_name, cat2_name, goods_type "
            . "from (cat1 inner join cat2 on cat1.cat1_id = cat2.cat2_cat1_id) "
            . "inner join (brand inner join goods on brand.brand_id = goods.goods_brand_id) "
            . "on cat2.cat2_id = goods.goods_cat2_id "
            . "order by brand_name";
		$rs = mysql_query($sql);
		while ($row = mysql_fetch_array($rs))
		{
			echo "<option value=" . $row["goods_id"];
			if ($fgoods == $row["goods_id"])
			{
				echo " selected ";
			}
			echo  ">" . $row["goods_id"] . "[" . $row["cat1_name"] . ":" . $row["cat2_name"] . "]" . $row["brand_name"];
			if ($row["goods_type"] != "")
			{
				echo "-" . $row["goods_type"];
			}
		}
	?>
	</select><br />
	颜色：<br />
	<table>
	<?php
		$line_count = 10;
		$sql = "select * from color order by color_order";
		$rs = mysql_query($sql);
		if ($rs)
		{
			for ($i = 0; $i < mysql_num_rows($rs); $i++)
			{
				if (($i % $line_count) == 0)
				{
					echo "<tr>";
				}
				$row = mysql_fetch_array($rs);
				echo "<td><input type=\"checkbox\" name=\"fcolor[]\" value=" . $row["color_id"] . " >" . $row["color_name"] . "</td>";
				if (($i % $line_count) == $line_count - 1)
				{
					echo "</tr>";
				}
			}
		}
	?>
	</tr></table><br />
	尺寸：<br />
	<table>
	<?php
		$sql = "select * from size order by size_order";
		$rs = mysql_query($sql);
		if ($rs)
		{
			for ($i = 0; $i < mysql_num_rows($rs); $i++)
			{
				if (($i % $line_count) == 0)
				{
					echo "<tr>";
				}
				$row = mysql_fetch_array($rs);
				echo "<td><input type=\"checkbox\" name=\"fsize[]\" value=" . $row["size_id"] . " >" . $row["size_name"] . "</td>";
				if (($i % $line_count) == $line_count - 1)
				{
					echo "</tr>";
				}
			}
		}
	?>
	</tr></table><br />
	人员：<select name="fuser">
	<?php
		$sql = "select * from user";
		$rs = mysql_query($sql);
		while ($row = mysql_fetch_array($rs))
		{
			echo "<option value=" . $row["user_id"];
			if ($fuser == $row["user_id"])
			{
				echo " selected ";
			}
			echo ">" . $row["user_name"];
		}
	?>
	</select><br />
	时间：<input type="text" id="ftime" name="ftime" onclick="return showCalendar('ftime', 'y-mm-dd');" />&nbsp;&nbsp;&nbsp;&nbsp;
	数量：<input type="text" name="famount" />&nbsp;&nbsp;&nbsp;&nbsp;
	链接：<input type="text" size="50" name="furl" /><br />
	说明：<textarea name="fremark" rows="4" cols="80"></textarea>&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="hidden" name="faction" value="2" />
	<input type="submit" value="增加" />
	<input type="reset" />
</form>
</body>
</html>