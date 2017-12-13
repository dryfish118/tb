<?php require_once("conn.php") ?>
<?php
    class CBrandData
    {
        var $id;
        var $name;
    }
    
    class CBrandSet
    {
        var $m_items = array();
        
        function __construct()
        {
            $i = 0;
            $sql = "select * from brand order by brand_id desc";
            $rs = $conn->query($sql);
            while ($row = $rs->fetch_assoc())
            {
                $this->m_items[$i] = new CBrandData;
                $this->m_items[$i]->id = $row["brand_id"];
                $this->m_items[$i]->name = $row["brand_name"];
                $i++;
            }
            $rs->free();
        }
    }
?>