<?php require_once("conn.php") ?>
<?php
	$SORT_DISABLE = 0;
    $SORT_NONE = 1;
    $SORT_ASCENDING = 2;
    $SORT_DESCENDING = 3;
	
	function getSortData($page, $default_directions)
    {
	    $sql = "select fld_sort_id, fld_value from tbl_sort where fld_user_id = " 
			. $_COOKIE["user"] . " and fld_table_name = '$page'";
        $rs = mysql_query($sql);
		$sort_string = "";
        if ($rs && $row = mysql_fetch_array($rs))
        {
			$sort_string = $row["fld_value"];
		}
		
		if ($sort_string == "")
		{
		    for ($i = 0; $i < count($default_directions); $i++)
            {
                $sort_string = $sort_string . $i . "," . $default_directions[$i];
                if ($i != count($default_directions) - 1)
                {
                    $sort_string = $sort_string . ";";
                }
            }
            $sql = "insert into tbl_sort(fld_user_id, fld_table_name, fld_value) values('" 
				. $_COOKIE["user"] . "', '$page', '$sort_string')";
        	mysql_query($sql);
		}
        
		return explode(";", $sort_string);
    }

    function getDirection($sort_data, $column)
    {
        for ($i = 0; $i < count($sort_data); $i++)
        {
            $sort_head = explode(",", $sort_data[$i]);
            if ($sort_head[0] == $column)
            {
                return $sort_head[1];
            }
        }
        return 0;
    }

    function isFirstSort($column, $sort_data)
    {
        if (count($sort_data))
        {
            $sort_head = explode(",", $sort_data[0]);
            if ($sort_head[0] == $column)
            {
                return true;
            }
        }
        return false;
    }

	function defineSortForm()
	{
		echo "    <form method=\"post\" name=\"sort\" action=\"./sort.php\" >\n";
		echo "        <input type=\"hidden\" name=\"fpage\" value=\"\" />\n";
		echo "        <input type=\"hidden\" name=\"fcolumn\" value=\"0\" />\n";
		echo "        <input type=\"hidden\" name=\"fdirection\" value=\"0\" />\n";
		echo "    </form>\n";
	}
	
    function generalHeader($page, $default_directions, $headers)
    {
        $sort_data = getSortData($page, $default_directions);
        $count = count($headers) / 2;
        for ($i = 0; $i < $count; $i++)
        {
            echo "            <th width=\"" . $headers[$i * 2 + 1] . "\">\n                ";
            $direction = getDirection($sort_data, $i);
            if ($direction == 0)
            {
                echo $headers[$i * 2] . "\n            </th>\n";
            }
            else
            {
                if ($direction != 1 && isFirstSort($i, $sort_data))
                {
                	$direction = $direction == 2 ? 3 : 2;
                }
                echo "<a href='javascript:void(0)' onclick='onSort(\"$page\", $i, $direction)'>" 
                    . $headers[$i * 2] . "</a>\n            </th>\n";
            }
        }
    }
    
    function appendSortSql($page, $default_directions, $sort_key)
    {
        $sort_data = getSortData($page, $default_directions);
        if (count($sort_data) == 0)
        {
            return "";
        }
        $sort_head = explode(",", $sort_data[0]);
        if ($sort_head[1] == 1)
        {
        	if ($sort_key[0] != "")
        	{
        		return " order by " . $sort_key[0];
        	}
            else
            {
            	return "";
            }
        }
        $sql = "";
        for ($i = 0; $i < count($sort_data); $i++)
        {
            $sort_head = explode(",", $sort_data[$i]);
            if ($sort_head[1] != 0 && $sort_head[1] != 1)
            {
                if ($sql != "")
                {
                    $sql .= ", ";
                }
                $sql = $sql . $sort_key[$sort_head[0]];
                if ($sort_head[1] == 3)
                {
                    $sql .= " desc";
                }
            }
        }
        if ($sql != "")
        {
            $sql = " order by " . $sql;
        }
        return $sql;
    }
	
	function defineSort2Form()
	{
		echo "    <form method=\"post\" name=\"order\" action=\"./changeorder.php\" >\n";
		echo "        <input type=\"hidden\" name=\"fpage\" value=\"\" />\n";
		echo "        <input type=\"hidden\" name=\"fid\" value=\"0\" />\n";
		echo "        <input type=\"hidden\" name=\"forder\" value=\"0\" />\n";
		echo "        <input type=\"hidden\" name=\"fdelta\" value=\"0\" />\n";
		echo "    </form>\n";
	}
	
	function defineSortFunction($fpage)
	{
		echo "    function onChangeOrder(fid, forder, fdelta)\n";
		echo "    {\n";
		echo "    	order.fpage.value = \"$fpage\";\n";
		echo "    	order.fid.value = fid;\n";
		echo "    	order.forder.value = forder;\n";
		echo "    	order.fdelta.value = fdelta;\n";
		echo "    	order.submit();\n";
		echo "    }\n";
	}
	
	class COrderData
	{
		var $id;
		var $name;
		var $order;
	}

	class COrderDataMgr
	{
		var $m_items = array();
	}
	
	function generalOrderHtml($v, $items)
	{
		$page = "<select style=width:100px onchange=\"onChangeOrder("
			. $v->id . ", " . $v->order
			. ", this.options[this.selectedIndex].value)\">";
		for ($i = 1; $i <= count($items); $i++)
		{
			$page .= "<option value=\"" . ($v->order - $i) . "\"";
			if ($i == $v->order)
			{
				$page .= "selected";
			}
			$page .= ">" . $items[$i - 1]->name;
		}
		$page .= "</select>";
		echo $page;
	}
	
	function definePageFunction()
	{
	    echo "function onPage(num)";
		echo "{";
		echo "	query_page.fcurpage.value = num;";
		echo "	query_page.submit();";
		echo "}";
		echo "function onGotoPage()";
		echo "{";
		echo "	var obj = document.getElementById(\"selpage\");";
		echo "	onPage(obj.value);";
		echo "}";
	}
	
	function generalPage($total_count, $curpage)
	{
	    if ($total_count > 30)
		{
			echo "<table><tr>";
			
			if ($curpage > 0)
			{
				echo "<td><a href='javascript:void(0)' onclick='onPage(0)'>第一页</a></td>";
				$prePage = $curpage - 1;
				echo "<td><a href='javascript:void(0)' onclick='onPage($curpage - 1)'>上一页</a></td>";
			}
			else
			{
				echo "<td>第一页</td>";
				echo "<td>上一页</td>";
			}
			
			$pageCount = $total_count / 30;
			if ((int)$pageCount < $pageCount)
			{
				$pageCount = (int)$pageCount + 1;
			}
			
			echo "<td>跳转<select id=\"selpage\" onchange=\"onGotoPage()\">";
			for ($i = 0; $i < $pageCount;)
			{
				echo "<option value=\"$i\"";
				if ($curpage == $i)
				{
					echo " selected";
				}
				$i++;
				echo ">$i";
			}
			echo "</select></td>";

			if ($curpage < $pageCount - 1)
			{
				$nextPage = $curpage + 1;
				echo "<td><a href='javascript:void(0)' onclick='onPage($nextPage)'>下一页</a></td>";
				$lastPage = $pageCount - 1;
				echo "<td><a href='javascript:void(0)' onclick='onPage($lastPage)'>最后一页</a></td>";
			}
			else
			{
				echo "<td>下一页</td>";
				echo "<td>最后一页</td>";
			}
			
			echo "</tr></table>";
		}
	}
?>
