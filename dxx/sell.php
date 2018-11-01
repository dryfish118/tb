<?php require_once("conn.php") ?>
<?php
$flogin = isset($_POST["flogin"]) ? $_POST["flogin"] : 0;
$faction = isset($_POST["faction"]) ? $_POST["faction"] : "";
if ($flogin == 0 || $faction == "") {
    return;
}

$fid = isset($_POST["fid"]) ? $_POST["fid"] : 0;
$fbrand = isset($_POST["fbrand"]) ? $_POST["fbrand"] : "";
$fcat1 = isset($_POST["fcat1"]) ? $_POST["fcat1"] : "";
$fcat2 = isset($_POST["fcat2"]) ? $_POST["fcat2"] : "";
$ftype = isset($_POST["ftype"]) ? $_POST["ftype"] : "";
$fprice = isset($_POST["fprice"]) ? $_POST["fprice"] : 0;
$fremark = isset($_POST["fremark"]) ? $_POST["fremark"] : "";
$fcurrent = isset($_POST["fcurrent"]) ? $_POST["fcurrent"] : 1;
$fcount = isset($_POST["fcount"]) ? $_POST["fcount"] : 0;
$fordertype = isset($_POST["fordertype"]) ? $_POST["fordertype"] : 0;
$forderdir = isset($_POST["forderdir"]) ? $_POST["forderdir"] : 0;
switch ($faction) {
    case "list" : {
        $pages = 0;
        if ($fcount > 0 && $fcurrent > 0) {
            $sql = "select count(*) as t from goods";
            $rs = $conn->query($sql);
            if ($rs) {
                $row= $rs->fetch_assoc();
                $total = $row["t"];
                if ($total > 0) {
                    $pages = (int)($total / $fcount);
                    if ($total % $fcount) {
                        $pages++;
                    }
                    if ($fcurrent > $pages) {
                        $fcurrent  = $pages;
                    }
                }
            }
        }
        $sql = "select goods_id, goods_brand_id, brand_name, cat1_name, " . 
            "goods_cat2_id, cat2_name, goods_type, goods_price, goods_remark " .
            "from (cat1 inner join cat2 on cat1.cat1_id = cat2.cat2_cat1_id) " .
            "inner join (brand inner join goods on brand.brand_id = goods.goods_brand_id) " .
            "on cat2.cat2_id = goods.goods_cat2_id order by ";
        if ($fordertype == 1) {
            $sql .= "brand_name";
        } else if ($fordertype == 2) {
            $sql .= "cat1_name";
        } else if ($fordertype == 3) {
            $sql .= "cat2_name";
        } else if ($fordertype == 4) {
            $sql .= "goods_type";
        } else if ($fordertype == 5) {
            $sql .= "goods_price";
        } else {
            $sql .= "goods_id";
        }
        if ($forderdir != 0) {
            $sql .= " desc";
        }
        if ($fcount > 0 && $fcurrent > 0) {
            $sql = $sql . " limit " . (($fcurrent - 1) * $fcount) . "," . $fcount;
        }
        $rs = $conn->query($sql);
        if ($rs) {
            $count = 0;
            $json = "{\"current\":\"$fcurrent\",\"pages\":\"$pages\",\"goods\":[";
            while ($row = $rs->fetch_assoc()) {
                if ($count) {
                    $json .= ",";
                }
                $count++;
				$json .= "{\"id\":" . $row["goods_id"] . 
					",\"brandid\":" . $row["goods_brand_id"] . 
					",\"brand\":\"" . $row["brand_name"] . 
					"\",\"cat1\":\"" . $row["cat1_name"] . 
					"\",\"cat2id\":" . $row["goods_cat2_id"] . 
					",\"cat2\":\"" . $row["cat2_name"] . 
					"\",\"type\":\"" . $row["goods_type"] . 
					"\",\"price\":" . $row["goods_price"] . 
					",\"remark\":\"" . trimReturn($row["goods_remark"]) . "\"}";
            }
            $json .= "]}";
            $rs->free();
    
            echo $json;
        }
        break;
    }
    case "add": {
        $result = 0;
		if ($fname != "" && $ftaobao != "") {
			$sql = "insert into client(client_name, client_taobao," .
                "client_tel, client_tel2, client_addr, client_code) values(" .
                "'$fname','$ftaobao','$ftel','$ftel2','$faddr','$fcode')";
            if ($conn->query($sql)) {
                $result = addHistory($flogin, "add", "client", $fname);
            }
        }
        echo $result;
        break;
    }
    case "delete": {
        $result = 0;
        if ($fid > 0) {
            $sql = "select client_name from client where client_id=$fid";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname = $row["client_name"];
                $sql = "delete from client where client_id=$fid";
                if ($conn->query($sql)) {
                    $result = addHistory($flogin, "delete", "client", $fname);
                }
            }
        }
        echo $result;
        break;
    }
    case "update": {
        $result = 0;
		if ($fid > 0 && $fname != "" && $ftaobao != "") {
            $sql = "select client_name from client where client_id=$fid";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname_old = $row["client_name"];
				$sql = "update client set client_name='$fname', client_taobao='$ftaobao'," .
                    "client_tel='$ftel', client_tel2='$ftel2', client_addr='$faddr'," .
                    "client_code='$fcode' where client_id='$fid'";
                if ($conn->query($sql)) {
                    $result = addHistory($flogin, "update", "client", "$fname_old->$fname");
                }
            }
        }
        echo $result;
        break;
    }
}
?>