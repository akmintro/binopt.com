"use strict"; //All my JavaScript written in Strict Mode http://ecma262-5.com/ELS5_HTML.htm#Annex_C

(function () {
    // ======== private vars ========
	var socket;
	var xhttp;
	var startserveraddress = 'https://binopt.com/api/v1/websocket/check';

    ////////////////////////////////////////////////////////////////////////////
    var init = function () {

		wsserverrun();
		
		socket = new WebSocket("ws://127.0.0.1:8887");

		socket.onopen = connectionOpen; 
		socket.onmessage = messageReceived;
        document.getElementById("currency-select").onchange = function () {
            socket.send(this.value);
        };
    };


	function connectionOpen() {
	   socket.send("Connection with \""+document.getElementById("sock-addr").value+"\" Подключение установлено обоюдно, отлично!");
	}

	function messageReceived(e) {
        var result = JSON.parse(e.data);

        if(result["type"] == "current")
        	document.getElementById("sock-current").innerHTML = result["data"]["name"] + ": " + result["data"]["last"];
        else if(result["type"] == "history") {
            var text = "";
            var data = result["data"]["data"];
            for (var item in data) {
                text += data[item]["value"] + "<br>";
            }
            document.getElementById("sock-history").innerHTML = text;
        }
	}

    function connectionClose() {
        socket.close();
        document.getElementById("sock-info").innerHTML += "Соединение закрыто <br />";

    }

    var wsserverrun = function() {
        xhttp = new XMLHttpRequest();
        xhttp.open('GET',startserveraddress,false);
        xhttp.send();
    };

	return {
        ////////////////////////////////////////////////////////////////////////////
        // ---- onload event ----
        load : function () {
            window.addEventListener('load', function () {
                init();
            }, false);
        }
    }
})().load();
