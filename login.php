<?php require_once("conn.php") ?>
<?php
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
?>
