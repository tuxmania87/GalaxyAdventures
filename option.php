<?php
include("head.php");
include("navlogged.php");
include("klassen.php");

include_once 'auth.php';
$id = requireLogin();
{


$ich=new Account($id);
//Fall4 Beschreibung ändern
if($_POST["sent"]==4) {
$beschreibung=($_POST["bla"]);
changeit($beschreibung);
mysqli_query($verbindung, "UPDATE account SET beschreibung='$beschreibung' WHERE id='$id'") or die(mysqli_error($verbindung));
echo 'Beschreibung ge&auml;ndert.';
}


//Allgemeiner Fall CHat Check
if($_POST["chatchange"]==1) {
if(isset($_POST["chatvar"])) {
	mysqli_query($verbindung, "UPDATE account SET chat=1 WHERE id='$ich->id'");
	$ich->chat=1;
	}
else
	{
	mysqli_query($verbindung, "UPDATE account SET chat=0 WHERE id='$ich->id'");
	$ich->chat=0;
	}
}
//Fall 3 Datei hochladen
        if($_POST["sent"]==3){
          if (isset ($_FILES['new_image'])){
              $imagename = $_FILES['new_image']['name'];
              $source = $_FILES['new_image']['tmp_name'];
              $target = "avatar/".$imagename;
              move_uploaded_file($source, $target);
 
              $imagepath = $imagename;
              $save = "avatar/" . $imagepath; //This is the new file you saving
              $file = "avatar/" . $imagepath; //This is the original file
  
              list($width, $height) = getimagesize($file) ;
 		if($width>80 && $width<3000 && $height<3000) { echo 'Bild zu gross, wenn JPEG dann transformiere auf richtige Gr&ouml;sse<br />';
              $modwidth = 150; 
 
              $diff = $width / $modwidth;
 
              $modheight = $height / $diff; 
              $tn = imagecreatetruecolor($modwidth, $modheight) ; 
              $image = imagecreatefromjpeg($file) ; 
              imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height) ; 
 
              imagejpeg($tn, $save, 100) ; 
 
              $save = "avatar/sml_" . $id . ".jpg" ; //This is the new file you saving
              $file = "avatar/" . $imagepath; //This is the original file
 
              list($width, $height) = getimagesize($file) ; 
 
              $modwidth = 80; 
 
              $diff = $width / $modwidth;
 
              $modheight = $height / $diff; 
              $tn = imagecreatetruecolor($modwidth, $modheight) ; 
              $image = imagecreatefromjpeg($file) ; 
              imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height) ; 
 
              imagejpeg($tn, $save, 100) ; 
		mysqli_query($verbindung, "UPDATE account SET bild='$save' WHERE id='$id'");
 			} else  { if($width<3000 && $height<3000) {
			$source=$file; $target2="avatar/sml_" . $id . ".jpg" ;
	              copy($target, $target2);
			echo 'Bild hochgeladen..<br />';
			mysqli_query($verbindung, "UPDATE account SET bild='$target2' WHERE id='$id'");
				} 
				}
          }
        }
//Fall 2 name aendern
if($_POST["sent"]==2) {
	$newname=pruefetext($_POST["nick"]);
	mysqli_query($verbindung, "UPDATE account SET nickname='$newname' WHERE id='$id'");
	echo 'Neuer Name wurde erfolgreich gesetzt!';
	echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=logout.php">';
}
//abarbeitung des Falls Sent 1
if($_POST["sent"]==1 && $_POST["pwchangepost"]==1)
	{
	$newpw=$_POST["newpw"];
	$newpw2=$_POST["newpwconfirm"];
	$oldpw=$_POST["oldpw"];
	if($oldpw==$_SESSION["Pwd"])
		if($newpw==$newpw2)
			{
			$newpw=md5($newpw);
			mysqli_query($verbindung, "UPDATE account SET passwort='$newpw' WHERE id='$id'");
			echo 'Neues Passwort wurde erfolgreich gesetzt!';
			echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=main.php">';
			}
		else	echo 'Das neue Passwort stimmt nicht mit der Best&auml;tigung &uuml;berein!';
	else echo 'Falsches altes Passwort angegeben!';
	}
