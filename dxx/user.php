<?php require_once("conn.php") ?>
<?php
$flogin = isset($_POST["flogin"]) ? $_POST["flogin"] : 0;
$faction = isset($_POST["faction"]) ? $_POST["faction"] : "";
if ($faction == "") {
    return;
}

$fid = isset($_POST["fid"]) ? $_POST["fid"] : 0;
$fname = isset($_POST["fname"]) ? $_POST["fname"] : "";
$forderdir = isset($_POST["forderdir"]) ? $_POST["forderdir"] : 0;
switch ($faction) {
    case "login": {
        $user_id = -1;
        if ($fname != "") {
            $sql = "select * from user where user_name like '$fname'";
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
        if ($flogin == 0) {
            return;
        }
        $sql = "select * from user order by user_id";
        if ($forderdir != 0) {
            $sql .= " desc";
        }
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
        if ($flogin != 0 && $fname != "") {
            $sql = "insert into user(user_name) values('$fname')";
            if ($conn->query($sql)) {
                $result = addHistory($flogin, "add", "user", $fname);
            }
        }
        echo $result;
        break;
    }
    case "delete": {
        $result = 0;
        if ($flogin != 0 && $fid > 0) {
            $sql = "select user_name from user where user_id=$fid";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname = $row["user_name"];
                $sql = "delete from user where user_id=$fid";
                if ($conn->query($sql)) {
                    $result = addHistory($flogin, "delete", "user", $fname);
                }
            }
        }
        echo $result;
        break;
    }
    case "update": {
        $result = 0;
        if ($flogin != 0 && $fid > 0 && $fname != "") {
            $sql = "select user_name from user where user_id=$fid";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname_old = $row["user_name"];
                $sql = "update user set user_name='$fname' where user_id=$fid";
                if ($conn->query($sql)) {
                    $result = addHistory($flogin, "update", "user", "$fname_old->$fname");
                }
            }
        }
        echo $result;
        break;
    }
}
?>