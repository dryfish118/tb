<?php
	$file_error = isset($_FILES["file"]["error"]) ? $_FILES["file"]["error"] : 0;
	$file_size = isset($_FILES["file"]["size"]) ? $_FILES["file"]["size"] : 0;
	$file_name = isset($_FILES["file"]["name"]) ? $_FILES["file"]["name"] : "";
	$file_tmp_name = isset($_FILES["file"]["tmp_name"]) ? $_FILES["file"]["tmp_name"] : "";
	
	$result = "";
	if ($file_error == 0)
	{
		if ($file_size != 0)
		{
			if (file_exists($file_name))
			{
				if (!unlink($file_name))
				{
					$result = "文件已存在，并且删除失败";
				}
		    }
		    if ($result == "")
		    {
			    if (move_uploaded_file($file_tmp_name, $file_name))
			    {
			    	$result = "上传成功";
			    }
			    else
			    {
			    	$result = "上传失败";
			    }
			}
		}
	}
	else
	{
		$result = "上传错误 [" . $file_error . "]";
	}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="zh-cn" />
<title>更新文件</title>
</head>
<body>
	<?php echo $result; ?>
	<form action="./upfile.php" method="post" enctype="multipart/form-data">
		<input type="file" name="file" id="file" />
		<input type="submit" value="提交" />
	</form>
</body>
</html>
