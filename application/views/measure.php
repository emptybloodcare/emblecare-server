<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<form action="/api/measure" method="post" enctype="multipart/form-data">
		<input type="text" name="user_idx" value="15">
		<input type="text" name="period" value="1">
		<input type="file" name="video">
		<input type="submit" value="전송">
	</form>
</body>
</html>