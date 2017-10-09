<?php require_once("sort_help.php") ?>
<?php
    $fbrand = isset($_GET["fbrand"]) ? $_GET["fbrand"] : 0;
    $fcat2 = isset($_GET["fcat2"]) ? $_GET["fcat2"] : 0;
    $ftype = isset($_GET["ftype"]) ? $_GET["ftype"] : 0;
    $fmoney = isset($_GET["fmoney"]) ? $_GET["fmoney"] : 0;
    $fremark = isset($_GET["fremark"]) ? $_GET["fremark"] : "";
    $act = isset($_GET["act"]) ? $_GET["act"] : 0;
    $fid = isset($_GET["fid"]) ? $_GET["fid"] : 0;
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-cn" />
<?php
    if ($act != 0)
    {
        if ($act == 1)
        {
            if ($fid != 0)
            {
                $sql = "delete from goods where goods_id=" . $fid;
                if (!mysql_query($sql))
                {
                    die($sql . "<br />" . $conn->connect_error . "<br />删除失败");
                }
                
                addHistory("删除", "商品", $fid);
            }
        }
        else if ($act == 2)
        {
            $sql = "insert into goods(goods_brand_id, goods_cat2_id, goods_type, goods_price, goods_remark)"
                . " values('" . $fbrand . "','" . $fcat2 . "','" . $ftype 
                . "','" . $fmoney . "','" . $fremark ."')";
            if (!mysql_query($sql))
            {
                die($sql . "<br />" . $conn->connect_error . "<br />添加失败");
            }
            
            addHistory("添加", "商品", $fbrand);
        }
        else if ($act == 3)
        {
            $sql = "update goods set goods_brand_id='" . $fbrand 
                . "', goods_cat2_id='" . $fcat2
                . "', goods_type='" . $ftype
                . "', goods_price='" . $fmoney
                . "', goods_remark='" . $fremark
                . "' where goods_id=" . $fid;
            if (!mysql_query($sql))
            {
                die($sql . "<br />" . $conn->connect_error . "<br />修改失败");
            }
            
            addHistory("修改", "商品", "[" . $fid . "]" . $fbrand);
        }
        else
        {
            die("未知操作");
        }
    }
?>
<script type="text/javascript" src="./dxx.js" ></script>
<script type="text/javascript">
    function delete_Goods(fid, fname)
    {
        if (confirm("确定要删除'" + fname + "'吗？"))
        {
            document.location.href = "./goods.php?act=1&fid=" + fid;
        }
    }
    function modify_Goods(fid, fbrand, fcat2, ftype, fmoney, fremark)
    {
        goods.act.value = 3;
        goods.fid.value = fid;
        updateItemToSelect(goods.fbrand, fbrand);
        updateItemToSelect(goods.fcat2, fcat2);
        goods.ftype.value = ftype;
        goods.fmoney.value = fmoney;
        goods.fremark.value = fremark;
        goods.sub.value = "修改";
        scroll(0,0);
    }
    function clear_form()
    {
        goods.act.value = 2;
        goods.fid.value = 0;
        goods.fbrand[0].selected = true;
        goods.fcat2[0].selected = true;
        goods.ftype.value = "";
        goods.fmoney.value = 0;
        goods.fremark.value = "";
        goods.sub.value = "增加";
    }
    function sub_form()
    {
        goods.fmoney.value = Math.abs(goods.fmoney.value.trim());
        goods.submit();
    }
</script>
<title>商品</title>
</head>
<body>
<?php
    defineSortForm();
?>
    <form method="get" name="goods" action="./goods.php">
        <div>品牌：<select name="fbrand">
<?php
            $sql = "select * from brand order by brand_id desc";
            $rs = mysql_query($sql);
            while ($row = mysql_fetch_array($rs))
            {
                echo "<option value=\"" . $row["brand_id"] . "\"";
                if ($fbrand == $row["brand_id"])
                {
                    echo " selected ";
                }
                echo  ">" . $row["brand_name"];
            }
?>
        </select></div>
        <div>类别：<select name="fcat2">
<?php
            $sql = "select cat1_name, cat2_id, cat2_name from cat1 inner join "
                ."cat2 on cat1.cat1_id = cat2.cat2_cat1_id";
            $rs = mysql_query($sql);
            while ($row = mysql_fetch_array($rs))
            {
                echo "<option value=\"" . $row["cat2_id"] . "\"";
                if ($fcat2 == $row["cat2_id"])
                {
                    echo " selected ";
                }
                echo ">[" . $row["cat1_name"] . "]" . $row["cat2_name"];
            }
?>
        </select></div>
        <div>型号：<input type="text" name="ftype" /></div>
        <div>进价：<input type="text" name="fmoney" /></div>
        <div>说明：<textarea name="fremark" rows="4" cols="80"></textarea></div>
        <input type="hidden" name="act" value="2" />
        <input type="hidden" name="fid" value="0" />
        <input type="submit" name="sub" value="增加" onclick="sub_form()" />
        <input type="reset" onclick="clear_form()" />
    </form>
    <p />
    <table frame="border" rules="all">
        <tr>
<?php
            $page = "goods";
            $default_dir = array(1, 2, 2, 2, 2, 2, 2, 0);
            $headers = array("*", "10", "编号", "40", "品牌", "100", "大类", "50",
                "小类", "120", "型号", "120", "进价", "50", "说明", "300");
             generalHeader($page, $default_dir, $headers);
?>
        </tr>
<?php
	$sql = "select goods_id, brand_name, goods_brand_id, cat1_name, cat2_name, "
		. "goods_cat2_id, goods_type, goods_price, goods_remark "
		. "from (cat1 inner join cat2 on cat1.cat1_id = cat2.cat2_cat1_id) "
		. "inner join (brand inner join goods on brand.brand_id = goods.goods_brand_id) "
		. "on cat2.cat2_id = goods.goods_cat2_id ";
	$sort_key = array("", "goods_id", "brand_name", "cat1_name", 
		"cat2_name", "goods_type", "goods_price", "");
	$sql = $sql . appendSortSql($page, $default_dir, $sort_key);
	$rs = mysql_query($sql);
	if ($rs)
	{
		while ($row = mysql_fetch_array($rs))
		{
			  echo "<tr><td><a href='javascript:void(0)' onclick='delete_Goods(" 
				  . $row["goods_id"]. ", \"" . $row["brand_name"] . "\")'>X</a>";
			  echo "</td><td>";
			  echo "<a href='javascript:void(0)' onclick='modify_Goods("
				  . $row["goods_id"] . ", " . $row["goods_brand_id"] . ", " . $row["goods_cat2_id"] 
				  . ", \"" . $row["goods_type"] . "\", " . $row["goods_price"] . ", \"" 
				  . str_replace("\r\n", "\\n", $row["goods_remark"]) . "\")'>"
				  . $row["goods_id"] . "</a>";
			  echo "</td><td>";
			  echo $row["brand_name"];
			  echo "</td><td>";
			  echo $row["cat1_name"];
			  echo "</td><td>";
			  echo $row["cat2_name"];
			  echo "</td><td>";
			  echo $row["goods_type"];
			  echo "</td><td>";
			  echo $row["goods_price"];
			  echo "</td><td>";
			  echo $row["goods_remark"];
			  echo "</td></tr>";
		}
	}
?>
    </table>
</body>
</html>