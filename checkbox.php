<html>
<head><title>a</title>
<script type="text/javascript">
<!--
function test() {
  alert('test');
}
//-->
</script>
</head>
<body>
<?php
echo $_POST["ch1"];
?>
<form action="checkbox.php" method="post">
<input type="checkbox" name="ch1" onclick="test();">
<input type="submit" value="test"></form>
</body>
</html>