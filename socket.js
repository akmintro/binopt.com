"use strict"; //All my JavaScript written in Strict Mode http://ecma262-5.com/ELS5_HTML.htm#Annex_C

(function () {
    // ======== private vars ========
	var socket;
	var xhttp;
	var startserveraddress = 'http://binopt.com/api/v1/websocket/check';
    var instrumentsaddress = 'http://binopt.com/api/v1/instruments';

    ////////////////////////////////////////////////////////////////////////////
    var init = function () {

		wsserverrun();
		
		socket = new WebSocket("ws://127.0.0.1:8887");

		socket.onopen = connectionOpen; 
		socket.onmessage = messageReceived;
        document.getElementById("currency-select").onchange = function () {
            socket.send(this.value);
        };

        getOptions();
    };


	function connectionOpen() {
	   socket.send("Connection with \""+document.getElementById("sock-addr").value+"\" Подключение установлено обоюдно, отлично!");
	}

	function messageReceived(e) {
        var result = JSON.parse(e.data);

        if(result["type"] == "current")
        	document.getElementById("sock-current").innerHTML = result["data"]["name"] + ": " + result["data"]["last"].toFixed(result["data"]["length"]);
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

	function getOptions(){
        xhttp = new XMLHttpRequest();
        xhttp.open('GET',instrumentsaddress,true);
        xhttp.send();
        xhttp.onreadystatechange = function () {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                var data = JSON.parse(xhttp.responseText)["data"];
                var select = document.getElementById('currency-select');

                for(var item in data)
                {
                    var opt = document.createElement('option');
                    opt.value = data[item]["id"];
                    opt.innerHTML = data[item]["name"];
                    select.appendChild(opt);
                }
            }
        }
    }

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
