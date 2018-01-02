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
        if (isset($_POST["fname"]) && $_POST["fname"] != "") {
            $sql = "insert into size(size_name) values('" .
                $_POST["fname"] . "')";
            if ($conn->query($sql)) {
                $result = addHistory($_POST["fuser"], "add", "size", $_POST["fname"]);
            }
        }
        echo $result;
        break;
    }
    case "delete": {
        $result = 0;
        if (isset($_POST["fid"])) {
            $sql = "select size_name from size where size_id='" . $_POST["fid"] . "'";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname = $row["size_name"];
                $sql = "delete from size where size_id='" . $_POST["fid"] . "'";
                if ($conn->query($sql)) {
                    $result = addHistory($_POST["fuser"], "delete", "size", $fname);
                }
            }
        }
        echo $result;
        break;
    }
    case "update": {
        $result = 0;
        if (isset($_POST["fid"]) && isset($_POST["fname"]) && $_POST["fname"] != "") {
            $sql = "select size_name from size where size_id='" . $_POST["fid"] . "'";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname = $row["size_name"];
                $sql = "update size set size_name='" . $_POST["fname"] .
                    "' where size_id='" . $_POST["fid"] ."'";
                if ($conn->query($sql)) {
                    $result = addHistory($_POST["fuser"], "update", "size", $_POST["fname"] . "(" . $fname . ")");
                }
            }
        }
        echo $result;
        break;
    }
}
?>