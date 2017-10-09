<?php require_once("conn.php") ?>
<?php
	$rowSell = NULL;
	$rsGoods2 = NULL;
	
	function initGoods2()
	{
		$sql = "select goods_id, brand_name, cat1_name, cat2_name, goods2_left, "
		    . "goods_type, color_name, size_name, goods2_id "
		    . "from size inner join (color inner join "
		    . "(((cat1 inner join cat2 on cat1.cat1_id = cat2.cat2_cat1_id) "
		    . "inner join (brand inner join goods on brand.brand_id = goods.goods_brand_id) "
		    . "on cat2.cat2_id = goods.goods_cat2_id) "
		    . "inner join goods2 on goods.goods_id = goods2.goods2_goods_id) "
		    . "on color.color_id = goods2.goods2_color_id) on size.size_id = goods2.goods2_size_id ";
		$sql .= "order by goods2_updatetime desc, brand_name, cat1_name, cat2_name, color_name, size_name"; 
		$rs = mysql_query($sql);
		if (!$rs)
		{
			die($sql . "<br />" . $conn->connect_error . "<br />initGoods2失败");
		}
		return $rs;
	}
	function getItemName($row)
	{
		$name = $row["goods_id"] . " " . $row["brand_name"] . "-" . $row["cat1_name"] . "-" . $row["cat2_name"];
		if ($row["goods_type"] != "")
		{
			$name .= "-" . $row["goods_type"];
		}
		$name .= "-" . $row["color_name"] . "-" . $row["size_name"];
		return $name;
	}
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
	function onAddClient()
	{
		document.location.href = "./client.php";
	}
	function onAdd()
	{
		sell.famount.value = Math.abs(sell.famount.value.trim());
		if (sell.famount.value == 0)
		{
			alert("输入数量");
			sell.famount.value = 0;
			return;
		}
		var t = document.getElementById("table_items");
		var r = t.insertRow(t.rows.length);
		var c = r.insertCell(0);
		c.innerHTML = "<a href='javascript:void(0)' onclick='onRemove(" + t.rows.length + ")'>删</a>";
		c = r.insertCell(1);
		c.innerHTML = getTextFromSelected(sell.fgoods);
		c = r.insertCell(2);
		c.innerHTML = sell.fprice.value;
		c = r.insertCell(3);
		c.innerHTML = sell.famount.value;
		r.setAttribute("value", sell.fgoods.value);
	}
	function onRemove(ln)
	{
		var t = document.getElementById("table_items");
		for (var i = ln - 1; i < t.rows.length - 1; i++)
		{
			t.rows[i].setAttribute("value", t.rows[i + 1].getAttribute("value"));
			t.rows[i].cells[1].innerHTML = t.rows[i + 1].cells[1].innerHTML;
			t.rows[i].cells[2].innerHTML = t.rows[i + 1].cells[2].innerHTML;
			t.rows[i].cells[3].innerHTML = t.rows[i + 1].cells[3].innerHTML;
		}
		t.deleteRow(t.rows.length - 1);
	}
	function onClear()
	{
		var t = document.getElementById("table_items");
		var length = t.rows.length;
		for (var i = 1; i < length; i++)
		{
			t.deleteRow(1);
		}
	}
	function onCheck()
	{
		var t = document.getElementById("table_items");
	    if (t.rows.length == 1)
	    {
	    	alert("没有销售记录");
	        return false;
		}
	    if (sell.fcard.length < 14)
	    {
	    	alert("单号错误");
	        return false;
		}
		
		if (sell.ftime.value == "")
		{
	    	alert("时间不能为空");
	        return false;
		}
		
		sell.fcard.value = sell.fcard.value.trim();
		if (sell.fcard.value == "")
		{
			return false;
		}
		
		sell.ffreightin.value = Math.abs(sell.ffreightin.value.trim());
		if (sell.ffreightin.value == "")
	    {
	    	alert("支付运费不能为空");
	        return false;
		}
	    sell.ffreightout.value = Math.abs(sell.ffreightout.value.trim());
		if (sell.ffreightout.value == "")
	    {
	    	alert("发货运费不能为空");
	        return false;
		}
		
		if (Math.abs(sell.ffreightout.value) > 0)
		{
			sell.ffreightout.value = "-" + sell.ffreightout.value;
		}
		
		sell.fitems.value = "";
		for (var i = 1; i < t.rows.length; i++)
		{
			sell.fitems.value = sell.fitems.value + t.rows[i].getAttribute("value") + " ";
			sell.fitems.value = sell.fitems.value + t.rows[i].cells[2].innerHTML + " ";
			sell.fitems.value = sell.fitems.value + t.rows[i].cells[3].innerHTML + " ";
		}
		
		return true;
	}
	
	function onQueryClient()
	{
		var taobao = document.getElementById("query_client_name").value.trim();
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
					sell.fclient.value = xh.responseText;
				}
			}
		}
		  
		xh.open("POST", "./query_order.php", true);
		xh.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xh.send(send_string);
	}
	
	function onSelectGoods2(id)
	{
		sell.fgoods.value = id;
	}
	
	function onQueryGoods()
	{
		var goods = document.getElementById("query_goods_id").value.trim();
		if (goods.length == 0)
		{
			return;
		}
		
		var send_string = "goods_id=" + goods;
		var xh = new XMLHttpRequest();
		
		xh.onreadystatechange = function()
		{
			if (xh.readyState == 4 && xh.status == 200)
			{
				var xmlDoc;
				try
				{
					xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
					xmlDoc.async = "false";
					xmlDoc.loadXML(xh.responseText);
				}
				catch (e)
				{
					var parser = new DOMParser();
					xmlDoc = parser.parseFromString(xh.responseText, "text/xml");
				}
				
				var txt;
				try
				{
					txt = "<table frame='border' rules='all'><tr><th>编号</th><th>品牌</th><th>颜色</th><th>尺寸</th><th>数量</th></tr>";
					var goods2 = xmlDoc.getElementsByTagName("goods2");
					for (i = 0; i < goods2.length; i++)
					{
						var id = goods2[i].getElementsByTagName("id")[0].childNodes[0].nodeValue;
						var brand = goods2[i].getElementsByTagName("brand")[0].childNodes[0].nodeValue;
						var color = goods2[i].getElementsByTagName("color")[0].childNodes[0].nodeValue;
						var size = goods2[i].getElementsByTagName("size")[0].childNodes[0].nodeValue;
						var left = goods2[i].getElementsByTagName("left")[0].childNodes[0].nodeValue;
						txt = txt + "<tr>";
						txt = txt + "<td><a href='javascript:void(0)' onclick='onSelectGoods2(" + id + ")'>选</a></td>";
						txt = txt + "<td>" + brand + "</td>";
						txt = txt + "<td>" + color + "</td>";
						txt = txt + "<td>" + size + "</td>";
						txt = txt + "<td>" + left + "</td>";
						txt = txt + "</tr>";
					}
					txt = txt + "</table>";
				}
				catch(e)
				{
					alert(xh.responseText);
					alert(id);
					alert(brand);
					alert(color);
					alert(size);
					
					txt = "出错了";
				}
				document.getElementById("tbGoods").innerHTML = txt;
			}
		}
		  
		xh.open("POST", "./query_order.php", true);
		xh.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xh.send(send_string);
	}
	
	function onSaveTemplate()
	{
		document.cookie = "template_ftime=" + escape(sell.ftime.value);
		document.cookie = "template_fuser_sell=" + escape(sell.fuser_sell.value);
		document.cookie = "template_ffreightin=" + escape(sell.ffreightin.value);
		document.cookie = "template_ffreightout=" + escape(sell.ffreightout.value);
		document.cookie = "template_fuser_freight=" + escape(sell.fuser_freight.value);
		document.cookie = "template_fremark=" + escape(sell.fremark.value);

		var t = document.getElementById("table_items");
		document.cookie = "template_rows=" + escape(t.rows.length - 1);
		for (i = 1; i < t.rows.length; i++)
		{
			document.cookie = "template_goods2" + i + "=" + escape(t.rows[i].getAttribute("value"));
			document.cookie = "template_price" + i + "=" + escape(t.rows[i].cells[2].innerHTML);
			document.cookie = "template_amount" + i + "=" + escape(t.rows[i].cells[3].innerHTML);
		}
	}
	
	function onLoadTemplate()
	{
		sell.ftime.value = unescape(getCookie("template_ftime"));
		sell.fuser_sell.value = unescape(getCookie("template_fuser_sell"));
		sell.ffreightin.value = unescape(getCookie("template_ffreightin"));
		sell.ffreightout.value = unescape(getCookie("template_ffreightout"));
		sell.fuser_freight.value = unescape(getCookie("template_fuser_freight"));
		sell.fremark.value = unescape(getCookie("template_fremark"));
		
		var rows = unescape(getCookie("template_rows"));
		var t = document.getElementById("table_items");
		while (t.rows.length > 1)
		{
			t.deleteRow(1);
		}
		for (i = 1; i <= rows; i++)
		{
			var v1 = unescape(getCookie("template_goods2" + i));
			var v2 = unescape(getCookie("template_price" + i));
			var v3 = unescape(getCookie("template_amount" + i));
			
			var r = t.insertRow(t.rows.length);
			var c = r.insertCell(0);
			c.innerHTML = "<a href='javascript:void(0)' onclick='onRemove(" + t.rows.length	+ ")'>删</a>";
			c = r.insertCell(1);
			c.innerHTML = getTextByValue(sell.fgoods, v1);
			c = r.insertCell(2);
			c.innerHTML = v2;
			c = r.insertCell(3);
			c.innerHTML = v3;
			r.setAttribute("value", v1);
		}
	}
