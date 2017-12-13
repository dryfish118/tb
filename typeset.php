<?php require_once("conn.php") ?>
<?php

    class CTypeData
    {
        var $name;
    }

    class CTypeSet
    {
        var $m_items = array();
        
        function __construct()
        {
            $i = 0;
            $sql = "select distinct(goods_type) as type from goods order by type";
            $rs = $conn->query($sql);
            while ($row = $rs->fetch_assoc())
            {
                $this->m_items[$i] = new CTypeData;
                $this->m_items[$i]->name = $row["type"];
                $i++;
            }
            $rs->free();
        }
    }

?>