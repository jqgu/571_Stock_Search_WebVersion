<?php
if (isset($_GET['symbol'])) { 
	$type = $_GET['type'];
	$symbol = $_GET['symbol'];
	if($type == 1)
	{
		$url="http://dev.markitondemand.com/MODApis/Api/v2/Quote/json?symbol=".$symbol;
		$file = file_get_contents($url);
		$json_file = json_encode($file);
		echo $json_file;
	}
	else if($type == 2)
	{	
		$url = "http://dev.markitondemand.com/Api/v2/InteractiveChart/json?parameters={Normalized:false,NumberOfDays:1095,DataPeriod:'Day',Elements:[{Symbol:'".$symbol."',Type:'price',Params:['ohlc']}]}";
		$file = file_get_contents($url);
		$chart = json_encode($file);
		echo $chart;
	}
	else if($type == 3)
	{
		$url="http://dev.markitondemand.com/MODApis/Api/v2/Lookup/json?input=".$symbol;
		$file = file_get_contents($url);
		$auto_file = json_encode($file);
		echo $auto_file;
	}
	else if($type == 4)
	{
		$accountKey = 'gygmtUaTvUBHjZZliwm79DMI7CVcwxOCTo/CbwVcjew';          
		$ServiceRootURL =  'https://api.datamarket.azure.com/Bing/Search/';		
		$WebSearchURL = $ServiceRootURL . 'v1/News?$format=json&Query=';		
		$context = stream_context_create(array(
			'http' => array(
				'request_fulluri' => true,
				'header'  => "Authorization: Basic " . base64_encode($accountKey . ":" . $accountKey)
			)
		));
		$request = $WebSearchURL . urlencode("'{$symbol}'"); 	
		$response = file_get_contents($request, 0, $context);
		$jsonobj = json_encode($response);///////////
		echo $jsonobj;
	}
}
?>

