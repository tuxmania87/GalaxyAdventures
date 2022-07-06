<?php
include("head.php");
if($_SESSION["Id"]>0)
include("navlogged.php");
else
include("nav.php");
?>
<h3>Mitarbeiter</h3>
<table class="bordered2">
<tr><td width="250px">Administrator</td><td>Cremetorte</td></tr>
<tr><td width="250px">Grafiker</td><td>-PDHoen-</td></tr>
<tr><td width="250px">Mapper</td><td>-PDHoen-</td></tr>
<tr><td width="250px">Mapper / Wiki</td><td>^Laisa^</td></tr>
<tr><td width="250px">(ex) Wiki</td><td>Keval</td></tr>
</table>
<h3>Mein Dank geht auch an:</h3>
<table class="bordered2">
<tr><td width="250px">Helferlein</td><td>Hindukusch</td></tr>
<tr><td width="250px">Helferlein</td><td>Nine</td></tr>
<tr><td width="250px">Helferlein</td><td>Wolf</td></tr>
</table>

<br />
<?php
include("foot.php");
?>
