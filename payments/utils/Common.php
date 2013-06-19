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

// Runs the curl command
function runCurlGet($url, $header) {
	$curl = curl_init($url);

	//GET request: send headers and return data transfer
	$options = array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_SSLVERSION => 3,
			CURLOPT_HTTPHEADER => array($header)
			);
	curl_setopt_array($curl, $options);

	$result = curl_exec($curl);
	curl_close($curl);

	return $result;
}

// Runs the curl command
function runCurlPost($url, $header, $postData) {
	$curl = curl_init($url);

	//GET request: send headers and return data transfer
	$options = array (
		CURLOPT_URL => $url,
		CURLOPT_POST => 1,
		CURLOPT_VERBOSE => 1,
		CURLOPT_POSTFIELDS => $postData,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_SSLVERSION => 3,
		CURLOPT_HTTPHEADER => array($header)
	);
	curl_setopt_array($curl, $options);

	$result = curl_exec($curl);
	curl_close($curl);

	return $result;
}

// Runs the GetFileContent command
function runGetFileContents($url, $method = 'GET')
{
	// Getting all shops of orders
	$options = array(
		  'http' => array(
			'method'  => $method,
			'header'=> "Accept: application/json\r\n"
			)
		);
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
	
	return $result;
}

// Getting order details including items and amount for a perticular shop
function getOrderDetailsForShop($orderId, $shopId) {
	$hostName = $_SERVER['HTTP_HOST'];
	$homeDir = $_SESSION['homeDir'];
	$http = "http";
	if (isset($_SERVER['HTTPS']))
		$http = "https";
	$url = "$http://$hostName$homeDir/rest/index.php/order/get/$orderId?shop_id=$shopId";
	$result = runGetFileContents($url);
	return $result;
}

// Getting item details based on item id
function getItemDetails($itemId) {
	$hostName = $_SERVER['HTTP_HOST'];
	$homeDir = $_SESSION['homeDir'];
	$http = "http";
	if (isset($_SERVER['HTTPS']))
		$http = "https";
	$url = "$http://$hostName$homeDir/rest/index.php/item/get/$itemId";
	$result = runGetFileContents($url);
	return $result;
}

// Creating item section of json which is required for sending to library
function getItemDetailsForJson($orderId, $shopId, $taxPercent) {
	$shopItems = getOrderDetailsForShop($orderId, $shopId);
	$shopItems = json_decode($shopItems, true);

	$items = array();
	$itemIndx = 0;

	foreach ($shopItems['items'] as $shopItem) {
		$itemDetails = getItemDetails($shopItem['item_id']);
		$itemDetails = json_decode($itemDetails, true);
	
		$item = array (
			"item_name" => $itemDetails['item_name'],
			"item_desc" => $itemDetails['item_name'],
			"item_price" => $itemDetails['item_price'],
			"item_tax" => doubleval($itemDetails['item_price']) * doubleval($taxPercent)/100,
			"item_qty" => $shopItem['qty'],
			"merchant_email" => getShopPPEmailUsingItem($orderId, $shopItem['item_id'])
		);
		$items[$itemIndx++] = $item;
	}
	
	return $items;
}

// Creating transaction section of json which is required to send to library
function getTransactionDetails($currency) {
	$invoiceId = rand(1000, 9999);
	$transaction = array (
		"invoice_number" => $invoiceId,
		"currency" => $currency
	);
	
	return $transaction;
}

// Adding records to payment table
function addRecordToPmtTable($orderId, $shopId, $taxPercent, $pmtStatus) {
	$hostName = $_SERVER['HTTP_HOST'];
	$homeDir = $_SESSION['homeDir'];
	$http = "http";
	if (isset($_SERVER['HTTPS']))
		$http = "https";
	$result = getOrderDetailsForShop($orderId, $shopId);
	$result = json_decode($result, true);
	$shopAmount = doubleval($result['amount']);
	$taxAmount = $shopAmount * doubleval($taxPercent) / 100;
	
	// Sending request to record into payments table
	$totalAmount = $shopAmount + $taxAmount;
	$url3 = "$http://$hostName$homeDir/rest/index.php/payment/pay/$orderId?shopid=$shopId&amount=$totalAmount&status=$pmtStatus";
	$result = runGetFileContents($url3);
}

// Getting all shop of orders
function getShopsOfOrder($orderId) {
	$hostName = $_SERVER['HTTP_HOST'];
	$homeDir = $_SESSION['homeDir'];
	$http = "http";
	if (isset($_SERVER['HTTPS']))
		$http = "https";
	
	// Getting all shops of orders
	$url1 = "$http://$hostName$homeDir/rest/index.php/order/get/$orderId";
	$result = runGetFileContents($url1);
	return $result;
}

// Getting payments details for perticular order id
function getPaymentDetails($orderId) {
	$hostName = $_SERVER['HTTP_HOST'];
	$homeDir = $_SESSION['homeDir'];
	$http = "http";
	if (isset($_SERVER['HTTPS']))
		$http = "https";
		
	$url = "$http://$hostName$homeDir/rest/index.php/payment/get/$orderId";
	$result = runGetFileContents($url);
	return $result;
}

// Getting shop PayPal email id associated with item id
function getShopPPEmailUsingItem($orderId, $itemId) {
	$shopPPEmail = "";
	$found = false;
	
	$result = getShopsOfOrder($orderId);
	$shopList = json_decode($result, true);
	
	for ($i=0; $i<count($shopList) && $found == false; $i++) {
		$result = getOrderDetailsForShop($orderId, $shopList[$i]['shop_id']);
		$result = json_decode($result, true);
		$shopItems = $result['items'];
		
		for ($j=0; $j<count($shopItems) && $found == false; $j++) {
			if ($shopItems[$j]['item_id'] == $itemId) {
				$shopPPEmail = $shopList[$i]['shop_pp_email'];
				$found = true;
			}
		}
	}
	
	return $shopPPEmail;
}

// Getting shop id using shop PayPal email id
function getShopIdUsingShopPPEmail($shopPPEmail) {
	$hostName = $_SERVER['HTTP_HOST'];
	$homeDir = $_SESSION['homeDir'];
	$http = "http";
	if (isset($_SERVER['HTTPS']))
		$http = "https";
	$url = "$http://$hostName$homeDir/rest/index.php/shop/ppemail/$shopPPEmail";
	$result = runGetFileContents($url);
	return $result;
}

function addVoicePayCode($orderId, $code) {
	$hostName = $_SERVER['HTTP_HOST'];
	$homeDir = $_SESSION['homeDir'];
	$http = "http";
	if (isset($_SERVER['HTTPS']))
		$http = "https";
	$url = "$http://$hostName$homeDir/rest/index.php/payment/voicepay/$orderId?code=$code";
	$result = runGetFileContents($url);
	return $result;
}

function getVoicePayCode($orderId) {
	$hostName = $_SERVER['HTTP_HOST'];
	$homeDir = $_SESSION['homeDir'];
	$http = "http";
	if (isset($_SERVER['HTTPS']))
		$http = "https";
	$url = "$http://$hostName$homeDir/rest/index.php/payment/voicepay/$orderId";
	$result = runGetFileContents($url);
	return $result;
}
?>
