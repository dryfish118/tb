<?php require_once("conn.php") ?>
<?php
if (!isset($_POST["flogin"]) || !isset($_POST["faction"]) || $_POST["faction"] != "list") {
    return;
}

$fcurrent = isset($_POST["fcurrent"]) ? $_POST["fcurrent"] : 1;
$fcount = isset($_POST["fcount"]) ? $_POST["fcount"] : 0;
$pages = 0;
if ($fcount > 0 && $fcurrent > 0) {
	$sql = "select count(*) as t from brand";
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
$sql = "select history_id, history_time, history_action, history_table, "
. "history_contents, user_name "
. "from user inner join history "
. "on history.history_user_id = user.user_id "
. "order by history_time desc";
if ($fcount > 0 && $fcurrent > 0) {
	$sql = $sql . " limit " . (($fcurrent - 1) * $fcount) . "," . $fcount;
}
$rs = $conn->query($sql);
if ($rs) {
	$count = 0;
	$json = "{\"current\":\"$fcurrent\",\"pages\":\"$pages\",\"history\":[";
	while ($row = $rs->fetch_assoc()) {
		if ($count) {
			$json .= ",";
		}
		$count++;
		$contents = $row["history_action"];
		$contents = $contents . "[" . $row["history_table"] . "]: ";
		$contents = $contents . $row["history_contents"];
		$json .= "{\"id\":" . $row["history_id"] . 
			",\"user\":\"" . $row["user_name"] .
			"\",\"time\":\"" . $row["history_time"] .
			"\",\"contents\":\"" . $contents . "\"}";
	}
	$json .= "]}";
	$rs->free();

	echo $json;
}
?>
