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
        $sql = "select * from issue";
        $rs = $conn->query($sql);
        if ($rs) {
            $count = 0;
            $json = "{\"issue\":[";
            while ($row = $rs->fetch_assoc()) {
                if ($count) {
                    $json .= ",";
                }
                $count++;
                $json .= "{\"id\":" . $row["issue_id"] . ",\"name\":\"" . $row["issue_name"] . "\",\"out\":\"" . $row["issue_out"] . "\"}";
            }
            $json .= "]}";
            $rs->free();
    
            echo $json;
        }
        break;
    }
    case "add": {
        $result = 0;
        if (isset($_POST["fname"]) && isset($_POST["fout"])) {
            $sql = "insert into issue(issue_name, issue_out) values('" 
                . $_POST["fname"] ."','" 
                . ($_POST["fout"] == "on" ? 1 : 0)
                . "')";
            if ($conn->query($sql)) {
                $result = addHistory("添加", "条目", $_POST["fname"] . "(" . ($_POST["fout"] == "on" ? "支出" : "收入") . ")");
            }
        }
        echo $result;
        break;
    }
    case "delete": {
        $result = 0;
        if (isset($_POST["fid"])) {
            $sql = "select issue_name from issue where issue_id='" . $_POST["fid"] . "'";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname = $row["issue_name"];
                $sql = "delete from issue where issue_id='" . $fid . "'";
                if ($conn->query($sql)) {
                    $result = addHistory("删除", "条目", $issue_name);
                }
            }
        }
        echo $result;
        break;
    }
    case "update": {
        $result = 0;
        if (isset($_POST["fid"]) && isset($_POST["fname"]) && isset($_POST["fout"])) {
            $sql = "select issue_name from issue where issue_id='" . $_POST["fid"] . "'";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $issue_name = $row["issue_name"];
                $sql = "update issue set issue_name='" . $_POST["fname"] 
                    . "', issue_out=" . ($_POST["fout"] == "on" ? 1 : 0)
                    . " where issue_id='" . $_POST["fid"] ."'";
                    if ($conn->query($sql)) {
                    $result = addHistory("修改", "条目", $_POST["fname"] . "(" . $issue_name . ")");
                }
            }
        }
        echo $result;
        break;
    }
}
?>