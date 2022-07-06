@echo off
SET dim=100
SET /a itermax=dim/20
SET /a itermaxsquare = itermax * itermax 
SET /a upperbound = itermaxsquare - 1
FOR /L %%x in (0,1,%upperbound%) DO ^
echo bearbeite Sektor %%x & php.exe createBigMap.php %%x %dim%> minimap/bild%%x.png
echo setze Teile zusammen
php.exe assembleMapParts.php %dim%> minimap/minimap.png
