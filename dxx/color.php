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
        $sql = "select * from color order by color_order";
        $rs = $conn->query($sql);
        if ($rs) {
            $count = 0;
            $json = "{\"color\":[";
            while ($row = $rs->fetch_assoc()) {
                if ($count) {
                    $json .= ",";
                }
                $count++;
                $json .= "{\"id\":" . $row["color_id"] . 
                    ",\"name\":\"" . $row["color_name"] . 
                    "\",\"order\":\"" . $row["color_order"] . "\"}";
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
            $sql = "insert into color(color_name, color_order) select '" .
                $_POST["fname"] . "', (max(color_order) + 1) from color";
            if ($conn->query($sql)) {
                $result = addHistory($_POST["fuser"], "add", "color", $_POST["fname"]);
            }
        }
        echo $result;
        break;
    }
    case "delete": {
        $result = 0;
        if (isset($_POST["fid"])) {
            $sql = "select color_name, color_order from color where color_id='" . $_POST["fid"] . "'";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname = $row["color_name"];
                $forder = $row["color_order"];
                
                $conn->autocommit(false);
                $sql = "delete from color where color_id='" . $_POST["fid"] . "'";
                if ($conn->query($sql)) {
                    $sql = "update color set color_order = color_order - 1 where color_order > " . $color_order;
                    if ($conn->query($sql)) {
                        $result = addHistory($_POST["fuser"], "delete", "color", $fname);
                        $conn->commit();
                    } else {
                        $conn->rollback();
                    }
                } else {
                    $conn->rollback();
                }
            }
        }
        echo $result;
        break;
    }
    case "update": {
        $result = 0;
        if (isset($_POST["fid"]) && isset($_POST["fname"]) && $_POST["fname"] != "") {
            $sql = "select color_name from color where color_id='" . $_POST["fid"] . "'";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname = $row["color_name"];
                $sql = "update color set color_name='" . $_POST["fname"] .
                    "' where color_id='" . $_POST["fid"] ."'";
                if ($conn->query($sql)) {
                    $result = addHistory($_POST["fuser"], "update", "color", $_POST["fname"] . "(" . $fname . ")");
                }
            }
        }
        echo $result;
        break;
    }
    case "top": {
        echo toTop("color");
        break;
    }
    case "bottom": {
        echo toBottom("color");
        break;
    }
    case "up": {
        echo toUp("color");
        break;
    }
    case "down": {
        echo toDown("color");
        break;
    }
}
?>