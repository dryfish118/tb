﻿<?php require_once("conn.php") ?>
<?php
$fuser = isset($_POST["fuser"]) ? $_POST["fuser"] : 0;
$faction = isset($_POST["faction"]) ? $_POST["faction"] : "";
if ($fuser == 0 || $faction == "") {
    return;
}

$fid = isset($_POST["fid"]) ? $_POST["fid"] : 0;
$fname = isset($_POST["fname"]) ? $_POST["fname"] : "";
$ftaobao = isset($_POST["ftaobao"]) ? $_POST["ftaobao"] : "";
$ftel = isset($_POST["ftel"]) ? $_POST["ftel"] : "";
$ftel2 = isset($_POST["ftel2"]) ? $_POST["ftel2"] : "";
$faddr = isset($_POST["faddr"]) ? $_POST["faddr"] : "";
$fcode = isset($_POST["fcode"]) ? $_POST["fcode"] : "";
switch ($faction) {
    case "list" : {
        $sql = "select * from client order by client_id limit 0,10";
        $rs = $conn->query($sql);
        if ($rs) {
            $count = 0;
            $json = "{\"client\":[";
            while ($row = $rs->fetch_assoc()) {
                if ($count) {
                    $json .= ",";
                }
                $count++;
				$json .= "{\"id\":" . $row["client_id"] . 
					",\"name\":\"" . $row["client_name"] . 
					"\",\"taobao\":\"" . $row["client_taobao"] . 
					"\",\"tel\":\"" . $row["client_tel"] . 
					"\",\"tel2\":\"" . $row["client_tel2"] . 
					"\",\"addr\":\"" . $row["client_addr"] . 
					"\",\"code\":\"" . $row["client_code"] . "\"}";
            }
            $json .= "]}";
            $rs->free();
    
            echo $json;
        }
        break;
    }
    case "add": {
        $result = 0;
		if ($fname != "" && $ftaobao != "") {
			$sql = "insert into client(client_name, client_taobao," .
                "client_tel, client_tel2, client_addr, client_code) values(" .
                "'$fname','$ftaobao','$ftel','$ftel2','$faddr','$fcode')";
            if ($conn->query($sql)) {
                $result = addHistory($fuser, "add", "client", $fname);
            }
        }
        echo $result;
        break;
    }
    case "delete": {
        $result = 0;
        if ($fid > 0) {
            $sql = "select client_name from client where client_id=$fid";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname = $row["client_name"];
                $sql = "delete from client where client_id=$fid";
                if ($conn->query($sql)) {
                    $result = addHistory($fuser, "delete", "client", $fname);
                }
            }
        }
        echo $result;
        break;
    }
    case "update": {
        $result = 0;
		if ($fid > 0 && $fname != "" && $ftaobao != "") {
            $sql = "select client_name from client where client_id=$fid";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname_old = $row["client_name"];
				$sql = "update client set client_name='$fname', client_taobao='$ftaobao'," .
                    "client_tel='$ftel', client_tel2='$ftel2', client_addr='$faddr'," .
                    "client_code='$fcode' where client_id='$fid'";
                if ($conn->query($sql)) {
                    $result = addHistory($fuser, "update", "client", "$fname_old->$fname");
                }
            }
        }
        echo $result;
        break;
    }
}
?>