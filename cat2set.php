<?php require_once("conn.php") ?>
<?php
    class CCat2Data
    {
        var $id;
        var $name;
        var $cat1;
    }
    
    class CCat2Set
    {
        var $m_items = array();
        
        function __construct()
        {
            $i = 0;
            $sql = "select cat1_name, cat2_id, cat2_name from cat1 inner join "
            ."cat2 on cat1.cat1_id = cat2.cat2_cat1_id";
            $rs = $conn->query($sql);
            while ($row = $rs->fetch_assoc())
            {
                $this->m_items[$i] = new CCat2Data;
                $this->m_items[$i]->id = $row["cat2_id"];
                $this->m_items[$i]->name = $row["cat2_name"];
                $this->m_items[$i]->cat1 = $row["cat1_name"];
                $i++;
            }
            $rs->free();
        }
    }
?>