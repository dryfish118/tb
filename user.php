<?php require_once("conn.php") ?>
<?php
if (!isset($_POST["faction"])) {
    return;
}

switch ($_POST["faction"]) {
    case "login": {
        $user_id = -1;
        if (isset($_POST["fname"])) {
            $sql = "select * from user where user_name like '" . $_POST["fname"] . "'";
            if ($rs = $conn->query($sql)) {
                if ($row = $rs->fetch_assoc()) {
                    $user_id = $row["user_id"];
                }
                $rs->free();
            }
            $conn->close();
        }
        echo $user_id;
        break;
    }
    case "userlist" : {
        if (!isset($_POST["fuser"])) {
            return;
        }
        $sql = "select * from user";
        $rs = $conn->query($sql);
        if ($rs) {
            $count = 0;
            $json = "{\"user\":[";
            while ($row = $rs->fetch_assoc()) {
                if ($count) {
                    $json .= ",";
                }
                $count++;
                $json .= "{\"id\":" . $row["user_id"] . ",\"name\":\"" . $row["user_name"] . "\"}";
            }
            $json .= "]}";
            $rs->free();
    
            echo $json;
        }
        break;
    }
}
?>