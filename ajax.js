function kdown(evt)
		{
			var keyCode = 
			    document.layers ? evt.which :
			    document.all ? event.keyCode :
			    document.getElementById ? evt.keyCode : 0;

			if(keyCode == 13)
			{
				sendchat();
			}
		}


// Request senden
function setRequest(uri1) {
	// Request erzeugen
	if (window.XMLHttpRequest) {
		request = new XMLHttpRequest(); // Mozilla, Safari, Opera
	} else if (window.ActiveXObject) {
		try {
			request = new ActiveXObject('Msxml2.XMLHTTP'); // IE 5
		} catch (e) {
			try {
				request = new ActiveXObject('Microsoft.XMLHTTP'); // IE 6
			} catch (e) {}
		}
	}

	// überprüfen, ob Request erzeugt wurde
	if (!request) {
		alert("Kann keine XMLHTTP-Instanz erzeugen");
		return false;
	} else {
		//var url = "chatq.php";
		var url = uri1;
		// Request öffnen
		request.open('GET', url, true);
		// Request senden
		request.send(null);
		// Request auswerten
		if(url=="chat_query.php") request.onreadystatechange = interpretRequest;
	} 
}

// Request auswerten
function interpretRequest() {
	switch (request.readyState) {
		// wenn der readyState 4 und der request.status 200 ist, dann ist alles korrekt gelaufen
		case 4:
			if (request.status != 200) {
				//alert("Der Request wurde abgeschlossen, ist aber nicht OK\nFehler:"+request.status);
			} else {
				var content = request.responseText;
				// den Inhalt des Requests in das <div> schreiben
				document.getElementById('chatid').innerHTML = content;
			}
			break;
		default:
			break;
	}
}

