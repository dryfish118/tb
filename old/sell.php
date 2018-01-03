<?php require_once("sort_help.php"); ?>
<?php
    $operator = isset($_POST["foperator"]) ? $_POST["foperator"] : OPERATOR_INVALID;
    $id = isset($_POST["fid"]) ? $_POST["fid"] : 0;
    $card = isset($_POST["fcard"]) ? $_POST["fcard"] : "";
    $time = isset($_POST["ftime"]) ? $_POST["ftime"] : "";
    $items = isset($_POST["fitems"]) ? $_POST["fitems"] : "";
    $user_sell = isset($_POST["fuser_sell"]) ? $_POST["fuser_sell"] : 0;
    $user_freight = isset($_POST["fuser_freight"]) ? $_POST["fuser_freight"] : 0;
    $client = isset($_POST["fclient"]) ? $_POST["fclient"] : 0;
    $freightin = isset($_POST["ffreightin"]) ? $_POST["ffreightin"] : 0;
    $freightout = isset($_POST["ffreightout"]) ? $_POST["ffreightout"] : 0;
    $remark = isset($_POST["fremark"]) ? $_POST["fremark"] : "";
    $curpage = isset($_POST["fcurpage"]) ? $_POST["fcurpage"] : 0;

    if ($operator != OPERATOR_INVALID)
    {
        mysql_query("begin");
        if ($operator == OPERATOR_REMOVE)
        {
            if ($id != 0)
            {
                $sql = "select * from item where item_sell_id = $id";
                $rs = mysql_query($sql);
                if (!$rs)
                {
                    mysql_query("rollback");
                    die($sql . "<br />" . $conn->connect_error . "<br />删除失败");
                }
                while ($row = mysql_fetch_array($rs))
                {
                    $sql = "update goods2 set goods2_out = goods2_out + " . $row["item_amount"]
                        . ", goods2_left = goods2_left + " . $row["item_amount"]
                        . " where goods2_id = " . $row["item_goods2_id"];
                    if (!mysql_query($sql))
                    {
                        mysql_query("rollback");
                        die($sql . "<br />" . $conn->connect_error . "<br />删除失败");
                    }
                }
                $sql = "delete from item where item_sell_id = $id";
				if (!mysql_query($sql))
                {
                    mysql_query("rollback");
                    die($sql . "<br />" . $conn->connect_error . "<br />删除失败");
                }
				
				$sql = "select sell_card from sell where sell_id = $id";
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
				$sell_card = $row["sell_card"];
				
                $sql = "delete from sell where sell_id = $id";
                if (!mysql_query($sql))
                {
                    mysql_query("rollback");
                    die($sql . "<br />" . $conn->connect_error . "<br />删除失败");
                }
                
                addHistory("删除", "销售", $sell_card);
            }
        }
        else if ($operator == OPERATOR_ADD)
        {
            if ($card != "" && $time != "" && $items != "")
            {
                $sql = "select * from sell where sell_card = '$card'";
                $rs = mysql_query($sql);
                if (!$rs)
                {
                    mysql_query("rollback");
                    die($sql . "<br />" . $conn->connect_error . "<br />添加失败");
                }
                if (mysql_fetch_array($rs))
                {
                    mysql_query("rollback");
                    die($sql . "<br />" . $conn->connect_error . "<br />添加失败");
                }
                
                if ($card == "00000000000000")
                {
                    $strCard = "";
                    for ($idx = 1; $idx < 100000; $idx++)
                    {
                        $strCard = strval($idx);
                        while (strlen($strCard) < 14)
                        {
                            $strCard = "0" . $strCard;
                        }
                        $sql = "select * from sell where sell_card = '$strCard'";
                        $rs = mysql_query($sql);
                        if (!mysql_fetch_array($rs))
                        {
                            break;
                        }
                    }
                    $card = $strCard;
                }
                
                $sql = "insert into sell(sell_sell_user_id,sell_freight_user_id,"
                    . "sell_client_id,sell_time,sell_card,"
                    . "sell_freight_in,sell_freight_out,sell_remark) "
                    . "values('$user_sell', '$user_freight', "
                    . "'$client', '$time', '$card', "
                    . "'$freightin', '$freightout', '$remark')";
                if (!mysql_query($sql))
                {
                    mysql_query("rollback");
                    die($sql . "<br />" . $conn->connect_error . "<br />添加失败");
                }
                
                $sql = "select max(sell_id) as max_id from sell";
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
                $sell_id = $row["max_id"];
                
                $totalin = 0;
                $fitem = explode(" ", $items);
                $size = (count($fitem) - 1) / 3;
                for ($i = 0; $i < $size; $i++)
                {
                    $totalin = $totalin + $fitem[$i * 3 + 1] * $fitem[$i * 3 + 2];
                    $sql = "insert into item(item_sell_id,item_goods2_id,item_price,item_amount) "
                        . "values('$sell_id', '" . $fitem[$i * 3]
                        . "', '" . $fitem[$i * 3 + 1] . "', '" . $fitem[$i * 3 + 2] . "')";
                    if (!mysql_query($sql))
                    {
                        mysql_query("rollback");
                        die($sql . "<br />" . $conn->connect_error . "<br />添加失败");
                    }
                    $sql = "update goods2 set goods2_out=goods2_out-" . $fitem[$i * 3 + 2]
                        . ", goods2_left=goods2_left-" . $fitem[$i * 3 + 2]
                        . " where goods2_id=" . $fitem[$i * 3];
                    if (!mysql_query($sql))
                    {
                        mysql_query("rollback");
                        die($sql . "<br />" . $conn->connect_error . "<br />添加失败");
                    }
                }
                $sql = "select sum(goods_price*item_amount) as totalout "
                    ."from goods inner join (goods2 inner join item on goods2.goods2_id=item.item_goods2_id) "
                    . "on goods.goods_id=goods2.goods2_goods_id where item_sell_id=" . $sell_id;
                $rs = mysql_query($sql);
                if (!mysql_query($sql))
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
                $totalout = -$row["totalout"];
                
                $sql = "update sell set sell_total_in=" . $totalin
                    . ", sell_total_out=" . $totalout
                    . ", sell_profit=sell_total_in+sell_total_out+sell_freight_in+sell_freight_out"
                     . " where sell_id=" . $sell_id;
                if (!mysql_query($sql))
                {
                    mysql_query("rollback");
                    die($sql . "<br />" . $conn->connect_error . "<br />添加失败");
                }
                
                addHistory("添加", "销售", $card);
            }
        }
        else
        {
            mysql_query("rollback");
            die("未知操作");
        }
        mysql_query("commit");
    }
	
	$sql = "select count(sell_id) as total_count , sum(sell_total_in) as sell_total, sum(sell_total_out) as sell_out from sell";
	$rs = mysql_query($sql);
	$total_count = 0;
	$sell_total = 0;
    $sell_out = 0;
	if ($rs && $row = mysql_fetch_array($rs))
    {
        $total_count = $row["total_count"];
		$sell_total = $row["sell_total"];
		$sell_out = $row["sell_out"];
    }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-cn" />
