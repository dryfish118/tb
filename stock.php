<?php require_once("sort_help.php"); ?>
<?php
    $faction = isset($_POST["faction"]) ? $_POST["faction"] : 0;
    $stock_id = isset($_POST["fid"]) ? $_POST["fid"] : 0;
    $goods_id = isset($_POST["fgoods"]) ? $_POST["fgoods"] : 0;
    $fcolors = isset($_POST["fcolor"]) ? $_POST["fcolor"] : 0;
    $fsizes = isset($_POST["fsize"]) ? $_POST["fsize"] : 0;
    $furl = isset($_POST["furl"]) ? $_POST["furl"] : "";
    $famount = isset($_POST["famount"]) ? $_POST["famount"] : 0;
    $fuser = isset($_POST["fuser"]) ? $_POST["fuser"] : "";
    $ftime = isset($_POST["ftime"]) ? $_POST["ftime"] : "";
    $fremark = isset($_POST["fremark"]) ? $_POST["fremark"] : "";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-cn" />
<?php
    if ($faction != 0)
    {
        mysql_query("begin");
        if ($faction == 1)
        {
            if ($stock_id != 0)
            {
                // 查找进货信息，goods2_id和stock_amount
                $sql = "select stock_goods2_id, stock_amount from stock where stock_id = $stock_id";
                $rs = mysql_query($sql);
                if (!$rs)
                {
                    mysql_query("rollback");
                    die($sql . "<br />" . $conn->connect_error . "<br />删除失败");
                }
                $row = mysql_fetch_array($rs);
                if (!$row)
                {
                    mysql_query("rollback");
                    die($sql . "<br />" . $conn->connect_error . "<br />删除失败");
                }
                $goods2_id = $row["stock_goods2_id"];
                $stock_amount = $row["stock_amount"];
                
                // 更新库存
                $sql = "update goods2 set goods2_in=goods2_in-$stock_amount"
                    . ", goods2_left=goods2_left-$stock_amount"
                    . " where goods2_id=$goods2_id";
                if (!mysql_query($sql))
                {
                    mysql_query("rollback");
                    die($sql . "<br />" . $conn->connect_error . "<br />删除失败");
                }
                // 查询库存
                $sql = "select color_name, size_name, " 
                    . "goods2_goods_id, goods2_in, goods2_out, goods2_left "
                    . "from color inner join ("
                    . "size inner join goods2 on size_id = goods2_size_id"
                    . ") on color.color_id = goods2.goods2_color_id "
                    . "where goods2_id = $goods2_id";
                $rs = mysql_query($sql);
                if (!$rs)
                {
                    mysql_query("rollback");
                    die($sql . "<br />" . $conn->connect_error . "<br />删除失败");
                }
                $row = mysql_fetch_array($rs);
                if (!$row)
                {
                    mysql_query("rollback");
                    die($sql . "<br />" . $conn->connect_error . "<br />删除失败");
                }
                $color_name = $row["color_name"];
                $size_name = $row["size_name"];
                $goods_id = $row["goods2_goods_id"];
                $goods2_in = $row["goods2_in"];
                $goods2_out = $row["goods2_out"];
                $goods2_left = $row["goods2_left"];
                
                // 检查库存是否正常
                //if ($goods2_in < 0 || $goods2_left < 0 || ($goods2_in + $goods2_out) != $goods2_left)
                //{
                //    mysql_query("rollback");
                //    die($sql . "<br />账本错误：总量（" . $goods2_in . "） 卖出（" . $goods2_out . "） 剩余（". $goods2_left . "）<br />" . $conn->connect_error . "<br />删除失败");
                //}
                
                // 如果库存是0,删除库存
                if ($goods2_in == 0)
                {
                    $sql = "delete from goods2 where goods2_id=$goods2_id";
                    if (!mysql_query($sql))
                    {
                        mysql_query("rollback");
                        die($sql . "<br />" . $conn->connect_error . "<br />删除失败");
                    }
                }
                
                // 删除进货
                $sql = "delete from stock where stock_id = $stock_id";
                if (!mysql_query($sql))
                {
                    mysql_query("rollback");
                    die($sql . "<br />" . $conn->connect_error . "<br />删除失败");
                }

                addHistory("删除", "进货", 
                    "编号（" . $goods_id .
                    "） 颜色（" . $color_name .
                    "） 尺寸（" . $size_name . 
                    "） 数量（" . $stock_amount . 
                    "）");
            }
        }
        else if ($faction == 2)
        {
            if ($goods_id != 0 && $fcolors != 0 && $fsizes != 0 &&
                $fuser != "" && $ftime != "" && $famount > 0)
            {
                // 为商品的每个颜色和尺寸创建一个单独的库存
                foreach ($fcolors as $color_id)
                {
                    foreach ($fsizes as $size_id)
                    {
                        // 查询是否已经有相同的库存
                        $sql = "select goods2_id from goods2 " . 
                            "where " .
                            "goods2_goods_id = $goods_id and " .
                            "goods2_color_id = $color_id and " .
                            "goods2_size_id = $size_id";
                        $rs = mysql_query($sql);
                        if (!$rs)
                        {
                            mysql_query("rollback");
                            die($sql . "<br />" . $conn->connect_error . "<br />添加失败");
                        }
                        $row = mysql_fetch_array($rs);
                        $goods2_id = 0;
                        if ($row)
                        {
                            // 已经具有类似的库存，现在仅仅是更新就好
                            $goods2_id = $row["goods2_id"];
                            $sql = "update goods2 set goods2_left = goods2_left + $famount, " .
                                "goods2_in = goods2_in + $famount where goods2_id= $goods2_id";
                            if (!mysql_query($sql))
                            {
                                mysql_query("rollback");
                                die($sql . "<br />" . $conn->connect_error . "<br />添加失败");
                            }
                        }
                        else
                        {
                            // 没有类似库存，先在goods2中添加库存
                            $sql = "insert into goods2(goods2_goods_id, goods2_color_id, " .
                                "goods2_size_id, goods2_left, goods2_in) " .
                                "values('$goods_id', '$color_id', '$size_id', '$famount', '$famount')";
                            if (!mysql_query($sql))
                            {
                                mysql_query("rollback");
                                die($sql . "<br />" . $conn->connect_error . "<br />添加失败");
                            }
                            $sql = "select goods2_id from goods2 " . 
                                "where goods2_goods_id = $goods_id and " . 
                                "goods2_color_id = $color_id and " .
                                "goods2_size_id= $size_id";
                            $rs = mysql_query($sql);
                            if (!$rs)
                            {
                                mysql_query("rollback");
                                die($sql . "<br />" . $conn->connect_error . "<br />添加失败");
                            }
                            $row = mysql_fetch_array($rs);
                            if (!$row)
                            {
                                mysql_query("rollback");
                                die($sql . "<br />" . $conn->connect_error . "<br />添加失败");
                            }
                            $goods2_id = $row["goods2_id"];
                        }
                        
                        // 添加进货信息
                        $sql = "insert into stock(stock_user_id, stock_goods2_id, " .
                            "stock_time, stock_amount, stock_remark) " .
                            "values('$fuser', '$goods2_id', '$ftime', '$famount', '$fremark')";
                        if (!mysql_query($sql))
                        {
                            mysql_query("rollback");
                            die($sql . "<br />" . $conn->connect_error . "<br />添加失败");
                        }
                        
                        // 查询库存
                        $sql = "select color_name, size_name, " 
                            . "goods2_goods_id, goods2_in, goods2_out, goods2_left "
                            . "from color inner join ("
                            . "size inner join goods2 on size_id = goods2_size_id"
                            . ") on color.color_id = goods2.goods2_color_id "
                            . "where goods2_id = $goods2_id";
                        $rs = mysql_query($sql);
                        if (!$rs)
                        {
                            mysql_query("rollback");
                            die($sql . "<br />" . $conn->connect_error . "<br />添加失败");
                        }
                        $row = mysql_fetch_array($rs);
                        if (!$row)
                        {
                            mysql_query("rollback");
                            die($sql . "<br />" . $conn->connect_error . "<br />添加失败");
                        }
                        
                        addHistory("添加", "进货",
                            "编号（" . $row["goods2_goods_id"] .
                            "） 颜色（" . $row["color_name"] .
                            "） 尺寸（" . $row["size_name"] . 
                            "） 数量（" . $famount . 
                            "）");
                    }
                }
            }
        }
        else
        {
            mysql_query("rollback");
            die("未知操作");
        }
        mysql_query("commit");
    }
