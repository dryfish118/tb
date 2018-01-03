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
		if (isset($_POST["fname"]) && $_POST["fname"] != "" &&
			isset($_POST["ftaobao"]) && $_POST["ftaobao"] != "" &&
			isset($_POST["ftel"]) && isset($_POST["ftel2"]) &&
			isset($_POST["faddr"]) && isset($_POST["fcode"])) {
			$sql = "insert into client(client_name, client_taobao," .
				"client_tel, client_tel2, client_addr, client_code) values('" .
				$_POST["fname"] . "','" . $_POST["ftaobao"] . "','" .
				$_POST["ftel"] . "','" . $_POST["ftel2"] . "','" .
				$_POST["faddr"] . "','" . $_POST["fcode"] . "')";
            if ($conn->query($sql)) {
                $result = addHistory($_POST["fuser"], "add", "client", $_POST["fname"]);
            }
        }
        echo $result;
        break;
    }
    case "delete": {
        $result = 0;
        if (isset($_POST["fid"])) {
            $sql = "select client_name from client where client_id='" . $_POST["fid"] . "'";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname = $row["client_name"];
                $sql = "delete from client where client_id='" . $_POST["fid"] . "'";
                if ($conn->query($sql)) {
                    $result = addHistory($_POST["fuser"], "delete", "client", $fname);
                }
            }
        }
        echo $result;
        break;
    }
    case "update": {
        $result = 0;
		if (isset($_POST["fid"]) && isset($_POST["fname"]) && $_POST["fname"] != "" &&
			isset($_POST["ftaobao"]) && $_POST["ftaobao"] != "" &&
			isset($_POST["ftel"]) && isset($_POST["ftel2"]) &&
			isset($_POST["faddr"]) && isset($_POST["fcode"])) {
            $sql = "select client_name from client where client_id='" . $_POST["fid"] . "'";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname = $row["client_name"];
				$sql = "update client set client_name='" . $_POST["fname"] .
					"', client_taobao='" . $_POST["ftaobao"] .
					"', client_tel='" . $_POST["ftel"] .
					"', client_tel2='" . $_POST["ftel2"] .
					"', client_addr='" . $_POST["faddr"] .
					"', client_code='" . $_POST["fcode"] .
                    "' where client_id='" . $_POST["fid"] ."'";
                if ($conn->query($sql)) {
                    $result = addHistory($_POST["fuser"], "update", "client", $_POST["fname"] . "(" . $fname . ")");
                }
            }
        }
        echo $result;
        break;
    }
}
?>