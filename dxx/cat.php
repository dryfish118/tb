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
    case "listcat1" : {
        $sql = "select * from cat1 order by cat1_name";
        $rs = $conn->query($sql);
        if ($rs) {
            $count = 0;
            $json = "{\"cat\":[";
            while ($row = $rs->fetch_assoc()) {
                if ($count) {
                    $json .= ",";
                }
                $count++;
                $json .= "{\"id\":" . $row["cat1_id"] . ",\"name\":\"" . $row["cat1_name"] . "\"}";
            }
            $json .= "]}";
            $rs->free();
    
            echo $json;
        }
        break;
    }
    case "addcat1": {
        $result = 0;
        if ($fname != "") {
            $sql = "insert into cat1(cat1_name) values('$fname')";
            if ($conn->query($sql)) {
                $result = addHistory($fuser, "add", "cat1", $fname);
            }
        }
        echo $result;
        break;
    }
    case "deletecat1": {
        $result = 0;
        if ($fid > 0) {
            $sql = "select cat1_name from cat1 where cat1_id=$fid";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname = $row["cat1_name"];
                $sql = "delete from cat1 where cat1_id=$fid";
                if ($conn->query($sql)) {
                    $result = addHistory($fuser, "delete", "cat1", $fname);
                }
            }
        }
        echo $result;
        break;
    }
    case "updatecat1": {
        $result = 0;
        if ($fid > 0 && $fname != "") {
            $sql = "select cat1_name from cat1 where cat1_id=$fid";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname_old = $row["cat1_name"];
                $sql = "update cat1 set cat1_name='$fname' where cat1_id=$fid";
                if ($conn->query($sql)) {
                    $result = addHistory($fuser, "update", "cat1", "$fname_old->$fname");
                }
            }
        }
        echo $result;
        break;
    }
}
?>