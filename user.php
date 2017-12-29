<?php require_once("conn.php") ?>
<?php
if (isset($_POST["fuser"])) {
    if (isset($_POST["faction"])) {
        if ($_POST["faction"] == "list") {
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
        }
    }
}
?>