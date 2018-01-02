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
        $sql = "select * from size order by size_order";
        $rs = $conn->query($sql);
        if ($rs) {
            $count = 0;
            $json = "{\"size\":[";
            while ($row = $rs->fetch_assoc()) {
                if ($count) {
                    $json .= ",";
                }
                $count++;
                $json .= "{\"id\":" . $row["size_id"] . 
                    ",\"name\":\"" . $row["size_name"] . 
                    "\",\"order\":\"" . $row["size_order"] . "\"}";
            }
            $json .= "]}";
            $rs->free();
    
            echo $json;
        }
        break;
    }
    case "add": {
        $result = 0;
        if (isset($_POST["fname"]) && $_POST["fname"] != "") {
            $sql = "insert into size(size_name, size_order) select '" .
                $_POST["fname"] . "', (max(size_order) + 1) from size";
            if ($conn->query($sql)) {
                $result = addHistory($_POST["fuser"], "add", "size", $_POST["fname"]);
            }
        }
        echo $result;
        break;
    }
    case "delete": {
        $result = 0;
        if (isset($_POST["fid"])) {
            $sql = "select size_name, size_order from size where size_id='" . $_POST["fid"] . "'";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname = $row["size_name"];
                $forder = $row["size_order"];
                
                $conn->autocommit(false);
                $sql = "delete from size where size_id='" . $_POST["fid"] . "'";
                if ($conn->query($sql)) {
                    $sql = "update size set size_order = size_order - 1 where size_order > " . $size_order;
                    if ($conn->query($sql)) {
                        $result = addHistory($_POST["fuser"], "delete", "size", $fname);
                        $conn->commit();
                    } else {
                        $conn->rollback();
                    }
                } else {
                    $conn->rollback();
                }
            }
        }
        echo $result;
        break;
    }
    case "update": {
        $result = 0;
        if (isset($_POST["fid"]) && isset($_POST["fname"]) && $_POST["fname"] != "") {
            $sql = "select size_name from size where size_id='" . $_POST["fid"] . "'";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname = $row["size_name"];
                $sql = "update size set size_name='" . $_POST["fname"] .
                    "' where size_id='" . $_POST["fid"] ."'";
                if ($conn->query($sql)) {
                    $result = addHistory($_POST["fuser"], "update", "size", $_POST["fname"] . "(" . $fname . ")");
                }
            }
        }
        echo $result;
        break;
    }
    case "top": {
        $result = 0;
        if (isset($_POST["fid"]) && $_POST["fid"] > 0) {
            $sql = "select size_name, size_order from size where size_id='" . $_POST["fid"] . "'";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname = $row["size_name"];
                $order_current = $row["size_order"];

                $sql = "select min(size_order) as order_min from size";
                $rs = $conn->query($sql);
                if ($rs) {
                    $row = $rs->fetch_assoc();
                    $order_min = $row["order_min"];

                    if ($order_min < $order_current) {
                        $conn->autocommit(false);
                        $sql = "update size set size_order=size_order+1 where size_order<" . $order_current;
                        if ($conn->query($sql)) {
                            $sql = "update size set size_order=" . $order_min . " where size_id='" . $_POST["fid"] . "'";
                            if ($conn->query($sql)) {
                                $result = addHistory($_POST["fuser"], "top", "size", $fname);
                                $conn->commit();
                            } else {
                                $conn->rollback();
                            }
                        } else {
                            $conn->rollback();
                        }
                    }
                }
            }
        }
        echo $result;
        break;
    }
    case "bottom": {
        $result = 0;
        if (isset($_POST["fid"]) && $_POST["fid"] > 0) {
            $sql = "select size_name, size_order from size where size_id='" . $_POST["fid"] . "'";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname = $row["size_name"];
                $order_current = $row["size_order"];

                $sql = "select max(size_order) as order_max from size";
                $rs = $conn->query($sql);
                if ($rs) {
                    $row = $rs->fetch_assoc();
                    $order_max = $row["order_max"];

                    if ($order_max > $order_current) {
                        $conn->autocommit(false);
                        $sql = "update size set size_order=size_order-1 where size_order>" . $order_current;
                        if ($conn->query($sql)) {
                            $sql = "update size set size_order=" . $order_max . " where size_id='" . $_POST["fid"] . "'";
                            if ($conn->query($sql)) {
                                $result = addHistory($_POST["fuser"], "bottom", "size", $fname);
                                $conn->commit();
                            } else {
                                $conn->rollback();
                            }
                        } else {
                            $conn->rollback();
                        }
                    }
                }
            }
        }
        echo $result;
        break;
    }
    case "up": {
        $result = 0;
        if (isset($_POST["fid"]) && $_POST["fid"] > 0) {
            $sql = "select size_name, size_order from size where size_id='" . $_POST["fid"] . "'";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname = $row["size_name"];
                $order_current = $row["size_order"];

                $sql = "select max(size_order) as order_max from size where size_order<" . $order_current;
                $rs = $conn->query($sql);
                if ($rs) {
                    $row = $rs->fetch_assoc();
                    $order_max = $row["order_max"];

                    if ($order_max < $order_current) {
                        $conn->autocommit(false);
                        $sql = "update size set size_order=" . $order_current . " where size_order=" . $order_max;
                        if ($conn->query($sql)) {
                            $sql = "update size set size_order=" . $order_max . " where size_id='" . $_POST["fid"] . "'";
                            if ($conn->query($sql)) {
                                $result = addHistory($_POST["fuser"], "up", "size", $fname);
                                $conn->commit();
                            } else {
                                $conn->rollback();
                            }
                        } else {
                            $conn->rollback();
                        }
                    }
                }
            }
        }
        echo $result;
        break;
    }
    case "down": {
        $result = 0;
        if (isset($_POST["fid"]) && $_POST["fid"] > 0) {
            $sql = "select size_name, size_order from size where size_id='" . $_POST["fid"] . "'";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $fname = $row["size_name"];
                $order_current = $row["size_order"];

                $sql = "select min(size_order) as order_min from size where size_order>" . $order_current;
                $rs = $conn->query($sql);
                if ($rs) {
                    $row = $rs->fetch_assoc();
                    $order_min = $row["order_min"];

                    if ($order_min > $order_current) {
                        $conn->autocommit(false);
                        $sql = "update size set size_order=" . $order_current . " where size_order=" . $order_min;
                        if ($conn->query($sql)) {
                            $sql = "update size set size_order=" . $order_min . " where size_id='" . $_POST["fid"] . "'";
                            if ($conn->query($sql)) {
                                $result = addHistory($_POST["fuser"], "down", "size", $fname);
                                $conn->commit();
                            } else {
                                $conn->rollback();
                            }
                        } else {
                            $conn->rollback();
                        }
                    }
                }
            }
        }
        echo $result;
        break;
    }
}
?>