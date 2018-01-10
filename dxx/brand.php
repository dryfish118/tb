<?php require_once("conn.php") ?>
<?php
$flogin = isset($_POST["flogin"]) ? $_POST["flogin"] : 0;
$faction = isset($_POST["faction"]) ? $_POST["faction"] : "";
if ($flogin == 0 || $faction == "") {
    return;
}

$fid = isset($_POST["fid"]) ? $_POST["fid"] : 0;
$fname = isset($_POST["fname"]) ? $_POST["fname"] : "";
$fcurrent = isset($_POST["fcurrent"]) ? $_POST["fcurrent"] : 1;
$fcount = isset($_POST["fcount"]) ? $_POST["fcount"] : 0;
$forderdir = isset($_POST["forderdir"]) ? $_POST["forderdir"] : 0;
switch ($faction) {
    case "list" : {
        $pages = 0;
        if ($fcount > 0 && $fcurrent > 0) {
            $sql = "select count(*) as t from brand";
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
        $sql = "select * from brand order by brand_name";
        if ($forderdir != 0) {
            $sql .= " desc";
        }
        if ($fcount > 0 && $fcurrent > 0) {
            $sql = $sql . " limit " . (($fcurrent - 1) * $fcount) . "," . $fcount;
        }
        $rs = $conn->query($sql);
        if ($rs) {
            $count = 0;
            $json = "{\"current\":\"$fcurrent\",\"pages\":\"$pages\",\"brand\":[";
            while ($row = $rs->fetch_assoc()) {
                if ($count) {
                    $json .= ",";
                }
                $count++;
                $json .= "{\"id\":" . $row["brand_id"] . ",\"name\":\"" . $row["brand_name"] . "\"}";
            }
            $json .= "]}";
            $rs->free();
    
            echo $json;
        }
        break;
    }
    case "add": {
        $result = 0;
        if ($fname != "") {
            $sql = "insert into brand(brand_name) values('$fname')";
            if ($conn->query($sql)) {
                $result = addHistory($flogin, "add", "brand", $fname);
            }
        }
        echo $result;
        break;
    }
    case "delete": {
        $result = 0;
        if ($fid > 0) {
            $sql = "select brand_name from brand where brand_id=$fid";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname = $row["brand_name"];
                $sql = "delete from brand where brand_id=$fid";
                if ($conn->query($sql)) {
                    $result = addHistory($flogin, "delete", "brand", $fname);
                }
            }
        }
        echo $result;
        break;
    }
    case "update": {
        $result = 0;
        if ($fid > 0 && $fname != "") {
            $sql = "select brand_name from brand where brand_id=$fid";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname_old = $row["brand_name"];
                $sql = "update brand set brand_name='$fname' where brand_id=$fid";
                if ($conn->query($sql)) {
                    $result = addHistory($flogin, "update", "brand", "$fname_old->$fname");
                }
            }
        }
        echo $result;
        break;
    }
}
?>