</script>
</head>
<body>
	<input type="button" value="保存到模板" onclick="onSaveTemplate()">
	<input type="button" value="从模板加载" onclick="onLoadTemplate()">
	<p>
	<form method="post" name="sell" action="./sell.php" onsubmit="return onCheck()">
		<input type="hidden" name="fitems" value ="" />
		<input type="hidden" name="foperator" value="<?php echo OPERATOR_ADD ?>" />
		<p><input type="submit" value="增加" /></p>
		单号：<input type="text" name = "fcard" value=<?php if ($rowSell) { echo $rowSell["sell_card"]; } else { echo "00000000000000"; } ?> />&nbsp;&nbsp;&nbsp;&nbsp;
		时间：<input type="text" id = "ftime" name = "ftime" <?php if ($rowSell) { echo "value=" . $rowSell["sell_time"]; } ?> onclick="return showCalendar('ftime', 'y-mm-dd');" />&nbsp;&nbsp;&nbsp;&nbsp;
		人员：<select name = "fuser_sell">
		<?php
			$sql = "select * from user";
			$rs = mysql_query($sql);
			while ($row = mysql_fetch_array($rs))
			{
				echo "<option value=" . $row["user_id"];
				if ($rowSell && $rowSell["sell_sell_user_id"] == $row["user_id"])
				{
					echo " selected ";
				}
				echo ">" . $row["user_name"];
			}
		?>
		</select><br />
		支运：<input type="text" name = "ffreightin" value=<?php if ($rowSell) { echo $rowSell["sell_freight_in"]; } else { echo "0"; } ?> />&nbsp;&nbsp;&nbsp;&nbsp;
		客户：<select name = "fclient">
		<?php
			$sql = "select * from client order by client_id desc";
			$rs = mysql_query($sql);
			while ($row = mysql_fetch_array($rs))
			{
				echo "<option value=\"" . $row["client_id"] . "\"";
				if ($rowSell && $rowSell["sell_client_id"] == $row["client_id"])
				{
					echo " selected ";
				}
				echo ">" . $row["client_name"] . "(" . $row["client_taobao"] . ")";
			}
		?>
		</select>
		<input type="button" value="查找客户" onclick="onQueryClient()" />
		<input type="text" id="query_client_name" /><br />
		发运：<input type="text" name = "ffreightout" value=<?php if ($rowSell) { echo $rowSell["sell_freight_out"]; } else { echo "0"; } ?> />&nbsp;&nbsp;&nbsp;&nbsp;
		发货：<select name = "fuser_freight">
		<?php
			$sql = "select * from user";
			$rs = mysql_query($sql);
			while ($row = mysql_fetch_array($rs))
			{
				echo "<option value=" . $row["user_id"];
				if ($rowSell && $rowSell["sell_freight_user_id"] == $row["user_id"])
				{
					echo " selected ";
				}
				echo ">" . $row["user_name"];
			}
		?>
		</select><br />
		<p>说明：<textarea name="fremark" rows="4" cols="80"><?php if ($rowSell) { echo $rowSell["sell_remark"]; } ?></textarea></p>

			<table border="0">
				<tr>
					<td width="50%">
						商品：<select name="fgoods">
						<?php
							if ($rsGoods2 == NULL)
							{
								$rsGoods2 = initGoods2();
							}
							mysql_field_seek($rsGoods2, 0);
							while ($row = mysql_fetch_array($rsGoods2))
							{
								echo "<option value=\"" . $row["goods2_id"] . "\">" . getItemName($row) . "</option>";
							}
						?>
						</select>
						<br />
						售价：<input type="text" name = "fprice" value = 0 />&nbsp;&nbsp;&nbsp;&nbsp;
						数量：<input type="text" name = "famount" value = 0 /><br />
						
						<input type="button" value="加入" onclick="onAdd()" />
						<input type="button" value="清空" onclick="onClear()" /></p>
						
						<table frame="border" rules="all" id="table_items">
							<tr>
								<th width="20">*</th>
								<th width="400">商品</th>
								<th width="40">价格</th>
								<th width="40">数量</th>
							</tr>
						</table>
					</td>
					<td width="50%">
						<input type="button" value="查找商品" onclick="onQueryGoods()" />
						<input type="text" id="query_goods_id" /><br/>
						<div id="tbGoods"></div>
					</td>
				</tr>
			</table>
	</form>
</body>
</html>