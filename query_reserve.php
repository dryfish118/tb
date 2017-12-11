<?php require_once("colordata.php") ?>
<?php require_once("sizedata.php") ?>
<?php
    $showall = (isset($_POST["fshowall"]) && ($_POST["fshowall"] == "on")) ? true : false;
    $goods = isset($_POST["fgoods"]) ? $_POST["fgoods"] : 0;
    $brand = isset($_POST["fbrand"]) ? $_POST["fbrand"] : 0;
    $cat1 = isset($_POST["fcat1"]) ? $_POST["fcat1"] : 0;
    $cat2 = isset($_POST["fcat2"]) ? $_POST["fcat2"] : 0;
    $type = isset($_POST["ftype"]) ? $_POST["ftype"] : 0;
    $color = isset($_POST["fcolor"]) ? $_POST["fcolor"] : 0;
    $size = isset($_POST["fsize"]) ? $_POST["fsize"] : 0;
    $curpage = isset($_POST["fcurpage"]) ? $_POST["fcurpage"] : 0;
    
    class CBrandData
    {
        var $id;
        var $name;
    }
    
    class CBrandSet
    {
        var $m_items = array();
        
        function __construct()
        {
            $i = 0;
            $sql = "select * from brand order by brand_id desc";
            $rs = mysql_query($sql);
            while ($row = mysql_fetch_array($rs))
            {
                $this->m_items[$i] = new CBrandData;
                $this->m_items[$i]->id = $row["brand_id"];
                $this->m_items[$i]->name = $row["brand_name"];
                $i++;
            }
        }
    }
    
    class CCat1Data
    {
        var $id;
        var $name;
    }
    
    class CCat1Set
    {
        var $m_items = array();
        
        function __construct()
        {
            $i = 0;
            $sql = "select * from cat1";
            $rs = mysql_query($sql);
            while ($row = mysql_fetch_array($rs))
            {
                $this->m_items[$i] = new CCat1Data;
                $this->m_items[$i]->id = $row["cat1_id"];
                $this->m_items[$i]->name = $row["cat1_name"];
                $i++;
            }
        }
    }
    
    class CCat2Data
    {
        var $id;
        var $name;
        var $cat1;
    }
    
    class CCat2Set
    {
        var $m_items = array();
        
        function __construct()
        {
            $i = 0;
            $sql = "select cat1_name, cat2_id, cat2_name from cat1 inner join "
            ."cat2 on cat1.cat1_id = cat2.cat2_cat1_id";
            $rs = mysql_query($sql);
            while ($row = mysql_fetch_array($rs))
            {
                $this->m_items[$i] = new CCat2Data;
                $this->m_items[$i]->id = $row["cat2_id"];
                $this->m_items[$i]->name = $row["cat2_name"];
                $this->m_items[$i]->cat1 = $row["cat1_name"];
                $i++;
            }
        }
    }
    
    class CTypeData
    {
        var $name;
    }
    
    class CTypeSet
    {
        var $m_items = array();
        
        function __construct()
        {
            $i = 0;
            $sql = "select distinct(goods_type) as type from goods order by type";
            $rs = mysql_query($sql);
            while ($row = mysql_fetch_array($rs))
            {
                $this->m_items[$i] = new CTypeData;
                $this->m_items[$i]->name = $row["type"];
                $i++;
            }
        }
    }
    
    function getCondition($showall, $goods, $brand, $cat1, $cat2, $type, $color, $size)
    {
        $condition = $showall ? "" : " and goods2_left > 0";
        if ($goods != 0)
        {
            return "where goods_id = $goods$condition";
        }
        else if ($brand != 0)
        {
            return "where brand_id = $brand$condition";
        }
        else if ($cat1 != 0)
        {
            return "where cat1_id = $cat1$condition";
        }
        else if ($cat2 != 0)
        {
            return "where cat2_id = $cat2$condition";
        }
        else if ($type != 0)
        {
            return "where goods_type like '$type'$condition";
        }
        else if ($color != 0)
        {
            return "where color_id = $color$condition";
        }
        else if ($size != 0)
        {
            return "where size_id = $size$condition";
        }
        else if (!$showall)
        {
            return "where goods2_left > 0";
        }
        return "";
    }
    
    
    $sql = "select count(distinct goods_id) as reserve_kind, "
        . "count(goods2_id) as total_count, "
        . "sum(goods2_left) as reserve_amount, "
        . "sum(goods2_left * goods_price) as reserve_total "
        . "from cat1 inner join"
        . "    (cat2 inner join"
        . "     (brand inner join "
        . "     (color inner join"
        . "     (size inner join"
        . "     (goods2 inner join"
        . "     goods on goods2.goods2_goods_id = goods.goods_id)"
        . "     on size.size_id = goods2.goods2_size_id)"
        . "     on color.color_id = goods2.goods2_color_id)"
        . "     on brand.brand_id = goods.goods_brand_id)"
        . "    on cat2.cat2_id = goods.goods_cat2_id)"
        . "on cat1.cat1_id = cat2.cat2_cat1_id ";
    $sql .= getCondition($showall, $goods, $brand, $cat1, $cat2, $type, $color, $size);
    $rs = mysql_query($sql);
    $reserve_kind = 0;
    $total_count = 0;
    $reserve_amount = 0;
    $reserve_total = 0;
    if ($rs && $row = mysql_fetch_array($rs))
    {
        $total_count = $row["total_count"];
        $reserve_kind = $row["reserve_kind"];
        $reserve_amount = $row["reserve_amount"];
        $reserve_total = $row["reserve_total"];
    }
    
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-cn" />
<script type="text/javascript" src="./dxx.js" ></script>
<script type="text/javascript">
    function onGoods(id)
    {
        query_id.fgoods.value = id;
        query_id.submit();
    }
    function onBrand(id)
    {
        query_brand.fbrand.value = id;
        query_brand.submit();
    }
    function onCat1(id)
    {
        query_cat1.fcat1.value = id;
        query_cat1.submit();
    }
    function onCat2(id)
    {
        query_cat2.fcat2.value = id;
        query_cat2.submit();
    }
    function onType(type)
    {
        query_type.ftype.value = type;
        query_type.submit();
    }
    function onColor(id)
    {
        query_color.fcolor.value = id;
        query_color.submit();
    }
    function onSize(id)
    {
        query_size.fsize.value = id;
        query_size.submit();
    }