?>
<link type="text/css" rel="stylesheet" href="./dtctrl/calendar.css" >
<script type="text/javascript" src="./dtctrl/calendar.js" ></script>  
<script type="text/javascript" src="./dtctrl/calendar-zh.js" ></script>
<script type="text/javascript" src="./dtctrl/calendar-setup.js"></script>
<script type="text/javascript" src="./dxx.js" ></script>
<script type="text/javascript">
    function deleteStock(fid, fname)
    {
        if (confirm("确定要删除'" + fname + "'吗？"))
        {
            stock.fid.value = fid;
            stock.submit();
        }
    }
    function addForm()
    {
        document.location.href = './addstock.php';
    }
</script>
<title>进货</title>
</head>
<body>
<?php
    defineSortForm();
?>
    <form method="post" name="stock" action="./stock.php" >
        <input type="hidden" name="faction" value="1" />
        <input type="hidden" name="fid" value="0" />
    </form>
    <input type="button" name="fadd" value="增加" onclick="addForm()" />
    <p />
    <table frame="border" rules="all">
        <tr>
<?php
            $page = "stock";
            $default_dir = array(1, 3, 2, 2, 2, 2, 2, 2, 3, 2, 2, 0, 0);
            $headers = array("*", "10", "编号", "40", "品牌", "50", "大类", "60",
                "小类", "100", "型号", "100", "颜色", "60", "尺寸", "60",
                "时间", "80", "人员", "50", "数量", "40", "链接", "350",
                "说明", "200");
             generalHeader($page, $default_dir, $headers);
