<?php
/**
 * Copyright (c) 2013 Puvanenthiran Subbaraj
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

session_start();
require './utils/Common.php';
//Checking if orderid and mobileno. are there in GET parameters
if (isset($_POST['orderId']) && isset($_POST['mobileno']))
{
	$orderId = $_POST['orderId'];
	$mobileno = $_POST['mobileno'];
	
	//Reading the values from ini file into array
	$ini_array = parse_ini_file('config/payments.ini', true);
	
	//Getting the list of shops in order made
	$result = getShopsOfOrder($orderId);
	$shopList = json_decode($result, true);
	
	//Looping through each shop
	if (count($shopList))
	{
		$shop = $shopList[0];
		
		//generating json data for each shop
		$shopId = $shop['shop_id'];

		$inputJsonArray = array (
			"authentication" => getAuthentication($ini_array),			
			"payee" => getIVRPayeeDetails($shop,$ini_array),
			"payer" => array (
				"funding_instrument" => array (
					"mobile" => $mobileno 				
					)
				),			
			"items" => getItemDetailsForJson($orderId, $shopId, $ini_array['general']['tax_percent']),
			"transaction" => getTransactionDetails($ini_array['general']['currency'])
		);
		
			
		$inputJson = json_encode($inputJsonArray);
		$http = "http";
		if (isset($_SERVER['HTTPS']))
			$http = "https";
		$hostName = $_SERVER['HTTP_HOST'];
		$libraryUrl = "$http://$hostName/" . $ini_array['general']['pmt_library_url']."/checkout";
		$header = "method: ivr";
		
		// Sending request to ivr pay api
		$result = runCurlPost($libraryUrl, $header, $inputJson);			
		$result = json_decode($result, true);
		
		addVoicePayCode($orderId, $result["data"]["secret_code"]);
	}
}
//checking if orderid and voiceconfcode(secret_code) are there in get parameters
else if(isset($_POST['orderId']) && isset($_POST['vpCode']))
{	
	$vpCode = $_POST['vpCode'];
	$orderId = $_POST['orderId'];

	$result = getVoicePayCode($orderId);
	$result = json_decode($result, true);
	$codeFromDB = $result['code'];
	
	//Reading the values from ini file into array
	$ini_array = parse_ini_file('config/payments.ini', true);

	//getting the shop list in order
	$result = getShopsOfOrder($orderId);
	$shopList = json_decode($result, true);
	$shopId = $shopList[0]['shop_id'];
		
	if ($codeFromDB == $vpCode)
		addRecordToPmtTable($orderId, $shopId, $ini_array['general']['tax_percent'], 'success');
	else
		addRecordToPmtTable($orderId, $shopId, $ini_array['general']['tax_percent'], 'failed');
}	
else
{
	echo "Invalid request.";
}
/**
 * This funtion will prepare authentication details for JSON data
 */
function getAuthentication($ini_array)
{
	$liveStatus = true;
	if ($ini_array['general']['live'] == "0")
		$liveStatus = false;
		
	$authentication['live'] = $liveStatus;
	$authentication['machine_id'] = $ini_array["ivr"]["machine_id"];
	$authentication['machine_secret'] = $ini_array["ivr"]["machine_secret"];
	return $authentication;
}

/**
 * This funtion will prepare payee details for JSON data
 */
function getIVRPayeeDetails($shop,$ini_array) {
	$payee=array();
    $payee['initiator'] = $shop["shop_name"];
    $payee['owner_email'] =$ini_array["ivr"]["owner_email"];
    return $payee;
}
?>
