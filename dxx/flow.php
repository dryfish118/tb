<?php require_once("conn.php") ?>
<?php
$flogin = isset($_POST["flogin"]) ? $_POST["flogin"] : 0;
$faction = isset($_POST["faction"]) ? $_POST["faction"] : "";
if ($flogin == 0 || $faction == "") {
    return;
}

$fid = isset($_POST["fid"]) ? $_POST["fid"] : 0;
$fname = isset($_POST["fname"]) ? $_POST["fname"] : "";
$ftaobao = isset($_POST["ftaobao"]) ? $_POST["ftaobao"] : "";
$ftel = isset($_POST["ftel"]) ? $_POST["ftel"] : "";
$ftel2 = isset($_POST["ftel2"]) ? $_POST["ftel2"] : "";
$faddr = isset($_POST["faddr"]) ? $_POST["faddr"] : "";
$fcode = isset($_POST["fcode"]) ? $_POST["fcode"] : "";
$fcurrent = isset($_POST["fcurrent"]) ? $_POST["fcurrent"] : 1;
$fcount = isset($_POST["fcount"]) ? $_POST["fcount"] : 0;
$fordertype = isset($_POST["fordertype"]) ? $_POST["fordertype"] : 0;
$forderdir = isset($_POST["forderdir"]) ? $_POST["forderdir"] : 0;
switch ($faction) {
    case "list" : {
        $pages = 0;
        if ($fcount > 0 && $fcurrent > 0) {
            $sql = "select count(*) as t from flow";
            $rs = $conn->query($sql);
            if ($rs) {
                $row= $rs->fetch_assoc();
                $total = $row["t"];
                if ($total > 0) {
                    $pages = (int)($total / $fcount);
                    if ($total % $fcount) {
                        $pages++;
                    }
                    if ($fcurrent > $pages) {
                        $fcurrent  = $pages;
                    }
                }
            }
        }
        $sql = "select flow_id, issue_id, issue_name, user_id, user_name, "
        . "date_format(flow_time, '%Y-%m-%d') as flow_time, "
        . "flow_money, flow_remark "
        . "from user inner join (issue inner join flow "
        . "on issue.issue_id = flow.flow_issue_id) "
        . "on user.user_id = flow.flow_user_id";
        $sql .= " order by ";
        if ($fordertype == 0) {
            $sql .= "issue_name";
        } else if ($fordertype == 1) {
            $sql .= "user_name";
        } else if ($fordertype == 2) {
            $sql .= "flow_time";
        } else {
            $sql .= "flow_money";
        }
        if ($forderdir != 0) {
            $sql .= " desc";
        }
        if ($fcount > 0 && $fcurrent > 0) {
            $sql = $sql . " limit " . (($fcurrent - 1) * $fcount) . "," . $fcount;
        }
        $rs = $conn->query($sql);
        if ($rs) {
            $count = 0;
            $json = "{\"current\":\"$fcurrent\",\"pages\":\"$pages\",\"flow\":[";
            while ($row = $rs->fetch_assoc()) {
                if ($count) {
                    $json .= ",";
                }
                $count++;
				$json .= "{\"id\":" . $row["flow_id"] . 
					",\"issue\":\"" . $row["issue_name"] . 
					"\",\"user\":\"" . $row["user_name"] . 
					"\",\"time\":\"" . $row["flow_time"] . 
					"\",\"money\":\"" . $row["flow_money"] . 
					"\",\"remark\":\"" . trimReturn($row["flow_remark"]) . "\"}";
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
                $result = addHistory($flogin, "add", "client", $fname);
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
                    $result = addHistory($flogin, "delete", "client", $fname);
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
                    $result = addHistory($flogin, "update", "client", "$fname_old->$fname");
                }
            }
        }
        echo $result;
        break;
    }
}
?>