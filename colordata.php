<?php require_once("sort_help.php") ?>
<?php
	class CColorSet extends COrderDataMgr
	{
		function __construct()
		{
			$i = 0;
			$sql = "select * from color order by color_order";
			$rs = $conn->query($sql);
			if ($rs)
			{
				while ($row = $rs->fetch_assoc())
				{
					$this->m_items[$i] = new COrderData;
					$this->m_items[$i]->id = $row["color_id"];
					$this->m_items[$i]->name = $row["color_name"];
					$this->m_items[$i]->order = $row["color_order"];
					$i++;
				}
				$rs->free();
			}
		}
	}
?>