<?php
	definePageFunction();
?>
</script>
<title>库存查询</title>
</head>
<body>
<?php
    defineSortForm();
?>
    <table>
        <tr>
            <td width="50">款式：</td><td width="100"><?php echo $reserve_kind; ?></td>
            <td width="50">品种：</td><td width="100"><?php echo $total_count; ?></td>
            <td width="50">库存：</td><td width="100"><?php echo $reserve_amount; ?></td>
            <td width="50">金额：</td><td width="100"><?php echo round($reserve_total, 2); ?></td>
        </tr>
    </table>
    
    <form method="post" name="query_page" action="./query_reserve.php">
        显示所有：<input type="checkbox" name="fshowall" <?php if ($showall) echo "checked";?> onclick="submit()"/>
        <input type="hidden" name="fcurpage" value="<?php echo $curpage; ?>" />
        <input type="hidden" name="fgoods" value="<?php echo $goods; ?>" />
        <input type="hidden" name="fbrand" value="<?php echo $brand; ?>" />
        <input type="hidden" name="fcat1" value="<?php echo $cat1; ?>" />
        <input type="hidden" name="fcat2" value="<?php echo $cat2; ?>" />
        <input type="hidden" name="ftype" value="<?php echo $type; ?>" />
        <input type="hidden" name="fcolor" value="<?php echo $color; ?>" />
        <input type="hidden" name="fsize" value="<?php echo $size; ?>" />
    </form>

    <form method="post" name="query_id" action="./query_reserve.php" onsubmit="return subGoodsId()">
        编号：<input type="text" name="fgoods" <?php if ($goods) echo "value=\"" . $goods . "\""; ?> />
        <input type="hidden" name="fshowall" <?php if ($showall) {echo "value=\"on\"";} else {echo "value=\"off\"";} ?> />
    </form>
    
    <form method="post" name="query_brand" action="./query_reserve.php">
        品牌：<select name="fbrand" onchange="submit()">
    <?php
        $bs = new CBrandSet;
        foreach ($bs->m_items as $v)
        {
            echo "<option value=\"" . $v->id . "\"";
            if ($brand == $v->id)
            {
                echo " selected ";
            }
            echo  ">" . $v->name;
        }
    ?>
        </select>
        <input type="hidden" name="fshowall" <?php if ($showall) {echo "value=\"on\"";} else {echo "value=\"off\"";} ?> />
    </form>
        
    <form method="post" name="query_cat1" action="./query_reserve.php">
        大类：<select name="fcat1" onchange="submit()">
    <?php
        $cs = new CCat1Set;
        foreach ($cs->m_items as $v)
        {
            echo "<option value=\"" . $v->id . "\"";
            if ($cat1 == $v->id)
            {
                echo " selected ";
            }
            echo ">" . $v->name;
        }
    ?>
        </select>
        <input type="hidden" name="fshowall" <?php if ($showall) {echo "value=\"on\"";} else {echo "value=\"off\"";} ?> />
    </form>

    <form method="post" name="query_cat2" action="./query_reserve.php">
        小类：<select name="fcat2" onchange="submit()">
    <?php
        $cs = new CCat2Set;
        foreach ($cs->m_items as $v)
        {
            echo "<option value=\"" . $v->id . "\"";
            if ($cat2 == $v->id)
            {
                echo " selected ";
            }
            echo ">[" . $v->cat1 . "]" . $v->name;
        }
    ?>
        </select>
        <input type="hidden" name="fshowall" <?php if ($showall) {echo "value=\"on\"";} else {echo "value=\"off\"";} ?> />
    </form>

    <form method="post" name="query_type" action="./query_reserve.php">
        型号：<select name="ftype" onchange="submit()">
    <?php
        $ts = new CTypeSet;
        foreach ($ts->m_items as $v)
        {
            echo "<option value=\"" . $v->name . "\"";
            if ($type == $v->name)
            {
                echo " selected ";
            }
            echo ">" . $v->name;
        }
    ?>
        </select>
        <input type="hidden" name="fshowall" <?php if ($showall) {echo "value=\"on\"";} else {echo "value=\"off\"";} ?> />
    </form>

    <form method="post" name="query_color" action="./query_reserve.php">
        颜色：<select name="fcolor" onchange="submit()">
    <?php
        $cs = new CColorSet;
        foreach ($cs->m_items as $v)
        {
            echo "<option value=\"" . $v->id . "\"";
            if ($color == $v->id)
            {
                echo " selected ";
            }
            echo ">" . $v->name;
        }
    ?>
        </select>
        <input type="hidden" name="fshowall" <?php if ($showall) {echo "value=\"on\"";} else {echo "value=\"off\"";} ?> />
    </form>

    <form method="post" name="query_size" action="./query_reserve.php">
        尺寸：<select name="fsize" onchange="submit()">
    <?php
        $ss = new CSizeSet;
        foreach ($ss->m_items as $v)
        {
            echo "<option value=\"" . $v->id . "\"";
            if ($size == $v->id)
            {
                echo " selected ";
            }
            echo ">" . $v->name;
        }
    ?>
        </select>
        <input type="hidden" name="fshowall" <?php if ($showall) {echo "value=\"on\"";} else {echo "value=\"off\"";} ?> />
    </form>
        
    <p />
