<?php require_once("conn.php") ?>
<?php
$flogin = isset($_POST["flogin"]) ? $_POST["flogin"] : 0;
$faction = isset($_POST["faction"]) ? $_POST["faction"] : "";
if ($flogin == 0 || $faction == "") {
    return;
}

$fid = isset($_POST["fid"]) ? $_POST["fid"] : 0;
$fname = isset($_POST["fname"]) ? $_POST["fname"] : "";
switch ($faction) {
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
        if ($fname != "") {
            $sql = "insert into color(color_name,color_order) select '$fname', (max(color_order)+1) from color";
            if ($conn->query($sql)) {
                $result = addHistory($flogin, "add", "color", $fname);
            }
        }
        echo $result;
        break;
    }
    case "delete": {
        $result = 0;
        if ($fid > 0) {
            $sql = "select color_name, color_order from color where color_id=$fid";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname = $row["color_name"];
                $forder = $row["color_order"];
                
                $conn->autocommit(false);
                $sql = "delete from color where color_id=$fid";
                if ($conn->query($sql)) {
                    $sql = "update color set color_order=color_order-1 where color_order>$forder";
                    if ($conn->query($sql)) {
                        $result = addHistory($flogin, "delete", "color", $fname);
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
        if ($fid > 0 && $fname != "") {
            $sql = "select color_name from color where color_id=$fid";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname_old = $row["color_name"];
                $sql = "update color set color_name='$fname' where color_id=$fid";
                if ($conn->query($sql)) {
                    $result = addHistory($flogin, "update", "color", "$fname_old->$fname");
                }
            }
        }
        echo $result;
        break;
    }
    case "top": {
        echo toTop($fid, $flogin, "color");
        break;
    }
    case "bottom": {
        echo toBottom($fid, $flogin, "color");
        break;
    }
    case "up": {
        echo toUp($fid, $flogin, "color");
        break;
    }
    case "down": {
        echo toDown($fid, $flogin, "color");
        break;
    }
}
?>