<script type="text/javascript" src="./dxx.js" ></script>
<script type="text/javascript">
    function onRemove(id, name)
    {
        if (confirm("确定要删除\"" + name + "\"吗？"))
        {
            sell.foperator.value = <?php echo OPERATOR_REMOVE; ?>;
            sell.fid.value = id;
            sell.submit();
        }
    }
    function onDetail(id)
    {
        document.location.href = "./query_sell.php?fsell_id=" + id;
    }
	
	function onQueryOrder()
	{
		var order = document.getElementById("order").value.trim();
		if (order.length == 0)
		{
			document.getElementById("result").innerHTML = "";
			return;
		}
		
		var send_string = "order=" + order;
		send_string = encodeURI(send_string);
		
		var xh = new XMLHttpRequest();
		
		xh.onreadystatechange = function()
		{
			if (xh.readyState == 4 && xh.status == 200)
			{
				if (xh.responseText == "1")
				{
					document.getElementById("result").innerHTML = "已登记";
				}
				else
				{
					document.getElementById("result").innerHTML = "未登记";
				}
			}
		}
		  
		xh.open("POST", "./query_order.php", true);
		xh.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xh.send(send_string);
	}
<?php
	definePageFunction();
?>
</script>
<title>销售</title>
</head>
<body>
<?php
    defineSortForm();
