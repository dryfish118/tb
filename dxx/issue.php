<?php require_once("conn.php") ?>
<?php
$fuser = isset($_POST["fuser"]) ? $_POST["fuser"] : 0;
$faction = isset($_POST["faction"]) ? $_POST["faction"] : "";
if ($fuser == 0 || $faction == "") {
    return;
}

$fid = isset($_POST["fid"]) ? $_POST["fid"] : 0;
$fname = isset($_POST["fname"]) ? $_POST["fname"] : "";
$fout = isset($_POST["fout"]) ? $_POST["fout"] : 1;
$fordertype = isset($_POST["fordertype"]) ? $_POST["fordertype"] : 0;
$forder = isset($_POST["forder"]) ? $_POST["forder"] : 0;
switch ($faction) {
    case "list" : {
        $sql = "select * from issue order by ";
        if ($fordertype == 0) {
            $sql .= "issue_name";
        } else {
            $sql .= "issue_out";
        }
        if ($forder != 0) {
            $sql .= " desc";
        }
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
        if ($fname != "") {
            $sql = "insert into issue(issue_name, issue_out) values('$fname',$fout)";
            if ($conn->query($sql)) {
                $result = addHistory($fuser, "add", "issue", $fname . "(" . $fout . ")");
            }
        }
        echo $result;
        break;
    }
    case "delete": {
        $result = 0;
        if ($fid > 0) {
            $sql = "select issue_name from issue where issue_id=$fid";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname = $row["issue_name"];
                $sql = "delete from issue where issue_id=$fid";
                if ($conn->query($sql)) {
                    $result = addHistory($fuser, "delete", "issue", $fname);
                }
            }
        }
        echo $result;
        break;
    }
    case "update": {
        $result = 0;
        if ($fid > 0 && $fname != "") {
            $sql = "select issue_name from issue where issue_id=$fid";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname_old = $row["issue_name"];
                $sql = "update issue set issue_name='$fname', issue_out=$fout where issue_id=$fid";
                if ($conn->query($sql)) {
                    $result = addHistory($fuser, "update", "issue", "$fname_old->$fname");
                }
            }
        }
        echo $result;
        break;
    }
}
?>