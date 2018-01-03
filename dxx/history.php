<?php require_once("conn.php") ?>
<?php
if (!isset($_POST["fuser"]) || !isset($_POST["faction"]) || $_POST["faction"] != "list") {
    return;
}

$sql = "select history_id, history_time, history_action, history_table, "
. "history_contents, user_name "
. "from user inner join history "
. "on history.history_user_id = user.user_id "
. "order by history_time desc limit 0,100";
$rs = $conn->query($sql);
if ($rs) {
	$count = 0;
	$json = "{\"history\":[";
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
