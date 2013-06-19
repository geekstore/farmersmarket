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

if (isset($_POST['orderId']) && isset($_POST['ccType']) && isset($_POST['ccNumber']) 
	&& isset($_POST['ccExpiryMon']) && isset($_POST['ccExpiryYear']) && isset($_POST['ccCVV']) 
	&& isset($_POST['ccFirstName']) && isset($_POST['ccLastName']))
{
	// Credit card details
	$orderId = $_POST['orderId'];
	$ccType = $_POST['ccType'];
	$ccNumber = $_POST['ccNumber'];
	$ccExpiryMon = $_POST['ccExpiryMon'];
	$ccExpiryYear = $_POST['ccExpiryYear'];
	$ccCVV = $_POST['ccCVV'];
	$ccFirstName = $_POST['ccFirstName'];
	$ccLastName = $_POST['ccLastName'];
	
	$ini_array = parse_ini_file('config/payments.ini', true);

	// Getting all shops of order
	$result = getShopsOfOrder($orderId);
	$shopList = json_decode($result, true);

	foreach($shopList as $shop)
	{
		$shopId = $shop['shop_id'];

		$liveStatus = true;
		if ($ini_array['general']['live'] == "0")
			$liveStatus = false;
			
		// Creating input json for each shop
		$inputJsonArray = array (
			"authentication" => array (
				"live" => $liveStatus,
				"api_username" => $shop['shop_pp_api_uname'],
				"api_password" => $shop['shop_pp_api_passwd'],
				"api_signature" => $shop['shop_pp_api_sign'],
			),
			"intent" => "sale",
			"payer" => array (
				"funding_instrument" => array (
					"credit_card" => array (
						"number" => $ccNumber,
						"type" => $ccType,
						"expire_month" => $ccExpiryMon,
						"expire_year" => $ccExpiryYear,
						"cvv2" => $ccCVV,
						"first_name" => $ccFirstName,
						"last_name" => $ccExpiryYear
					)
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
		$header = "method: credit_card";

		// Sending request to payment library for credit card payment
		$result = runCurlPost($libraryUrl, $header, $inputJson);
		$result = json_decode($result, true);
		$pmtStatus = $result['status'];

		// Adding records to payment table
		addRecordToPmtTable($orderId, $shopId, $ini_array['general']['tax_percent'], $pmtStatus);
	}
}
else
{
	echo "Invalid request.";
}
?>
