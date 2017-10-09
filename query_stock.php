<?php require_once("sort_help.php") ?>
<?php
	$time1 = isset($_POST["ftime1"]) ? $_POST["ftime1"] : "";
	$time2 = isset($_POST["ftime2"]) ? $_POST["ftime2"] : "";
	$user = isset($_POST["fuser"]) ? $_POST["fuser"] : "";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="./dtctrl/calendar.css" >
<script type="text/javascript" src="./dtctrl/calendar.js" ></script>  
<script type="text/javascript" src="./dtctrl/calendar-zh.js" ></script>
<script type="text/javascript" src="./dtctrl/calendar-setup.js"></script>
<script type="text/javascript" src="./dxx.js" ></script>
<script type="text/javascript">
    function clear_form()
    {
        query_cond.ftime1.value = "";
        query_cond.ftime2.value = "";
        query_cond.fuser.options[0].selected = true;
    }
</script>
<title>进货查询</title>
</head>
<body>
<?php
    defineSortForm();
?>
    <form method="post" name="query_cond" action="./query_stock.php">
    <table frame="border" rules="all" id="table_condition">
        <tr>
            <td>
                    起始时间：<input type="text" id="ftime1" name="ftime1" value = "<?php echo $time1; ?>" onclick="return showCalendar('ftime1', 'y-mm-dd');" />
            </td>
            <td>
                    结束时间：<input type="text" id="ftime2" name="ftime2" value = "<?php echo $time2; ?>" onclick="return showCalendar('ftime2', 'y-mm-dd');" />
            </td>
            <td>
                    人员：<select name="fuser">
                        <option>
                    <?php
                        $sql = "select * from user";
                        $rs = mysql_query($sql);
						if ($rs)
						{
							while ($row = mysql_fetch_array($rs))
							{
								echo "<option value=" . $row["user_id"];
								if ($user == $row["user_id"])
								{
									echo " selected ";
								}
								echo ">" . $row["user_name"];
							}
						}
                    ?>
                    <input type="submit" value="查询" />
                    <input type="button" value="清空" onclick="clear_form()" />
                    </select>
            </td>
            <td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</td>
            <td>数量：</td><td>0</td>
            <td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</td>
            <td>金额：</td><td>0</td>
        </tr>
    </table>
    </form>
	<p>
    <table frame="border" rules="all">
        <tr>
<?php
            $page = "query_stock";
            $default_dir = array(2, 2, 2, 2, 2, 2, 3, 2, 2, 2, 2);
            $headers = array("品牌", "80", "大类", "60", "小类", "100", 
                "型号", "100", "颜色", "60", "尺寸", "60", "时间", "100", 
                "人员", "80", "进货", "40", "价格", "40", "合计", "40");
             generalHeader($page, $default_dir, $headers);
?>
        </tr>
<?php
	$sql = "select brand_name, cat1_name, cat2_name, goods_type, color_name, size_name, "
		. "date_format(stock_time, '%Y-%m-%d') as stock_time, "
		. "user_name, stock_amount, goods_price, stock_amount*goods_price as total "
		. "from user inner join (size inner join (((cat1 inner join cat2 "
		. "on cat1.cat1_id = cat2.cat2_cat1_id) "
		. "inner join (brand inner join goods on brand.brand_id = goods.goods_brand_id) "
		. "on cat2.cat2_id = goods.goods_cat2_id) "
		. "inner join ((color inner join goods2 on color.color_id = goods2.goods2_color_id) "
		. "inner join stock on goods2.goods2_id = stock.stock_goods2_id) "
		. "on goods.goods_id = goods2.goods2_goods_id) on size.size_id = goods2.goods2_size_id) "
		. "on user.user_id = stock.stock_user_id ";
	
	$sqlCond = "";
	if ($time1 != "")
	{
		if ($time2 != "")
		{
			$sqlCond = "where (stock_time >= '$time1' and stock_time <= '$time2') ";
		}
		else
		{
			$sqlCond = "where (stock_time='$time1') ";
		}
	}

	if ($user != "")
	{
		if ($sqlCond != "")
		{
			$sqlCond .= " and ";
		}
		else
		{
			$sqlCond .= "where ";
		}
		$sqlCond .= "user_id = $user ";
	}

	if ($sqlCond != "")
	{
		$sql .= $sqlCond;
	}

	$sort_key = array("brand_name", "cat1_name", "cat2_name", "goods_type",
		"color_order", "size_order", "stock_time", "user_name",
		"stock_amount", "goods_price", "total");
	$sql = $sql . appendSortSql($page, $default_dir, $sort_key);
	
	$rs = mysql_query($sql);
	$stock_amount = 0;
	$stock_total = 0;
	if ($rs)
	{
		while ($row = mysql_fetch_array($rs))
		{
			$stock_amount += $row["stock_amount"];
			$stock_total += $row["total"];
			  echo "<tr><td>";
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
			  echo $row["stock_time"];
			  echo "</td><td>";
			  echo $row["user_name"];
			  echo "</td><td>";
			  echo $row["stock_amount"];
			  echo "</td><td>";
			  echo $row["goods_price"];
			  echo "</td><td>";
			  echo round($row["total"], 2);
			  echo "</td></tr>";
		}
	}
?>
    </table>
</body>
<script type="text/javascript">
    var obj_table_condition = document.getElementById("table_condition");
    obj_table_condition.rows[0].cells[5].innerHTML = '<?php echo $stock_amount; ?>';
    obj_table_condition.rows[0].cells[8].innerHTML = '<?php echo $stock_total; ?>';
</script>
</html>