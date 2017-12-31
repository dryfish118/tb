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
    case "list" : {
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
    case "add": {
        $result = 0;
        if (isset($_POST["fuser"]) && isset($_POST["fname"]) && $_POST["fname"] != "") {
            $sql = "insert into user(user_name) values('" . $_POST["fname"] . "')";
            if ($conn->query($sql)) {
                $result = addHistory($_POST["fuser"], "add", "user", $_POST["fname"]);
            }
        }
        echo $result;
        break;
    }
    case "delete": {
        $result = 0;
        if (isset($_POST["fuser"]) && isset($_POST["fid"])) {
            $sql = "select user_name from user where user_id='" . $_POST["fid"] . "'";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname = $row["user_name"];
                $sql = "delete from user where user_id='" . $_POST["fid"] . "'";
                if ($conn->query($sql)) {
                    $result = addHistory($_POST["fuser"], "delete", "user", $fname);
                }
            }
        }
        echo $result;
        break;
    }
    case "update": {
        $result = 0;
        if (isset($_POST["fuser"]) && isset($_POST["fid"]) && isset($_POST["fname"])) {
            $sql = "select user_name from user where user_id='" . $_POST["fid"] . "'";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname = $row["user_name"];
                $sql = "update user set user_name='" . $_POST["fname"] .
                    "' where user_id='" . $_POST["fid"] ."'";
                if ($conn->query($sql)) {
                    $result = addHistory($_POST["fuser"], "update", "user", $_POST["fname"] . "(" . $fname . ")");
                }
            }
        }
        echo $result;
        break;
    }
}
?>