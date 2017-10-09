<?php
	$faction = isset($_POST["faction"]) ? $_POST["faction"] : 0;
	$ffile = isset($_POST["ffile"]) ? $_POST["ffile"] : "";
	
	$dir = "./";
	if ($faction != 0 && $ffile != "")
	{
		if ($faction == 1)
		{
			unlink($ffile);
		}
		else if ($faction == 2)
		{
			$cab = $ffile;
			$pos = strrpos($cab, "/");
			if ($pos != -1)
			{
				$cab = substr($cab, $pos + 1);
				$cab = $cab . ".cab";
				$exe_command = "makecab " . $ffile . " " . $cab;
				exec($exe_command);
				echo "<script type=\"text/javascript\">window.open(\"./" . $cab . "\");</script>";
			}
		}
		else if ($faction == 3)
		{
			$dir = $ffile;
		}
	}

	class CFileInfo
	{
		var $name;
		var $path;
		var $attrib;
	}
	$fileInfos = array();
	if ($handle = opendir($dir))
	{
		$count = 0;
		if ($dir != "./")
		{
			$fileInfos[$count] = new CFileInfo;
			$fileInfos[$count]->name = "..";
			$fileInfos[$count]->path = substr($dir, 0, strrpos(substr($dir, 0, strlen($dir) - 1), "/") + 1);
			$fileInfos[$count]->attrib = 1;
			$count++;
			$dir = $dir . "/";
		}
		while (false !== ($file = readdir($handle)))
		{
			if ($file != "." && $file != "..")
			{
				$fileInfos[$count] = new CFileInfo;
				$fileInfos[$count]->name = $file;
				$fileInfos[$count]->path = $dir . $file;
				if (is_dir($dir . $file))
				{
					$fileInfos[$count]->attrib = 1;
				}
				else
				{
					$fileInfos[$count]->attrib = 0;
				}
				$count++;
			}
		}
		closedir($handle);
	}
	
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-cn" />
<title>文件管理</title>
<script type="text/javascript">
	function onDelete(f)
	{
		filst.faction.value = 1;
		filst.ffile.value = f;
		filst.submit();
	}
	function onDownload(f)
	{
		filst.faction.value = 2;
		filst.ffile.value = f;
		filst.submit();
	}
	function onEnter(f)
	{
		filst.faction.value = 3;
		filst.ffile.value = f;
		filst.submit();
	}
</script>
</head>
<body>
	<form method="post" name="filst" action="./filst.php" >
		<input type="hidden" name="faction" value="0" />
		<input type="hidden" name="ffile" value="" />
	</form>
	<table frame="border" rules="all">
		<tr>
			<th width="50">删除</th>
			<th width="50">下载</th>
			<th width="200">文件</th>
		</tr>
<?php
		foreach ($fileInfos as $fi)
		{
?>
		<tr>
			<td>
<?php 
				if ($fi->attrib == 0)
				{
					echo "<a href='javascript:void(0)' onclick='onDelete(\"" . $fi->path . "\")'>X</a>";
				}
?>
			</td>
			<td>
<?php 
				if ($fi->attrib == 0)
				{
					echo "<a href='javascript:void(0)' onclick='onDownload(\"" . $fi->path . "\")'>D</a>";
				}
?>
			</td>
			<td>
<?php 
				if ($fi->attrib == 0)
				{
					echo "<a href='" . $fi->path . "'>" . $fi->name . "</a>";
				}
				else
				{
					echo "<a href='javascript:void(0)' onclick='onEnter(\"" . $fi->path . "\")'>" . $fi->name . "</a>";
				}
?>
			</td>
		</tr>
<?php
		}
?>
	</table>
</body>
</html>