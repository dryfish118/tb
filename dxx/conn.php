<?php
    date_default_timezone_set('PRC');

    $conn = new mysqli("localhost", "root", "admin", "dxx");
    if ($conn->connect_error) {
        die($conn->connect_error);
    }
    mysqli_query($conn, "set names utf8");
    
    function addHistory($user, $action, $table, $contents) {
        $sql = "insert into history(history_user_id,history_action,history_table,history_contents) "
            . "values(\"" . $user . "\",\"" . $action 
            . "\",\"" . $table . "\",\"" . $contents . "\")";
        global $conn;
        return $conn->query($sql) ? 1 : 0;
    }

    function trimReturn($str) {
        $str = trim($str, "\r\n ");
        $str = str_replace("\r", " ", $str);
        $str = str_replace("\n", " ", $str);
        return $str;
    }

    function toTop($fid, $flogin, $page) {
        global $conn;
        $sql = "select " . $page . "_name as fname, " . $page . "_order as forder from $page where " . $page . "_id=$fid";
        $rs = $conn->query($sql);
        if ($rs) {
            $row = $rs->fetch_assoc();
            $fname = $row["fname"];
            $forder = $row["forder"];

            $sql = "select min(" . $page . "_order) as forder from $page where " . $page . "_order<$forder";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $forderMin = $row["forder"];

                $conn->autocommit(false);
                $sql = "update $page set " . $page . "_order=" . $page . "_order+1 where " . $page . "_order<$forder";
                if ($conn->query($sql)) {
                    $sql = "update $page set " . $page . "_order=$forderMin  where " . $page . "_id=$fid";
                    if ($conn->query($sql)) {
                        addHistory($flogin, "top", $page, $fname);
                        $conn->commit();
                        return 1;
                    } else {
                        $conn->rollback();
                    }
                } else {
                    $conn->rollback();
                }
            }
        }
        return 0;
    }

    function toBottom($fid, $flogin, $page) {
        global $conn;
        $sql = "select " . $page . "_name as fname, " . $page . "_order as forder from $page where " . $page . "_id=$fid";
        $rs = $conn->query($sql);
        if ($rs) {
            $row = $rs->fetch_assoc();
            $fname = $row["fname"];
            $forder = $row["forder"];

            $sql = "select max(" . $page . "_order) as forder from $page where " . $page . "_order>$forder";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $forderMax = $row["forder"];

                $conn->autocommit(false);
                $sql = "update $page set " . $page . "_order=" . $page . "_order-1 where " . $page . "_order>$forder";
                if ($conn->query($sql)) {
                    $sql = "update $page set " . $page . "_order=$forderMax where " . $page . "_id=$fid";
                    if ($conn->query($sql)) {
                        addHistory($flogin, "bottom", $page, $fname);
                        $conn->commit();
                        return 1;
                    } else {
                        $conn->rollback();
                    }
                } else {
                    $conn->rollback();
                }
            }
        }
        return 0;
    }

    function toUp($fid, $flogin, $page) {
        global $conn;
        $sql = "select " . $page . "_name as fname, " . $page . "_order as forder from $page where " . $page . "_id=$fid";
        $rs = $conn->query($sql);
        if ($rs) {
            $row = $rs->fetch_assoc();
            $fname = $row["fname"];
            $forder = $row["forder"];

            $sql = "select max(" . $page . "_order) as forder from $page where " . $page . "_order<$forder";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $forderMax = $row["forder"];

                $conn->autocommit(false);
                $sql = "update $page set " . $page . "_order=$forder where " . $page . "_order=$forderMax";
                if ($conn->query($sql)) {
                    $sql = "update $page set " . $page . "_order=$forderMax where " . $page . "_id=$fid";
                    if ($conn->query($sql)) {
                        addHistory($flogin, "up", $page, $fname);
                        $conn->commit();
                        return 1;
                    } else {
                        $conn->rollback();
                    }
                } else {
                    $conn->rollback();
                }
            }
        }
        return 0;
    }

    function toDown($fid, $flogin, $page) {
        global $conn;
        $sql = "select " . $page . "_name as fname, " . $page . "_order as forder from $page where " . $page . "_id=$fid";
        $rs = $conn->query($sql);
        if ($rs) {
            $row = $rs->fetch_assoc();
            $fname = $row["fname"];
            $forder = $row["forder"];

            $sql = "select min(" . $page . "_order) as forder from $page where " . $page . "_order>$forder";
            $rs = $conn->query($sql);
            if ($rs) {
                $row = $rs->fetch_assoc();
                $forderMin = $row["forder"];

                $conn->autocommit(false);
                $sql = "update $page set " . $page . "_order=$forder where " . $page . "_order=$forderMin";
                if ($conn->query($sql)) {
                    $sql = "update $page set " . $page . "_order=$forderMin where " . $page . "_id=$fid";
                    if ($conn->query($sql)) {
                        addHistory($flogin, "up", $page, $fname);
                        $conn->commit();
                        return 1;
                    } else {
                        $conn->rollback();
                    }
                } else {
                    $conn->rollback();
                }
            }
        }
        return 0;
    }
?>