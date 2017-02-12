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

        chart = Highcharts.chart('container', {
            chart: {/*
                //zoomType: 'x'
                events: {
                 load: function () {

                 // set up the updating of the chart each second
                 var series = this.series[0];
                 setInterval(function () {
                 var x = (new Date()).getTime(), // current time
                 y = Math.random();
                 series.addPoint([x, y], true, true);
                 }, 1000);
                 }
                 }*/
            },
            title: {
                text: 'USD to EUR exchange rate over time'
            },
            subtitle: {
                text: document.ontouchstart === undefined ?
                    'Click and drag in the plot area to zoom in' : 'Pinch the chart to zoom in'
            },
            xAxis: {
                type: 'datetime'
            },
            yAxis: {
                title: {
                    text: 'Exchange rate'
                }
            },
            legend: {
                enabled: false
            },
            plotOptions: {
                area: {
                    fillColor: {
                        linearGradient: {
                            x1: 0,
                            y1: 0,
                            x2: 0,
                            y2: 1
                        },
                        stops: [
                            [0, Highcharts.getOptions().colors[0]],
                            [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                        ]
                    },
                    marker: {
                        radius: 2
                    },
                    lineWidth: 1,
                    states: {
                        hover: {
                            lineWidth: 1
                        }
                    },
                    threshold: null
                }
            },

            series: [{
                type: 'area'
            }]
        });

        series = chart.series[0];
    };

	function messageReceived(e) {
        var result = JSON.parse(e.data);

        if(result["type"] == "current") {
            document.getElementById("sock-current").innerHTML = result["data"]["name"] + ": " + result["data"]["close"].toFixed(result["data"]["length"]);

            var time = Date.parse(result["time"] + " GMT")+tzoffset;


            if((time-1000) % 10000 == 0 || lastpoint == null) {
                series.addPoint([time, result["data"]["close"]], true, series.data.length > 360);
                lastpoint = series.data[series.data.length-1];
            }
            lastpoint.x = time;
            lastpoint.y = result["data"]["close"];
            lastpoint.update();
        }
        else if(result["type"] == "history") {

            var res = result["data"]["data"];

            var text = "[";
            var i = 0;
            for (var item in res) {
                if(i>0)
                    text += ",";
                text += "[" + (Date.parse(res[item]["currencytime"] + " GMT")+tzoffset) + "," + res[item]["close"] + "]";
                i++;
            }
            text += "]";

            var data = JSON.parse(text);

            series.setData(data);

            lastpoint = null;
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
                setChartNames(select);
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

	function setChartNames(select)
    {
        var name = select.options[select.selectedIndex].text;
        console.log(name);
        series.name = name;
        chart.setTitle({ text: name + ' exchange rate over time'});
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
