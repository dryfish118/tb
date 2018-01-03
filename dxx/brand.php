<?php require_once("conn.php") ?>
<?php
$fuser = isset($_POST["fuser"]) ? $_POST["fuser"] : 0;
$faction = isset($_POST["faction"]) ? $_POST["faction"] : "";
if ($fuser == 0 || $faction == "") {
    return;
}

$fid = isset($_POST["fid"]) ? $_POST["fid"] : 0;
$fname = isset($_POST["fname"]) ? $_POST["fname"] : "";
switch ($faction) {
    case "list" : {
        $sql = "select * from brand order by brand_id";
        $rs = $conn->query($sql);
        if ($rs) {
            $count = 0;
            $json = "{\"brand\":[";
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
                $result = addHistory($fuser, "add", "brand", $fname);
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
                    $result = addHistory($fuser, "delete", "brand", $fname);
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
                    $result = addHistory($fuser, "update", "brand", "$fname_old->$fname");
                }
            }
        }
        echo $result;
        break;
    }
}
?>