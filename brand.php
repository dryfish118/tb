<?php require_once("conn.php") ?>
<?php
if (!isset($_POST["fuser"])) {
    return;
}

if (!isset($_POST["faction"])) {
    return;
}

switch ($_POST["faction"]) {
    case "list" : {
        $sql = "select * from brand";
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
        if (isset($_POST["fname"]) && $_POST["fname"] != "") {
            $sql = "insert into brand(brand_name) values('" .
                $_POST["fname"] . "')";
            if ($conn->query($sql)) {
                $result = addHistory($_POST["fuser"], "add", "brand", $_POST["fname"]);
            }
        }
        echo $result;
        break;
    }
    case "delete": {
        $result = 0;
        if (isset($_POST["fid"])) {
            $sql = "select brand_name from brand where brand_id='" . $_POST["fid"] . "'";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname = $row["brand_name"];
                $sql = "delete from brand where brand_id='" . $_POST["fid"] . "'";
                if ($conn->query($sql)) {
                    $result = addHistory($_POST["fuser"], "delete", "brand", $fname);
                }
            }
        }
        echo $result;
        break;
    }
    case "update": {
        $result = 0;
        if (isset($_POST["fid"]) && isset($_POST["fname"]) && $_POST["fname"] != "") {
            $sql = "select brand_name from brand where brand_id='" . $_POST["fid"] . "'";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname = $row["brand_name"];
                $sql = "update brand set brand_name='" . $_POST["fname"] .
                    "' where brand_id='" . $_POST["fid"] ."'";
                if ($conn->query($sql)) {
                    $result = addHistory($_POST["fuser"], "update", "brand", $_POST["fname"] . "(" . $fname . ")");
                }
            }
        }
        echo $result;
        break;
    }
}
?>