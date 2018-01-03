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
        $sql = "select * from size order by size_order";
        $rs = $conn->query($sql);
        if ($rs) {
            $count = 0;
            $json = "{\"size\":[";
            while ($row = $rs->fetch_assoc()) {
                if ($count) {
                    $json .= ",";
                }
                $count++;
                $json .= "{\"id\":" . $row["size_id"] . 
                    ",\"name\":\"" . $row["size_name"] . 
                    "\",\"order\":\"" . $row["size_order"] . "\"}";
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
            $sql = "insert into size(size_name, size_order) select '$fname',(max(size_order)+1) from size";
            if ($conn->query($sql)) {
                $result = addHistory($fuser, "add", "size", $fname);
            }
        }
        echo $result;
        break;
    }
    case "delete": {
        $result = 0;
        if ($fid > 0) {
            $sql = "select size_name, size_order from size where size_id=$fid";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname = $row["size_name"];
                $forder = $row["size_order"];
                
                $conn->autocommit(false);
                $sql = "delete from size where size_id=$fid";
                if ($conn->query($sql)) {
                    $sql = "update size set size_order=size_order-1 where size_order>$forder";
                    if ($conn->query($sql)) {
                        $result = addHistory($fuser, "delete", "size", $fname);
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
            $sql = "select size_name from size where size_id=$fid";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname_old = $row["size_name"];
                $sql = "update size set size_name='$fname' where size_id=$fid";
                if ($conn->query($sql)) {
                    $result = addHistory($fuser, "update", "size", "$fname_old->$fname");
                }
            }
        }
        echo $result;
        break;
    }
    case "top": {
        echo toTop("size");
        break;
    }
    case "bottom": {
        echo toBottom("size");
        break;
    }
    case "up": {
        echo toUp("size");
        break;
    }
    case "down": {
        echo toDown("size");
        break;
    }
}
?>