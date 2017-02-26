"use strict"; //All my JavaScript written in Strict Mode http://ecma262-5.com/ELS5_HTML.htm#Annex_C

(function () {
    // ======== private vars ========
	var socket;
	var xhttp;
	var startserveraddress = 'http://binopt.com/api/v1/websocket/start';
    var instrumentsaddress = 'http://binopt.com/api/v1/instruments';
    var investsaddress = 'http://binopt.com/api/v1/invests';
    var bettsaddress = 'http://binopt.com/api/v1/bets';
    var chart;
    var series;
    var lastpoint = null;
    var tzoffset = -new Date().getTimezoneOffset()*60000;

    ////////////////////////////////////////////////////////////////////////////
    var init = function () {
		wsserverrun();
		
		socket = new WebSocket("ws://127.0.0.1:8887");
		socket.onmessage = messageReceived;

        document.getElementById("currency-select").onchange = function () {
            socket.send(this.value);

            setChartNames(this);
        };

        setInstruments();
        setInvests();

        document.getElementById("high-button").onclick = function() {
            betfunction(1);
        }

        document.getElementById("low-button").onclick = function() {
            betfunction(0);
        }
    };

	function messageReceived(e) {
        var result = JSON.parse(e.data);

        if(result["type"] == "current") {
            document.getElementById("sock-current").innerHTML = result["data"]["name"] + ": " + result["data"]["close"].toFixed(result["data"]["length"]);

            var data = [{Close: 120.0, High: 125.0, Low: 110.0, Open: 118.0, Date: "2016-12-15"}];
        }
        else if(result["type"] == "history") {
            ///
        }
	}

    var wsserverrun = function() {
        xhttp = new XMLHttpRequest();
        xhttp.open('GET',startserveraddress,false);
        xhttp.send();
    };

	function setInstruments() {
        var xhttp = new XMLHttpRequest();
        xhttp.open('GET', instrumentsaddress, true);
        xhttp.send();
        xhttp.onreadystatechange = function () {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                var data = JSON.parse(xhttp.responseText)["data"];
                var select = document.getElementById('currency-select');

                for (var item in data) {
                    var opt = document.createElement('option');
                    opt.value = data[item]["id"];
                    opt.innerHTML = data[item]["name"];
                    select.appendChild(opt);
                }
            }
        }
    }

    function setInvests() {
        var xhttp = new XMLHttpRequest();
        xhttp.open('GET', investsaddress, true);
        xhttp.send();

        xhttp.onreadystatechange = function () {
            if (xhttp.readyState == 4 && xhttp.status == 200) {
                var data = JSON.parse(xhttp.responseText)["data"];
                var select = document.getElementById('invest-select');

                for (var item in data) {
                    var opt = document.createElement('option');
                    opt.value = data[item]["id"];
                    opt.innerHTML = data[item]["size"];
                    select.appendChild(opt);
                }
            }
        }
    }

    function betfunction(updown) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", bettsaddress, true);/*
         xhr.setRequestHeader("Content-type", "application/json");*/
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var json = JSON.parse(xhr.responseText);
                document.getElementById("bet-result").innerHTML = json["meta"]["message"];
            }
        }

        var instrument = document.getElementById("currency-select");
        var invest = document.getElementById("invest-select");
        var data = JSON.stringify([
            {
                "account": 2,
                "invest": invest.options[invest.selectedIndex].value,
                "instrument": instrument.options[instrument.selectedIndex].value,
                "updown": updown
            }
        ]);
        xhr.send(data);
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