//Ende

$accountvar=mysqli_query($verbindung, "SELECT * FROM account WHERE id='$id'");
while($accountfeld=mysqli_fetch_array($accountvar)) {
$name=$accountfeld["name"];
$nick=$accountfeld["displaynick"];
$img=$accountfeld["avatar"];
$beschreibung=$accountfeld["beschreibung"];
}
if($img=='') $img="siedler.gif";

?>
<h3>Optionen</h3>
<?php
echo '<div class="box" style="width:350px">';
echo '<table><tr><td>Accountname:</td><td>',$ich->login,'</td></tr>';
echo '<tr><td>Id</td><td>',$_SESSION["Id"],'</td></tr>';
echo '<tr><td>Name im Spiel:</td><td>',$ich->nickname,'</td></tr>';
echo '<tr><td>Avatar:</td><td><img src="',$ich->bild,'" border="0" /></td></tr>';
echo '</table></div><br /><hr /><br /><br />';
//Chat aktivieren
echo '<span style="text-decoration:underline;font-weight:bold;">Ingame-Chat Einstellungen</span><br /><br />';
echo '<form name="chatform" action="option.php" method="POST"><input type="hidden" name="chatchange" value="1" />';
echo '<div class="box" style="width:400px;"><table>';
echo '<tr><td>Chat aktivieren:</td><td><input type="checkbox" name="chatvar" onClick="document.chatform.submit();" ',$ich->chat==1?'checked="true"':'','></td></tr>';
echo '</table></div></form>';
//Ende
echo '<br /><span style="text-decoration:underline;font-weight:bold;">Passwort &auml;ndern</span><br /><br />';
echo '<form action="option.php" method="POST">';
echo '<div class="box" style="width:400px;"><table>';
//hiddensent
echo '<input type="hidden" name="pwchangepost" value="1" />';
echo '<tr><td>altes Passwort:</td><td><input type="password" name="oldpw"></td></tr>';
echo '<tr><td>neues Passwort:</td><td><input type="password" name="newpw"></td></tr>';
echo '<tr><td>best&auml;tige neues Passwort</td><td><input type="password" name="newpwconfirm"></td></tr>';
echo '</table></div>';
echo '<input type="hidden" name="sent" value="1"><input type="submit" value="speichern"></form>';
echo '<br /><span style="text-decoration:underline;font-weight:bold;">Spielername &auml;ndern</span><br /><br />';
echo '<form action="option.php" method="POST">';
echo '<div class="box" style="width:400px;"><table>';
echo '<tr><td>neuer Spielername</td><td><input type="text" name="nick"></td></tr></table>';
echo '</div><input type="hidden" name="sent" value="2"><input type="submit" value="speichern"></form>';
echo '<br />
<span style="text-decoration:underline;font-weight:bold;">Bilder hochladen:</span><br /><br />
<div class="box" style="width:400px;">
Entweder du l&auml;dst ein .jpg Bild hoch beliebiger Gr&ouml;sse, dieses wird dann auf die passende Gr&ouml;sse formatiert <br /><br /><span style="font-weight:bold;color:yellow;">ODER</span><br /><br />
du l&auml;dst ein Bild beliebigen Formats hoch, was maximal 80Pixel Breit ist.<br /><br />
<font color="red">Keine rassistischen, pornographischen oder sonstig anst&ouml;ssigen Bilder!</font>&nbsp;<form action="option.php" method="post" enctype="multipart/form-data" id="something" class="uniForm">
        <input name="new_image" id="new_image" size="30" type="file" class="fileUpload" />
	<input type="hidden" name="sent" value="3">
        <button name="submit" type="submit" class="submitButton">Upload/Resize Image</button>
</form>';
echo '</div><br /><br /><form action="option.php" method="post"><div class="box" style="width:400px;">';
echo '<h4>Spielerbeschreibung (<a href="gacode.htm">GA-Code</a>)</h4><textarea name="bla" rows="20" cols="50">',$beschreibung,'</textarea><input type="hidden" name="sent" value="4" /></div><input type="submit" value="eintragen" /></form>';
}
include("foot.php");
?>
