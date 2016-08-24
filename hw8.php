<!DOCTYPE html>
<html>
<head>
   <title>Stock Search</title>
   <meta charset="utf-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
   <script
			  src="https://code.jquery.com/jquery-2.2.2.js"
			  integrity="sha256-4/zUCqiq0kqxhZIyp4G0Gk+AOtCJsY1TA00k5ClsZYE="
			  crossorigin="anonymous">
	</script>
	<script
			  src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"
			  integrity="sha256-xNjb53/rY+WmG+4L6tTl9m6PpqknWZvRt0rO1SRnJzw="
			  crossorigin="anonymous">
	</script>
	<link rel="stylesheet" href="http://apps.bdimg.com/libs/jqueryui/1.10.4/css/jquery-ui.min.css">
	<script src="https://code.highcharts.com/stock/highstock.js"></script>
	<script src="https://code.highcharts.com/stock/modules/exporting.js"></script>
	<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
	<!--
    <link href="bootstrap-3.3.5-dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="bootstrap-3.3.5-dist/js/bootstrap.min.js"></script>
	-->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
	<script>
	var current_stock_table = 0;
	var current_stock_info = "";
	var chart_url = "";
	var symbol_name = "";
	//quote,for tab 1///////////////////////////////////////////////
	function Get_Quote()
	{
		symbol_name = $("#name")[0].value;
		if(symbol_name == "")
			return;
		Request_quote();
	}
	function From_favourite(ss)
	{
		symbol_name = ss;
		Request_quote();
	}
	function Request_quote()
	{
		$.ajax({
		url:"./server.php",
		data:{symbol:symbol_name, type:1},
		type:'GET',
		dataType:'json',
		cache:false,
		success:function(response, status, xhr)
		{
			//alert("success");
			var data = JSON.parse(response);
			show_current_stock(data);
		},
		error: function(xhr, status, error)
		{
			alert(error);
		},
		});
		
	}
	function Go_back()
	{
		$("#myCarousel").carousel(0);
		$("#myCarousel").carousel('pause');
	}
	function Next_page()
	{
		$("#myCarousel").carousel(1);
		$("#myCarousel").carousel('pause');
	}
	function show_current_stock(data)
	{
		current_stock_info = "";
		chart_url = "";
		if(current_stock_table == 1)
		{
			var t = document.getElementById("current_stock_table");
			document.getElementById("div_current_stock").removeChild(t);
			var i = document.getElementById("chart_img");
			document.getElementById("show_chart").removeChild(i);
		}
		var message = data.Message;
		if(message!=undefined)
		{
			//alert("not exist!");
			document.getElementById("error_info").innerHTML="Select a valid entry";
			current_stock_table = 0;
			document.getElementById("go_to_next").setAttribute("disabled","");
			return;
		}
		var pic1="http://cs-server.usc.edu:45678/hw/hw8/images/up.png";
		var pic2="http://cs-server.usc.edu:45678/hw/hw8/images/up.png";
		var name = data.Name;
		var symbol = data.Symbol;
		var last_price = "$ "+data.LastPrice.toFixed(2);
		
		var change = data.Change.toFixed(2);
		var change_percent = data.ChangePercent.toFixed(2);
		if(change_percent < 0)
			pic1="http://cs-server.usc.edu:45678/hw/hw8/images/down.png";
		else if(change_percent == 0)
			pic1="";
		change_percent = change+"("+change_percent+"%) ";
		
		var date = Get_date(data.Timestamp,1);
		var market_cap = Get_maketcap(data.MarketCap);
		var volume = data.Volume;
		var change_percentYTD = data.ChangePercentYTD.toFixed(2);
		if(change_percentYTD < 0)
			pic2 = "http://cs-server.usc.edu:45678/hw/hw8/images/down.png";
		else if(change_percentYTD == 0)
			pic2 = "";
		var changeYTD = data.ChangeYTD.toFixed(2)+"("+change_percentYTD+"%) ";
		var high = "$ "+data.High.toFixed(2);
		var low = "$ "+data.Low.toFixed(2);
		var open = "$ "+data.Open.toFixed(2);
		var title = new Array("Name", "Symbol", "Last Price", "Change(Change Percent)","Time and Date","Market Cap", "Volume","ChangeYTD(Change Percent YTD)","High Price","Low Price", "Opening Price");
		var content = new Array(name, symbol, last_price, change_percent, date, market_cap, volume, changeYTD, high, low, open);
		var table = document.createElement("table");
		var tbody = document.createElement("tbody");
		table.setAttribute("class","table table-striped")
		table.setAttribute("id","current_stock_table")
		
		current_stock_info = symbol+";"+name+";"+last_price+";"+change_percent+";"+pic1+";"+market_cap;
		var star_empty = true;
		var symbol_list = localStorage.getItem("favourite_list");
		if(symbol_list != null)
		{
			var list = symbol_list.split(",");
			for(var ii=0; ii < list.length; ii++)
			{
				if(symbol == list[ii])
				{
					star_empty = false;
					break;
				}
			}
		}
		var star = document.getElementById("star");
		if(star_empty == true)
		{
			star.setAttribute("class","glyphicon glyphicon-star-empty");
			star.setAttribute("style","");
		}
		else
		{
			star.setAttribute("class","glyphicon glyphicon-star");
			star.setAttribute("style","color:yellow");
		}
		for(var index = 0; index < 11; index++)
		{
			tr = document.createElement("tr");
			th = document.createElement("th");
			td = document.createElement("td");
			th.innerHTML = title[index];
			
			if(index == 3)
			{
				if(pic1 != "")
				{
					if(pic1 == "http://cs-server.usc.edu:45678/hw/hw8/images/up.png")
						td.setAttribute("style","color:green");
					else
						td.setAttribute("style","color:red");
					var img = document.createElement("img");
					img.setAttribute("src",pic1);
					img.setAttribute("height","20px");
					img.setAttribute("width","20px");
					td.innerHTML = content[index];
					td.appendChild(img);
				}
			}
			else if(index == 7)
			{
				if(pic2 != "")
				{
					if(pic2 == "http://cs-server.usc.edu:45678/hw/hw8/images/up.png")
						td.setAttribute("style","color:green");
					else
						td.setAttribute("style","color:red");
					var img = document.createElement("img");
					img.setAttribute("src",pic2);
					img.setAttribute("height","20px");
					img.setAttribute("width","20px");
					td.innerHTML = content[index];
					td.appendChild(img);
				}
			}
			else
				td.innerHTML = content[index];
			tr.appendChild(th);
			tr.appendChild(td);
			tbody.appendChild(tr);
		}
		table.appendChild(tbody);
		document.getElementById("div_current_stock").appendChild(table);
		
		chart_url = "http://chart.finance.yahoo.com/t?s="+symbol+"&lang=en-US&width=400&height=300";
		var chart_img = document.createElement("img");
		chart_img.setAttribute("src",chart_url);
		chart_img.setAttribute("id", "chart_img");
		chart_img.setAttribute("style","width:95%")
		document.getElementById("show_chart").appendChild(chart_img);
		
		var button = document.getElementById("go_to_next");
		button.removeAttribute("disabled");
		
		$("#myCarousel").carousel(1);
		$("#myCarousel").carousel('pause');
		$('#myTab a:first').tab('show');
		current_stock_table = 1;
	}
	function Get_maketcap(cap)
	{
		if(cap/1000000000 < 0)
		{
			cap = (cap/1000000).toFixed(2);
			cap += " Million"
		}
		else
		{
			cap = (cap/1000000000).toFixed(2);
			cap += " Billion"
		}
		return cap;
	}
	function Get_date(dd,type)
	{
		var d = new Date(dd);
		var month = [
		  "January", "February", "March",
		  "April", "May", "June", "July",
		  "August", "September", "October",
		  "November", "December"
		];
		var date = fix(d.getDate(),2);
		var mon =month[d.getMonth()];
		var year = d.getFullYear();
		var hour = fix(d.getHours(),2);
		var min = fix(d.getMinutes(),2);
		var sec = fix(d.getSeconds(),2);
		var other = hour>=12?"pm":"am";
		if(type == 1)
			return date+" "+mon+" "+year+" "+hour+":"+min+":"+sec+" "+other;
		else
			return date+" "+mon+" "+year+" "+hour+":"+min+":"+sec;
	}
	function fix(num, length) {
		return ('' + num).length < length ? ((new Array(length + 1)).join('0') + num).slice(-length) : '' + num;
	}
	function Clear_info()
	{
		document.getElementById("name").value="";
		document.getElementById("error_info").innerHTML="";
		document.getElementById("go_to_next").setAttribute("disabled","");
		$("#myCarousel").carousel(0);
		$("#myCarousel").carousel('pause');
	}

	//the following functions are for tab2 : historic charts///////////////////////////////////////////////
	function InteractiveChartApi()
	{	
		$.ajax({
			data: {symbol:symbol_name, type:2},
			url: "./server.php",
			dataType: "json",
			context: this,
			success: function(response){
				var json = JSON.parse(response);
				render(json);
			},
			error: function(response,txtStatus){
				alert(response+"  "+txtStatus);
				console.log(response,txtStatus)
			}
		});
	}
	function _fixDate(dateIn) {
		var dat = new Date(dateIn);
		return Date.UTC(dat.getFullYear(), dat.getMonth(), dat.getDate());
	};

	function _getOHLC(json) {
		var dates = json.Dates || [];
		var elements = json.Elements || [];
		var chartSeries = [];

		if (elements[0]){

			for (var i = 0, datLen = dates.length; i < datLen; i++) {
				var dat = _fixDate( dates[i] );
				var pointData = [
					dat,
					elements[0].DataSeries['open'].values[i],
					elements[0].DataSeries['high'].values[i],
					elements[0].DataSeries['low'].values[i],
					elements[0].DataSeries['close'].values[i]
				];
				chartSeries.push( pointData );
			};
		}
		return chartSeries;
	};

	function render(data) {
		var ohlc = _getOHLC(data);
		// create the chart
		$('#show_historic_charts').highcharts('StockChart', {  
			rangeSelector: {
				selected: 0,
				buttons: [{
					type: 'week',
					count: 1,
					text: '1w'
				}, 	{
					type: 'month',
					count: 1,
					text: '1m'
				}, {
					type: 'month',
					count: 3,
					text: '3m'
				}, {
					type: 'month',
					count: 6,
					text: '6m'
				}, {
					type: 'ytd',
					text: 'YTD'
				}, {
					type: 'year',
					count: 1,
					text: '1y'
				}, {
					type: 'all',
					text: 'All'
				}],
				inputEnabled:false
			},
			title: {
				text: symbol_name + ' Historical Price'
			},
			yAxis: [{
				title: {
					text: 'Stock Value'
				}
			}],
			series: [{
				type: 'area',
				name: symbol_name,
				data: ohlc,
				threshold : null,///////////////////////////////////////
				tooltip : {
						valueDecimals : 2},/////////////////////////////////////
				fillColor : {
						linearGradient : {
							x1: 0,
							y1: 0,
							x2: 0,
							y2: 1
						},
						stops : [
							[0, Highcharts.getOptions().colors[0]],
							[1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
						]
					}
			}],
				navigation: {
				buttonOptions: {
					enabled: false
				}
			}
		
		});
	};
	
	//the following functions are for tab 3: Being News
	
	function Get_news(){
	//var name_symbol= $("#name")[0].value;
		$.ajax({
			url: "./server.php",
			data:{symbol:symbol_name, type:4},
			dataType: "json",
			type: "GET",
			success: function(response, status, xhr){
				//alert("news success");
				var data = JSON.parse(response);
				show_news(data);
			},
			error: function(response,txtStatus){
				alert(response);
			}
		});
	}
	var has_news = 0;
	function show_news(data)
	{
		if(has_news == 1)
		{
			var old_news_group = document.getElementById("news_group");
			document.getElementById("news").removeChild(old_news_group);
			has_news = 0;
		}
		var result = data.d.results;
		if(result.length == 0)
		{
			alert("empty");
			return;
		}
		var length = result.length;
		var news_group = document.createElement("div");
		news_group.setAttribute("class","col-md-12 col-xs-12");
		news_group.setAttribute("id","news_group");
		news_group.innerHTML="<hr />"
		if(length != 0)
			has_news = 1;
		for(var i = 0; i < length; i++)
		{
			var A_news = document.createElement("div");
			A_news.setAttribute("class","well col-md-12");
			var title = result[i].Title;
			var url = result[i].Url;
			var content = result[i].Description;
			var publisher = result[i].Source;
			var publishedDate = Get_date(result[i].Date,2);
			//var publishedDate = result[i].publishedDate;
			var title_ = document.createElement("p");
			title_.setAttribute("class","text-primary");
			title_.setAttribute("style","font-weight:bold");
			title_.innerHTML = "<a href="+url+" target='_blank'>"+title+"</a>";
			
			var content_ = document.createElement("p");
			content_.innerHTML = content;
			
			var publisher_ = document.createElement("b");
			publisher_.innerHTML = "Publisher: "+publisher+"<br/>";
			
			var publishedDate_ = document.createElement("b");
			publishedDate_.innerHTML = "Date: "+publishedDate+"<br/><p>&nbsp;</p>";
		
			A_news.appendChild(title_);
			A_news.appendChild(content_);
			A_news.appendChild(publisher_);
			A_news.appendChild(publishedDate_);
			news_group.appendChild(A_news);
		}
		if(has_news == 1)
			document.getElementById("news").appendChild(news_group);
	}
	
	//the following functions are for Favourite List
	function favourite()
	{
		var star = document.getElementById("star");
		var classname = star.getAttribute("class");
		if(classname == "glyphicon glyphicon-star")
		{
			star.setAttribute("class","glyphicon glyphicon-star-empty");
			star.setAttribute("style","");
			var tmp = current_stock_info.split(";");
			var id_ = tmp[0];
			Remove_from_table(id_);
		}
		else
		{
			star.setAttribute("class","glyphicon glyphicon-star");
			star.setAttribute("style","color:yellow");
			var tmp = current_stock_info.split(";");
			var kk = tmp[0];
			var symbol_list = new Array(localStorage.getItem("favourite_list"));
			if(symbol_list[0] == null)
			{
				symbol_list = new Array();
				symbol_list.push(kk);
			}
			else
				symbol_list.push(kk);
			localStorage.setItem("favourite_list",symbol_list);
			Add_to_list(current_stock_info);
		}	
	}
	function Add_to_list(info)
	{
		var summary = info.split(";");
		var tr = document.createElement("tr");
		tr.setAttribute("id","favourite_"+summary[0]);
		for(var i = 0; i < summary.length; i++)
		{
			if(i == 4)
				continue;
			var td = document.createElement("td");
			if(i == 0)
			{
				//td.setAttribute("class","text-primary");
				var p = document.createElement("p");
				p.innerHTML = summary[0];
				p.setAttribute("class","text-primary");
				p.setAttribute("onclick","From_favourite('"+summary[0]+"')");
				p.setAttribute("style","cursor:pointer");
				td.appendChild(p);
				tr.appendChild(td);
				continue;
			}	
			td.innerHTML = summary[i];
			if(i == 3)
			{
				var pic = summary[4];
				var img = document.createElement("img");
				if(pic == "http://cs-server.usc.edu:45678/hw/hw8/images/up.png")
				{
					td.setAttribute("style","color:green");
				}
				else
				{
					td.setAttribute("style","color:red");
				}
				img.setAttribute("src",pic);
				img.setAttribute("height","20px");
				img.setAttribute("width","20px");
				td.appendChild(img);
			}
			tr.appendChild(td);
		}
		var delete_button = document.createElement("button");
		var span = document.createElement("span");
		span.setAttribute("class","glyphicon glyphicon-trash");
		delete_button.appendChild(span);
		delete_button.setAttribute("class","btn btn-default");
		delete_button.setAttribute("id","delete_facourite_"+summary[0]);
		delete_button.setAttribute("onclick","Delete_from_list(this.id)");
		var last_one = document.createElement("td");
		last_one.appendChild(delete_button);
		tr.appendChild(last_one);
		
		document.getElementById("favourite_list").appendChild(tr);
	}
	function Delete_from_list(id_)
	{
		Remove_from_table(id_.substr(17));
	}
	function Remove_from_table(id_)
	{
		var symbol_list = localStorage.getItem("favourite_list");
		var list = symbol_list.split(",");
		for(var i = 0 ; i < list.length; i++)
		{
			if(list[i] == id_)
			{
				list.splice(i,1);
				break;
			}
		}
		localStorage.setItem("favourite_list", list);
		var tr_id = "favourite_"+id_;
		var tr = document.getElementById(tr_id);
		document.getElementById("favourite_list").removeChild(tr);
	}
	</script>
	
	<script>
	// the following functions are for autocomplete
	$(function(){
			var symbol_list = localStorage.getItem("favourite_list");
			if(symbol_list != null)
			{
				var list = symbol_list.split(",");
				if(list[0] == "")
				{
					list.splice(0,1);
					localStorage.setItem("favourite_list", list);
				}
				update_data(list, 2);
			}

			$("#name").autocomplete({
				source:function(request, response)
				{
					$.ajax({
						url: "./server.php",
						dataType: "json",
						data: {
							symbol: request.term,
							type: 3
						},
						success: function(raw_data) {
							var data = JSON.parse(raw_data);
							var list = Process_auto(data);
							response(list);
						}
					});
				},
				select: function(event, ui){
				$(this).value = ui.item;
				}
		});	
		/*
		$("#request_form").submit(function(event){
		  event.preventDefault();
		  Get_Quote();
		});
		*/
		function Process_auto(data)
		{
		  var array = new Array();
		  for(i=0;i<data.length;i++)
		  {
			var str = data[i].Symbol+' - '+data[i].Name+' ('+data[i].Exchange+')';
			var arr= {label:str,value:data[i].Symbol};
			array.push(arr);
		  }
		  return array;
		}

	
	//the following is for Facebook sharing/////////////////////
	$('#share_button').click(function(e){
	//alert("here");
	var tmp = current_stock_info.split(";");
	e.preventDefault();
	FB.ui(
	{
		method: 'feed',
		name: 'Current Stock Price of '+tmp[1]+' is '+tmp[2],
		picture: chart_url,
		caption: 'LAST TRADE PRICE:'+tmp[2]+', CHANGE: '+tmp[3],
		description: 'Stock Information of '+tmp[1]+'('+tmp[0]+')',
		message: ''
	},
	function(response) {
		if (response && !response.error_message) {
		  alert('Posted Successfully');
		} else {
		  alert('Not Posted');
		}
	}
	);
  
	});
	
	//the following is for refresh
	$('#auto_refresh').change(function(){automatic_refresh()});
	});
	var running = 0;
	var handler = 0;
	function automatic_refresh()
	{
		var box = document.getElementById("auto_refresh");
		if(box.checked)
		{
			running = 1;
			handler = setInterval("update_list()", 5000);
		}	
		else
		{
			if(running != 0)
			{
				clearInterval(handler);
				running = 0;
			}
		}
			
	}
	function update_list()
	{
		var tbody = document.getElementById("favourite_list");
		var rows = tbody.getElementsByTagName("tr");
		if(rows.length == 1)
			return;
		var symbol_array = new Array();
		for(var i = 1; i < rows.length; i++)
		{
			var name = rows[i].getElementsByTagName("td")[0].getElementsByTagName("p")[0].innerHTML;
			symbol_array.push(name);
		}
		update_data(symbol_array, 1);
	}
	function update_data(arr, type)
	{
		for(var i = 0; i < arr.length; i++)
		{
			var name = arr[i];
			$.ajax({
			url:"./server.php",
			data:{symbol:name, type:1},
			type:'GET',
			dataType:'json',
			cache:false,
			success:function(response, status, xhr)
			{
				//alert("success");
				var data = JSON.parse(response);
				update_table(data, type);
			},
			error: function(xhr, status, error)
			{
				alert(error);
			},
			});
		}
	}
	function update_table(data, type)
	{
		var pic="http://cs-server.usc.edu:45678/hw/hw8/images/up.png";
		var name = data.Name;
		var symbol = data.Symbol;
		var last_price = "$ "+data.LastPrice.toFixed(2);
		//var last_price = name+" lala";
		var change = data.Change.toFixed(2);
		var change_percent = data.ChangePercent.toFixed(2);
		if(change_percent < 0)
			pic="http://cs-server.usc.edu:45678/hw/hw8/images/down.png";
		else if(change_percent == 0)
			pic="";
		change_percent = change+"("+change_percent+"%) ";
		var market_cap = Get_maketcap(data.MarketCap);
		
		current_stock_info = symbol+";"+name+";"+last_price+";"+change_percent+";"+pic+";"+market_cap;
		//localStorage.setItem(symbol, current_stock_info);
		if(type == 2)
		{
			Add_to_list(current_stock_info);
			return;
		}
		var rows = document.getElementById("favourite_list").getElementsByTagName("tr");
		var that_row = "";
		for(var ii = 1; ii < rows.length; ii++)
		{
			if(symbol == rows[ii].getElementsByTagName('td')[0].getElementsByTagName("p")[0].innerHTML)
			{
				that_row = rows[ii].getElementsByTagName('td');
				break;
			}
		}
		that_row[2].innerHTML = last_price;////////////////////////////////
		that_row[3].innerHTML = change_percent;
		var img = document.createElement("img");
		if(pic == "http://cs-server.usc.edu:45678/hw/hw8/images/up.png")
		{
			that_row[3].setAttribute("style","color:green");
		}
		else
		{
			that_row[3].setAttribute("style","color:red");
		}
		img.setAttribute("src",pic);
		img.setAttribute("height","20px");
		img.setAttribute("width","20px");
		that_row[3].appendChild(img);
		
	}
	</script>
	
	<style type="text/css">
	@media screen and (max-width:540px){.hidden_widget{display:none} #main_frame{margin-left:10px; margin-right:10px}} 
	@media screen and (min-width:540px){.hidden_widget{display:inline}}
	</style>
</head>
<body style="background-image:url('http://3.bp.blogspot.com/-DLTc1HpdEA8/Velb82V9KeI/AAAAAAAATLI/_DZcc5t2FXM/s1600/monotype-print-wall-color-texture-blue-background-hd-wallpaper.jpg')">
<!-- the following is for Facebook initialization -->

	<div id="fb-root"></div>
	<script>
	  window.fbAsyncInit = function() {
		FB.init({
		  appId      : '565719906939639',
		  xfbml      : true,
		  version    : 'v2.5'
		});
	  };
	  (function() {
		var e = document.createElement('script'); e.async = true;
		e.src = document.location.protocol +
		'//connect.facebook.net/en_US/all.js';
		document.getElementById('fb-root').appendChild(e);
		}());
	</script>
	
<!--   ---------------------------------------------------      -->

	<div class="container" id = "main_frame" style="padding:0px">

	<div class="container" style="background-color:white; color:#000; margin-top:5px;border-radius:10px">
	<h4 class="text-center" style="font-family:arial;margin-top:30px">Stock Market Search</h4>
	
	<div class="row">
		<form class="form-inline" role="form" id="request_form" action="javascript:;">
		<div class="col-md-3 col-ms-12">
			<div class="form-group">
				<b class="text-center">Enter the stock name or symbol:<span style="color:red">*</span></b>
			</div>
		</div>
		<div class="col-md-6 col-xs-12">
			<div class="form-group" style="width:100%">
				<input type="text" class="form-control" id="name" placeholder="Apple Inc or Apple" style="width:100%" required>
			</div>	
			<div>
				<br/>
				<p id="error_info" style="color:red"></p>
			</div>
		</div>
		
		<div class="col-md-3 col-xs-12">
			<div class="form-group">
				<button  type="submit" class="btn btn-primary" onclick="Get_Quote()"><span class="glyphicon glyphicon-search"></span> Get Quote</button>
				<button  class="btn btn-default"  onclick="Clear_info()"><span class="glyphicon glyphicon-refresh"></span> Clear</button>
			</div>
			<div>
			<br />
			<b>Powered By: <a href="http://dev.markitondemand.com/MODApis/" target="_blank"><img src="http://cs-server.usc.edu:45678/hw/hw8/images/mod-logo.png" width="100px" height="30px"></a></b>
			<p style="margin:0px">&nbsp</p>
			</div>
		</div>
		</form>
	</div>
	
	</div>

	<hr />
	
	<div class="container" style="background-color:white; color:#000;border-radius:10px">
	
	<div id="myCarousel" class="carousel slide " data-interval="false">
	<!-- 轮播（Carousel）项目 -->
    <div class="carousel-inner">
      <div class="item active">
		<div class="panel panel-default" style="margin-top:20px;">
			<div class="panel-heading" style="padding-bottom: 5px;">
				<div class="row">
					<div class="col-md-2 col-xs-5">
						<b class="panel-title" style="font-size:14px">Favourite List</b>
					</div>
					<div class="col-md-5 col-xs-0">
					</div>
					<div class="col-md-5 col-xs-7" style="text-align:right">
					<span style="width:100%">
						<span class="hidden_widget" style="font-size:13px">Automatic Refresh:</span>
						<input id="auto_refresh" data-toggle="toggle" type="checkbox" title="refresh every 5 seconds">
						<button class="btn btn-default" data-toggle="tooltip"  onclick="update_list()" title="refresh manually"><span class="glyphicon glyphicon-refresh"></span></button>
						<button  id="go_to_next" data-toggle="tooltip" title="stock info" disabled class="btn btn-default" onclick="Next_page()"><span class="glyphicon glyphicon-chevron-right"></span></button>
					</span>
					</div>
					<script>
							$(function () { $("[data-toggle='tooltip']").tooltip(); });
					</script>
				</div>
				
			</div>
			<div class="panel-body">
				<div class="table-responsive" style="border:none">
				<table class="table">
				   <tbody id="favourite_list">
					  <tr>
						 <th>Symbol</th>
						 <th>Company Name</th>
						 <th>Stock Price</th>
						 <th>Change(Change Percent)</th>
						 <th>Market Cap</th>
						 <th>&nbsp;&nbsp;</th>
					  </tr>
					  
				   </tbody>
				</table>
				</div>
			</div>
		</div>
      </div>
	  
      <div class="item">
			<div class="panel panel-default" style="margin-top:20px;">
				<div class="panel-heading" >
					<div class="row">
					<div class="col-md-1 col-xs-2">
					<button  style="text-align:left" class="btn btn-default" onclick="Go_back()"><span class="glyphicon glyphicon-chevron-left"></span></button>
					</div>
					<div class="col-md-10 col-xs-8" style="text-align:center">
					<b class="panel-title" style="font-size:14px;">Stock Details</b>
					</div><div class="col-md-1 col-xs-2"></div>
					</div>
				</div>
				
				<div class="panel-body">
					<div class="col-md-12 col-xs-12" style="padding:0px">
					<div class="col-md-12 col-xs-12" style="padding:0px">
					<ul id="myTab" class="nav nav-pills">
						<li class="active">
							<a href="#current_chart" data-toggle="tab">
								<span><span class="glyphicon glyphicon-dashboard"></span> <span class="hidden_widget">Current</span> Stock</span>
							</a>
						</li>
						<li>
							<a href="#historic_charts" data-toggle="tab" onclick="InteractiveChartApi()">
								<span class="glyphicon glyphicon-stats"></span> <span class="hidden_widget">Historic</span> Charts
							</a>
						</li>
						<li>
							<a href="#news" data-toggle="tab" onclick="Get_news()">
								<span class="glyphicon glyphicon-link"></span> News <span class="hidden_widget">Feeds</span>
							</a>
						</li>
					</ul>
					</div>
					<div id="myTabContent" class="tab-content">
						<div class="tab-pane fade in active" id="current_chart">
							<div class="col-md-12 col-xs-12" style="padding:0px">
								<div class="col-md-2 col-xs-6">
								<p>&nbsp</p>
								<b>Stock Details</b>
								</div>
								<div class="col-md-6 col-xs-0"></div>
								<div class="col-md-4 col-xs-6" id="facebook" style="text-align:right">	
									<img src="https://cdn1.iconfinder.com/data/icons/logotypes/32/square-facebook-512.png" height=44px id="share_button"  style="cursor:pointer"/>
									<button class="btn btn-default btn-lg"  onclick="favourite()"><span id="star" class="glyphicon glyphicon-star-empty"></span></button>
								</div>
							</div>
							
							<div class="col-md-6 col-xs-12">
								<div id="div_current_stock"></div>
							</div>
							
							
							<div class="col-md-6 col-xs-12">
								<div id="show_chart" style="width:100%; text-align:center">
								</div>
							</div>
						</div>
						
						<div class="tab-pane fade" id="historic_charts">
							<div id="show_historic_charts"></div>
						</div>
						
						<div class="tab-pane fade" id="news">
							
						</div>
					</div>
					
				</div>
			</div>
      </div>
   </div>
   
   
   
	</div>
	</div>
</body>
</html>