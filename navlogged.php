
<body onload="start();">


<script type="text/javascript" src="wz_tooltip.js"></script>

<ul id="menu">
  <li class="button ubersicht"><a href="main.php"><span>&Uuml;bersicht</span></a></li>
  <li class="button kolonien"><a href="planetchoice.php"><span>Kolonien</span></a></li>

  <li class="button schiffe"><a href="schiffchoice.php"><span>Schiffe</span></a></li>

  <li class="button flotten"><a href="flotte.php?fid=0"><span>Flotten</span></a></li>

  <li class="button kommunikation"><a href="kommunikation.php"><span>Kommunikation</span></a></li>
  <li class="button quests"><a href="showquest.php"><span>Quests</span></a></li>
  <li class="button datenbank"><a href="datenbank.php"><span>Datenbank</span></a></li>
  <li class="button hilfe"><a href="tickets.php"><span>Hilfe</span></a></li>

  <li class="button optionen"><a href="option.php"><span>Optionen</span></a></li>
  <li class="button logout"><a href="logout.php"><span>Logout</span></a></li>

  <?php if($_SESSION["Id"]<100 && $_SESSION["Id"]>0) echo '<li><a href="order.php">Schiffe beantragen</a></li>'; ?>
  <?php if($_SESSION["Id"]=='3') echo '<li><a href="schiffchoice.php?handel=1">Handelsschiffe anzeigen</a></li>'; ?>
  <?php echo '<li><b><span style="font-size:bigger;">',date("d.m.Y"),'</span><br /><span id="time"></span></b></li>'; ?>
</ul>
<?php
if(!isset($_SESSION["Id"])) die("Fehler: Session abgelaufen! Bitte neu <a href='index.php'>einloggen</a>"); 

?>
