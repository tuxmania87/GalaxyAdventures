<html>
<head>
 <title> Star Trek - Galaxy Adventures II </title>

<script type="text/javascript">
Normal1 = new Image();
Normal1.src = "ubersicht.png";
Highlight1 = new Image();
Highlight1.src = "ubersicht1.png";

Normal2 = new Image();
Normal2.src = "kolonien.png";
Highlight2 = new Image();
Highlight2.src = "kolonien1.png";

Normal3 = new Image();
Normal3.src = "schiffe.png";
Highlight3 = new Image();
Highlight3.src = "schiffe1.png";

Normal4 = new Image();
Normal4.src = "flotten.png";
Highlight4 = new Image();
Highlight4.src = "flotten1.png";

Normal5 = new Image();
Normal5.src = "kommunikation.png";
Highlight5 = new Image();
Highlight5.src = "kommunikation1.png";

Normal6 = new Image();
Normal6.src = "datenbank.png";
Highlight6 = new Image();
Highlight6.src = "datenbank1.png";

Normal7 = new Image();
Normal7.src = "hilfe.png";
Highlight7 = new Image();
Highlight7.src = "hilfe1.png";

Normal8 = new Image();
Normal8.src = "optionen.png";
Highlight8 = new Image();
Highlight8.src = "optionen1.png";

Normal9 = new Image();
Normal9.src = "logout.png";
Highlight9 = new Image();
Highlight9.src = "logout1.png";

function Bildwechsel (Bildnr, Bildobjekt) {
  window.document.images[Bildnr].src = Bildobjekt.src;
}
</script>

<style type="text/css">
body {font-size:12;
    font-family:"Arial";}
td          {font-size:12;}
</style>
</head>

<body style="background-image:url(ground.jpg); background-repeat:no-repeat; background-position:left,top" bgcolor="black" text="#cccccc" Marginheight="0" Marginwidth="0" topMargin="0" leftMargin="0">


<div style="position:absolute; top:180px; left:24px;">
<a href="" target="game" onmouseover="Bildwechsel(0, Highlight1)" onmouseout="Bildwechsel(0, Normal1)">
<img src="ubersicht.png" border="0">
</a>
</div>

<div style="position:absolute; top:200px; left:24px;">
<a href="" target="game" onmouseover="Bildwechsel(1, Highlight2)" onmouseout="Bildwechsel(1, Normal2)">
<img src="kolonien.png" border="0">
</a>
</div>

<div style="position:absolute; top:220px; left:24px;">

<a href="" target="game" onmouseover="Bildwechsel(2, Highlight3)" onmouseout="Bildwechsel(2, Normal3)">
<img src="schiffe.png" border="0">
</a>
</div>

<div style="position:absolute; top:260px; left:24px;">
<a href="" target="game" onmouseover="Bildwechsel(3, Highlight4)" onmouseout="Bildwechsel(3, Normal4)">
<img src="flotten.png" border="0">
</a>
</div>

<div style="position:absolute; top:300px; left:24px;">
<a href="" target="game" onmouseover="Bildwechsel(4, Highlight5)" onmouseout="Bildwechsel(4, Normal5)">
<img src="kommunikation.png" border="0">
</a>
</div>

<div style="position:absolute; top:320px; left:24px;">
<a href="" target="game" onmouseover="Bildwechsel(5, Highlight6)" onmouseout="Bildwechsel(5, Normal6)">
<img src="datenbank.png" border="0">
</a>
</div>

<div style="position:absolute; top:340px; left:24px;">
<a href="" target="game" onmouseover="Bildwechsel(6, Highlight7)" onmouseout="Bildwechsel(6, Normal7)">
<img src="hilfe.png" border="0">
</a>
</div>

<div style="position:absolute; top:380px; left:24px;">
<a href="" target="game" onmouseover="Bildwechsel(7, Highlight8)" onmouseout="Bildwechsel(7, Normal8)">
<img src="optionen.png" border="0">
</a>
</div>

<div style="position:absolute; top:400px; left:24px;">
<a href="" target="game" onmouseover="Bildwechsel(8, Highlight9)" onmouseout="Bildwechsel(8, Normal9)">
<img src="logout.png" border="0">
</a>
</div>
<div id="main">
MAIN
</div>



</body>
</html>