?>
    <form method="post" name="sell" action="./sell.php">
        <input type="hidden" name="foperator" value="<?php echo OPERATOR_REMOVE; ?>" />
        <input type="hidden" name="fid" value="0" />
    </form>
	
    <table>
        <tr>
            <td width="70">销售额：</td><td width="100"><?php echo round($sell_total, 2); ?></td>
            <td width="70">成本：</td><td width="100"><?php echo round($sell_out, 2); ?></td>
            <td width="70">毛利润：</td><td width="100"><?php echo round($sell_total + $sell_out, 2); ?></td>
			<td><a href="./addsell.php">增加</a></td>
			<td width="30"></td>
			<td witdh="50"><input type="text" id="order" onchange="onQueryOrder()"></td>
			<td witdh="30"><div id="result"></div></td>
        </tr>
    </table>
    <p>
	<form method="post" name="query_page" action="./sell.php">
        <input type="hidden" name="fcurpage" value="<?php echo $curpage; ?>" />
    </form>
	
<?php
	generalPage($total_count, $curpage);
?>

    <table frame="border" rules="all">
        <tr>
<?php
            $page = "sell";
            $default_dir = array(1, 2, 2, 3, 0, 0, 2, 0, 2, 2, 2, 0);
            $headers = array("*", "10", "销售人", "60", "客户", "220", "时间", "100",
                "定单号", "150", "支运", "50", "发货人", "60", "发运", "60",
                "销售额", "80", "成本", "80", "毛利润", "80", "说明", "400");
             generalHeader($page, $default_dir, $headers);
?>
        </tr>
<?php
        $sql = "select sell_id, user_name, client_id, client_taobao, client_name, "
            . "date_format(sell_time, '%Y-%m-%d') as sell_time, "
            . "sell_card, sell_freight_in, "
            . "sell_freight_out, sell_remark, sell_total_in, sell_total_out, sell_profit, "
            . "(select user_name from user where user_id = sell_freight_user_id) as freight_user_name "
            . "from client inner join (user inner join sell on user.user_id = sell.sell_sell_user_id) "
            . "on client.client_id = sell.sell_client_id ";
        $sort_key = array("", "user_name", "client_taobao", "sell_time",
            "", "", "freight_user_name", "",
            "sell_total_in", "sell_total_out", "sell_profit", "");
        $sql .= appendSortSql($page, $default_dir, $sort_key);
		$starPos = $curpage * 30;
		$lastPos = $starPos + 30;
		$sql .= " limit $starPos, $lastPos";
        $rs = mysql_query($sql);
        
        if ($rs)
        {
            while ($row = mysql_fetch_array($rs))
            {
                $sell_total += $row["sell_total_in"];
                $sell_out += $row["sell_total_out"];
                echo "        <tr>\n            <td>\n                ";
                echo "<a href='javascript:void(0)' onclick='onRemove(" 
                    . $row["sell_id"]. ", \"" . $row["sell_card"] . "\")'>X</a>";
                echo "\n            </td>\n            <td>\n                ";
                echo $row["user_name"];
                echo "\n            </td>\n            <td>\n                ";
                echo $row["client_name"] . "(" . $row["client_taobao"] . ")";
                echo "\n            </td>\n            <td>\n                ";
                echo $row["sell_time"];
                echo "\n            </td>\n            <td>\n                ";
                echo "<a href='javascript:void(0)' onclick='onDetail(" 
                    . $row["sell_id"] . ")'>" . $row["sell_card"] . "</a>";
                echo "\n            </td>\n            <td>\n                ";
                echo $row["sell_freight_in"];
                echo "\n            </td>\n            <td>\n                ";
                echo $row["freight_user_name"];
                echo "\n            </td>\n            <td>\n                ";
                echo $row["sell_freight_out"];
                echo "\n            </td>\n            <td>\n                ";
                echo round($row["sell_total_in"], 2);
                echo "\n            </td>\n            <td>\n                ";
                echo round($row["sell_total_out"], 2);
                echo "\n            </td>\n            <td>\n                ";
                echo round($row["sell_profit"], 2);
                echo "\n            </td>\n            <td>\n                ";
                echo $row["sell_remark"];
                echo "\n            </td>\n        </tr>\n";
            }
        }
?>
    </table>
</body>
</html>