<?php
	generalPage($total_count, $curpage);
?>

    <table frame="border" rules="all">
        <tr>
<?php
            $page = "query_reserve";
            $default_dir = array(2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 0);
            $headers = array("编号", "40", "品牌", "100", "大类", "60",
                "小类", "100", "型号", "100", "颜色", "60", "尺寸", "60",
                "进价", "45", "进货", "45", "销售", "45", "库存", "45", "链接", "350");
             generalHeader($page, $default_dir, $headers);
?>
        </tr>
<?php
    $sql = "select goods_id, brand_id, brand_name, cat1_id, cat1_name, cat2_id, cat2_name, "
        . "goods_type, color_id, color_name, color_order, size_id, size_name, size_order, "
        . "goods_price, goods2_url, goods2_in, goods2_out, goods2_left "
        . "from cat1 inner join"
        . "    (cat2 inner join"
        . "     (brand inner join "
        . "     (color inner join"
        . "     (size inner join"
        . "     (goods2 inner join"
        . "     goods on goods2.goods2_goods_id = goods.goods_id)"
        . "     on size.size_id = goods2.goods2_size_id)"
        . "     on color.color_id = goods2.goods2_color_id)"
        . "     on brand.brand_id = goods.goods_brand_id)"
        . "    on cat2.cat2_id = goods.goods_cat2_id)"
        . "on cat1.cat1_id = cat2.cat2_cat1_id ";
    
    $sql .= getCondition($showall, $goods, $brand, $cat1, $cat2, $type, $color, $size);
    $sort_key = array("goods_id", "brand_name", "cat1_name", "cat2_name", 
        "goods_type", "color_order", "size_order", "goods_price", "goods2_in", 
        "goods2_out", "goods2_left", "");
    $sql .= appendSortSql($page, $default_dir, $sort_key);
    $starPos = $curpage * 30;
    $lastPos = $starPos + 30;
    $sql .= " limit $starPos, $lastPos";
    $rs = mysql_query($sql);
    if ($rs)
    {
        while ($row = mysql_fetch_array($rs))
        {
            echo "<tr><td>";
            echo "<a href='javascript:void(0)' onclick='onGoods(" . $row["goods_id"] . ")'>" . $row["goods_id"] . "</a>";
            echo "</td><td>";
            echo "<a href='javascript:void(0)' onclick='onBrand(" . $row["brand_id"] . ")'>" . $row["brand_name"] . "</a>";
            echo "</td><td>";
            echo "<a href='javascript:void(0)' onclick='onCat1(" . $row["cat1_id"] . ")'>" . $row["cat1_name"] . "</a>";
            echo "</td><td>";
            echo "<a href='javascript:void(0)' onclick='onCat2(" . $row["cat2_id"] . ")'>" . $row["cat2_name"] . "</a>";
            echo "</td><td>";
            if ($row["goods_type"] != "")
            {
                echo "<a href='javascript:void(0)' onclick='onType(\"" . $row["goods_type"] . "\")'>" . $row["goods_type"] . "</a>";
            }
            echo "</td><td>";
            echo "<a href='javascript:void(0)' onclick='onColor(" . $row["color_id"] . ")'>" . $row["color_name"] . "</a>";
            echo "</td><td>";
            echo "<a href='javascript:void(0)' onclick='onSize(" . $row["size_id"] . ")'>" . $row["size_name"] . "</a>";
            echo "</td><td>";
            echo $row["goods_price"];
            echo "</td><td>";
            echo $row["goods2_in"];
            echo "</td><td>";
            echo $row["goods2_out"];
            echo "</td><td>";
            echo $row["goods2_left"];
            echo "</td><td>";
            if (!empty($row["goods2_url"]))
            {
                echo "<a href=\"" . $row["goods2_url"] . "\" target=\"_blank\">" . $row["goods2_url"] . "</a>";
            }
            echo "</td></tr>";
        }
    }
?>
    </table>
</body>
</html>
