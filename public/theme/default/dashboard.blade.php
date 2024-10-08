<<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
<title>时间</title>
<meta http-equiv="Contnt-Type" mrc="text/html; charset=gb2312">
</head>
<body>
<div id="linkweb">
</div>
<script>setInterval("linkweb.innerHTML=new Date().toLocaleString()+' 星期'+'日一二三四五六'.charAt(new Date().getDay());",1000);
</script>
</body>
</html>