?>
        </tr>
<?php
	$sql = "select stock_id, brand_name, cat1_name, cat2_name, "
		. "goods_id, goods_type, color_id, color_name, "
		. "color_order, size_id, size_name, size_order, "
		. "date_format(stock_time, '%Y-%m-%d') as stock_time, "
		. "user_id, user_name, stock_amount, goods2_url, stock_remark "
		. "from user inner join ((color inner join (size inner join "
		. "(((cat1 inner join cat2 on cat1.cat1_id=cat2.cat2_cat1_id) "
		. "inner join (brand inner join goods on brand.brand_id=goods.goods_brand_id) "
		. "on cat2.cat2_id=goods.goods_cat2_id) inner join goods2 on "
		. "goods.goods_id=goods2.goods2_goods_id) on size.size_id=goods2.goods2_size_id) "
		. "on color.color_id=goods2.goods2_color_id) "
		. "inner join stock on goods2.goods2_id=stock.stock_goods2_id) "
		. "on user.user_id=stock.stock_user_id ";
	$sort_key = array("", "goods_id", "brand_name", "cat1_name", 
		"cat2_name", "goods_type", "color_order", "size_order", 
		"stock_time", "user_name", "stock_amount", "", "");
	$sql = $sql . appendSortSql($page, $default_dir, $sort_key);
	$rs = mysql_query($sql);
	if ($rs)
	{
		while ($row = mysql_fetch_array($rs))
		{
			  echo "        <tr>\n";
			  echo "            <td><a href='javascript:void(0)' onclick='deleteStock(" .
				  $row["stock_id"]. ", \"" . $row["brand_name"] . "\")'>X</a></td>\n";
			  echo "            <td>" . $row["goods_id"] . "</td>\n";
			  echo "            <td>" . $row["brand_name"] . "</td>\n";
			  echo "            <td>" . $row["cat1_name"] . "</td>\n";
			  echo "            <td>" . $row["cat2_name"] . "</td>\n";
			  echo "            <td>" . $row["goods_type"] . "</td>\n";
			  echo "            <td>" . $row["color_name"] . "</td>\n";
			  echo "            <td>" . $row["size_name"] . "</td>\n";
			  echo "            <td>" . $row["stock_time"] . "</td>\n";
			  echo "            <td>" . $row["user_name"] . "</td>\n";
			  echo "            <td>" . $row["stock_amount"] . "</td>\n";
			  echo "            <td><a href=\"" . $row["goods2_url"] . "\" target=\"_blank\">" . $row["goods2_url"] . "</a></td>\n";
			  echo "            <td>" . $row["stock_remark"] . "</td>\n";
			  echo "        </tr>\n";
		}
	}
?>
    </table>
</body>
</html>