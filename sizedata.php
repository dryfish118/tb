<?php require_once("sort_help.php") ?>
<?php
	class CSizeSet extends COrderDataMgr
	{
		function __construct()
		{
			$i = 0;
			$sql = "select * from size order by size_order";
			$rs = mysql_query($sql);
			if ($rs)
			{
				while ($row = mysql_fetch_array($rs))
				{
					$this->m_items[$i] = new COrderData;
					$this->m_items[$i]->id = $row["size_id"];
					$this->m_items[$i]->name = $row["size_name"];
					$this->m_items[$i]->order = $row["size_order"];
					$i++;
				}
			}
		}
	}
?>