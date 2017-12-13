<?php require_once("conn.php") ?>
<?php
    class CCat1Data
    {
        var $id;
        var $name;
    }
    
    class CCat1Set
    {
        var $m_items = array();
        
        function __construct()
        {
            $i = 0;
            $sql = "select * from cat1";
            $rs = $conn->query($sql);
            while ($row = $rs->fetch_assoc())
            {
                $this->m_items[$i] = new CCat1Data;
                $this->m_items[$i]->id = $row["cat1_id"];
                $this->m_items[$i]->name = $row["cat1_name"];
                $i++;
            }
            $rs->free();
        }
    }
?>