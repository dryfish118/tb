<?php require_once("conn.php") ?>
<?php
	$fuser = isset($_GET["fuser"]) ? $_GET["fuser"] : "";
	$ftime1 = isset($_GET["ftime1"]) ? $_GET["ftime1"] : "";
	$ftime2 = isset($_GET["ftime2"]) ? $_GET["ftime2"] : "";
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
<title>收支查询</title>
</head>
<body>
	<form name="query_cond" action="./query_income.php" method="get">
	<table id="table_form">
		<tr>
			<td>
					起始时间：<input type="text" id="ftime1" name="ftime1" value='<?php echo $ftime1; ?>' onclick="return showCalendar('ftime1', 'y-mm-dd');" />
			</td>
			<td>
					结束时间：<input type="text" id="ftime2" name="ftime2" value='<?php echo $ftime2; ?>' onclick="return showCalendar('ftime2', 'y-mm-dd');" />
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
								if ($fuser == $row["user_id"])
								{
									echo " selected ";
								}
								echo ">" . $row["user_name"];
							}
						}
					?>
					</select>
					<input type="submit" value="查询" />
					<input type="button" value="清空" onclick="clear_form()" />
			</td>
			<td>0</td>
		</tr>
	</table>
	</form>
	<table>
		<tr valign="top">
			<td>
				<table frame="border" rules="all" id="table_flow">
					<tr>
						<th width="100">时间</th>
						<th width="100">人员</th>
						<th width="100">金额</th>
					</tr>
				<?php
					$sql = "select user_name, sum(flow_money) as total, date_format(flow_time, '%Y-%m-%d') as flow_time "
				        . "from user inner join flow on user.user_id = flow.flow_user_id ";
		
					$sqlCond = "";
				    if ($ftime1 != "")
				    {
				    	if ($ftime2 != "")
				        {
				        	$sqlCond = "where (flow_time >= '" . $ftime1 . "' and flow_time <= '" . $ftime2 . "') ";
				    	}
				        else
				        {
				            $sqlCond = "where (flow_time='" . $ftime1 . "') ";
				    	}
				    }

				    if ($fuser != "")
				    {
				    	if ($sqlCond != "")
				    	{
				    		$sqlCond .= " and ";
				    	}
				    	else
				    	{
				    		$sqlCond .= "where ";
				    	}
				        $sqlCond .= "user_id = " . $fuser . " ";
				    }
				    
				    if ($sqlCond != "")
				    {
				        $sql .= $sqlCond;
				    }
		
				    $sql .= "group by flow_time, user_name order by flow_time desc";
					$rs = mysql_query($sql);
					
					echo "<tr><td>";
					echo "合计";
			  		echo "</td><td>";
			  		echo "流水";
			  		echo "</td><td>";
			  		echo 0;
			  		echo "</td></tr>";
			  		
					$flow_total = 0;
					if ($rs)
					{
						while ($row = mysql_fetch_array($rs))
						{
							$flow_total += $row["total"];
							echo "<tr><td>";
							echo $row["flow_time"];
							echo "</td><td>";
							echo $row["user_name"];
							echo "</td><td>";
							echo round($row["total"], 2);
							echo "</td></tr>";
						}
					}
				?>
				</table>
			</td>
			<td>
				<table frame="border" rules="all" id = "table_stock">
					<tr>
						<th width="100">时间</th>
						<th width="100">人员</th>
						<th width="100">金额</th>
					</tr>
				<?php
					$sql = "select date_format(stock_time, '%Y-%m-%d') as stock_time, "
						. "user_name, -sum(stock_amount*goods_price) as total "
						. "from goods inner join (goods2 inner join "
						. "(user inner join stock on user.user_id = stock.stock_user_id) "
						. "on goods2.goods2_id=stock.stock_goods2_id) "
						. "on goods.goods_id=goods2.goods2_goods_id ";
				
					$sqlCond = "";
				    if ($ftime1 != "")
				    {
				    	if ($ftime2 != "")
				        {
				        	$sqlCond = "where (stock_time >= '" . $ftime1 . "' and stock_time <= '" . $ftime2 . "') ";
				    	}
				        else
				        {
				            $sqlCond = "where (stock_time='" . $ftime1 . "') ";
				    	}
				    }

				    if ($fuser != "")
				    {
				    	if ($sqlCond != "")
				    	{
				    		$sqlCond .= " and ";
				    	}
				    	else
				    	{
				    		$sqlCond .= "where ";
				    	}
				        $sqlCond .= "user_id = " . $fuser . " ";
				    }
				    
				    if ($sqlCond != "")
				    {
				        $sql .= $sqlCond;
				    }
				
					$sql .= "group by stock_time, user_name order by stock_time desc";
					$rs = mysql_query($sql);
					
					echo "<tr><td>";
					echo "合计";
			  		echo "</td><td>";
			  		echo "进货";
			  		echo "</td><td>";
			  		echo 0;
			  		echo "</td></tr>";
			  		
					$stock_total = 0;
					if ($rs)
					{
						while ($row = mysql_fetch_array($rs))
						{
							if ($row["total"] != 0)
							{
								$stock_total += $row["total"];
								echo "<tr><td>";
								echo $row["stock_time"];
								echo "</td><td>";
								echo $row["user_name"];
								echo "</td><td>";
								echo round($row["total"], 2);
								echo "</td></tr>";
							}
						}
					}
				?>
				</table>
			</td>
			<td>
				<table frame="border" rules="all" id="table_sell">
					<tr>
						<th width="100">时间</th>
						<th width="100">人员</th>
						<th width="100">金额</th>
					</tr>
				<?php
					$sql = "select date_format(sell_time, '%Y-%m-%d') as sell_time, sell_sell_user_id, sell_freight_user_id, "
						. "(select user_name from user where user_id=sell_sell_user_id) as user_name, "
						. "sum(sell_total_in+sell_freight_in) as total, "
						. "(select user_name from user where user_id=sell_freight_user_id) as user_name2, "
						. "sum(sell_freight_out) as freight_out from sell ";

					$sqlCond = "";
				    if ($ftime1 != "")
				    {
				    	if ($ftime2 != "")
				        {
				        	$sqlCond = "where (sell_time >= '" . $ftime1 . "' and sell_time <= '" . $ftime2 . "') ";
				    	}
				        else
				        {
				            $sqlCond = "where (sell_time='" . $ftime1 . "') ";
				    	}
				    }

				    if ($fuser != "")
				    {
				    	if ($sqlCond != "")
				    	{
				    		$sqlCond .= " and ";
				    	}
				    	else
				    	{
				    		$sqlCond .= "where ";
				    	}
				        $sqlCond .= "(sell_sell_user_id = " . $fuser 
				        	. " or sell_freight_user_id = "  . $fuser . ") ";
				    }
				    
				    if ($sqlCond != "")
				    {
				        $sql .= $sqlCond;
				    }
				
					$sql .= "group by sell_time, sell_sell_user_id, sell_freight_user_id order by sell_time desc";
					
					$rs = mysql_query($sql);
					echo "<tr><td>";
					echo "合计";
			  		echo "</td><td>";
			  		echo "销售";
			  		echo "</td><td>0</td></tr>";
			  		
					class SellInfo
			  		{
			  			public $id = 0;
			  			public $name;
			  			public $money = 0;
			  		}
			  		class SellDay
			  		{
			  			public $dt;
			  			public $si = array();
			  		}
			  		
			  		//sell_time
			  		//sell_sell_user_id
			  		//sell_freight_user_id
					//user_name
					//total
					//user_name2
					//freight_out
			  		
			  		$sd = array();
			  		$sd_count = -1;
					if ($rs)
					{
						while ($row = mysql_fetch_array($rs))
						{
							if ($sd_count == -1 || $sd[$sd_count]->dt != $row["sell_time"])
							{
								if ($fuser == "")
								{
									array_push($sd, new SellDay());
									$sd_count++;
									$sd[$sd_count]->dt = $row["sell_time"];
									
									array_push($sd[$sd_count]->si, new SellInfo());
									$sd[$sd_count]->si[count($sd[$sd_count]->si) - 1]->id = $row["sell_sell_user_id"];
									$sd[$sd_count]->si[count($sd[$sd_count]->si) - 1]->name = $row["user_name"];
									$sd[$sd_count]->si[count($sd[$sd_count]->si) - 1]->money = $row["total"];
									
									if ($row["sell_sell_user_id"] == $row["sell_freight_user_id"])
									{
										$sd[$sd_count]->si[count($sd[$sd_count]->si) - 1]->money += $row["freight_out"];
									}
									else
									{
										array_push($sd[$sd_count]->si, new SellInfo());
										$sd[$sd_count]->si[count($sd[$sd_count]->si) - 1]->id = $row["sell_freight_user_id"];
										$sd[$sd_count]->si[count($sd[$sd_count]->si) - 1]->name = $row["user_name2"];
										$sd[$sd_count]->si[count($sd[$sd_count]->si) - 1]->money = $row["freight_out"];
									}
								}
								else
								{
									if ($row["sell_sell_user_id"] == $fuser || 
										$row["sell_freight_user_id"] == $fuser)
									{
										array_push($sd, new SellDay());
										$sd_count++;
										$sd[$sd_count]->dt = $row["sell_time"];
										
										if ($row["sell_sell_user_id"] == $fuser)
										{
											array_push($sd[$sd_count]->si, new SellInfo());
											$sd[$sd_count]->si[count($sd[$sd_count]->si) - 1]->id = $row["sell_sell_user_id"];
											$sd[$sd_count]->si[count($sd[$sd_count]->si) - 1]->name = $row["user_name"];
											$sd[$sd_count]->si[count($sd[$sd_count]->si) - 1]->money = $row["total"];
											
											if ($row["sell_freight_user_id"] == $fuser)
											{
												$sd[$sd_count]->si[count($sd[$sd_count]->si) - 1]->money += $row["freight_out"];
											}
										}
										else if ($row["sell_freight_user_id"] == $fuser)
										{
											array_push($sd[$sd_count]->si, new SellInfo());
											$sd[$sd_count]->si[count($sd[$sd_count]->si) - 1]->id = $row["sell_freight_user_id"];
											$sd[$sd_count]->si[count($sd[$sd_count]->si) - 1]->name = $row["user_name2"];
											$sd[$sd_count]->si[count($sd[$sd_count]->si) - 1]->money = $row["freight_out"];
										}
									}
								}
							}
							else
							{
								if ($sd[$sd_count]->dt == $row["sell_time"])
								{
									if ($fuser == "" || $row["sell_sell_user_id"] == $fuser)
									{
										$i = 0;
										for (; $i < count($sd[$sd_count]->si); $i++)
										{
											if ($row["sell_sell_user_id"] == $sd[$sd_count]->si[$i]->id)
											{
												break;
											}
										}
										if ($i == count($sd[$sd_count]->si))
										{
											array_push($sd[$sd_count]->si, new SellInfo());
											$sd[$sd_count]->si[count($sd[$sd_count]->si) - 1]->id = $row["sell_sell_user_id"];
											$sd[$sd_count]->si[count($sd[$sd_count]->si) - 1]->name = $row["user_name"];
											$sd[$sd_count]->si[count($sd[$sd_count]->si) - 1]->money = $row["total"];
										}
										else
										{
											$sd[$sd_count]->si[$i]->money += $row["total"];
										}
									}
									
									if ($fuser == "" || $row["sell_freight_user_id"] == $fuser)
									{
										$i = 0;
										for (; $i < count($sd[$sd_count]->si); $i++)
										{
											if ($row["sell_freight_user_id"] == $sd[$sd_count]->si[$i]->id)
											{
												break;
											}
										}
										if ($i == count($sd[$sd_count]->si))
										{
											array_push($sd[$sd_count]->si, new SellInfo());
											$sd[$sd_count]->si[count($sd[$sd_count]->si) - 1]->id = $row["sell_freight_user_id"];
											$sd[$sd_count]->si[count($sd[$sd_count]->si) - 1]->name = $row["user_name2"];
											$sd[$sd_count]->si[count($sd[$sd_count]->si) - 1]->money = $row["freight_out"];
										}
										else
										{
											$sd[$sd_count]->si[$i]->money += $row["freight_out"];
										}
									}
								}
							}
						}
						
						function AddToList($dt, $si)
						{
							if ($si->money != 0)
							{
								echo "<tr><td>";
								echo date("Y-m-d", strtotime($dt));
								echo "</td><td>";
								echo $si->name;
								echo "</td><td>";
								echo round($si->money, 2);
								echo "</td></tr>";
							}
						}
						
						$sell_total = 0;
						for ($i = 0; $i <= $sd_count; $i++)
						{
							for ($j = 0; $j < count($sd[$i]->si); $j++)
							{
								addToList($sd[$i]->dt, $sd[$i]->si[$j]);
								$sell_total += $sd[$i]->si[$j]->money;
							}
						}
					}
				?>
				</table>
			</td>
		</tr>
	</table>
</body>
<script type="text/javascript">
	var obj_table_flow = document.getElementById("table_flow");
	obj_table_flow.rows[1].cells[2].innerText = <?php echo round($flow_total, 2); ?>;
	var obj_table_stock = document.getElementById("table_stock");
	obj_table_stock.rows[1].cells[2].innerText = <?php echo round($stock_total, 2); ?>;
	var obj_table_sell = document.getElementById("table_sell");
	obj_table_sell.rows[1].cells[2].innerText = <?php echo round($sell_total, 2); ?>;
	var obj_table_form = document.getElementById("table_form");
	obj_table_form.rows[0].cells[3].innerText = "总额：" + <?php echo round($flow_total + $stock_total + $sell_total, 2); ?>;
</script>
</html>