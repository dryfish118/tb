<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript">
	function onParseOrder()
	{
		var txtCode = document.getElementById("txtCode").value;
		
		var reg = /订单编号：(\d+) 成交时间：/g;
		var orders = new Array();
		var rt;
		while (rt = reg.exec(txtCode))
		{
			orders.push(rt[1]);
		}
		
		var send_string = "orders=" + orders.join("-");
		send_string = encodeURI(send_string);
		
		//document.getElementById("result").innerHTML = orders.join("<br/>");
		
		var xh = new XMLHttpRequest();
		
		xh.onreadystatechange = function()
		{
			if (xh.readyState == 4 && xh.status == 200)
			{
				document.getElementById("result").innerHTML = xh.responseText;
				//alert(xh.responseText);
			}
		}
		  
		xh.open("POST", "./query_order.php", true);
		xh.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xh.send(send_string);
	}
</script>
</head>
<body>
<textarea id="txtCode" rows="20" cols="180">
</textarea><br/>
<input type="button" value="解析" onClick="onParseOrder()">
<p>
<div id="result"></div>
</body>
</html>