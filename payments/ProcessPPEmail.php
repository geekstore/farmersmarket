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
if (isset($_POST['orderId'])) {
	$orderId = $_POST['orderId'];
	$ini_array = parse_ini_file('config/payments.ini', true);

	// Creating input json for PayPal email payment
	$inputJsonArray = array(
		"authentication" => getPPEmailAuthentication($ini_array),
		"url" => getRedirectUrls(),
		"payee" => array(
			"email" => getPPPayeeEmails($orderId)
		),
		"items" => getPPEmailItemsDetail($orderId, $ini_array['general']['tax_percent']),
		"transaction" => getTransactionDetails($ini_array['general']['currency'])
	);
	
	$inputJson = json_encode($inputJsonArray);

	$http = "http";
	if (isset($_SERVER['HTTPS']))
		$http = "https";
	$hostName = $_SERVER['HTTP_HOST'];
	$libraryUrl = "$http://$hostName/" . $ini_array['general']['pmt_library_url']."/checkout";
	$header = "method: email";

	// Sending request to payment library for PayKey
	$libResult = runCurlPost($libraryUrl, $header, $inputJson);
	echo $libResult;
}

// Getting authentication required for paypal email PayKey
function getPPEmailAuthentication($ini_array) {
	$liveStatus = true;
	if ($ini_array['general']['live'] == "0")
		$liveStatus = false;
	$auth = array (
		"live" => $liveStatus,
		"api_username" => $ini_array['paypal_email']['api_username'],
		"api_password" => $ini_array['paypal_email']['api_password'],
		"api_signature" => $ini_array['paypal_email']['api_signature'],
		"api_appid" => $ini_array['paypal_email']['api_appid']
	);
	
	return $auth;
}

// Getting redirect and cancel urls
function getRedirectUrls() {
	$urls = array (
		"return_url" => urlencode("http://www.paypal.com"),
		"cancel_url" => urlencode("http://www.paypal.com")
	);
	
	return $urls;
}

// Getting all payee PayPal email IDs
function getPPPayeeEmails($orderId) {
	$result = getShopsOfOrder($orderId);
	$shopList = json_decode($result, true);

	$ShopsPPEmails = "";
	
	foreach ($shopList as $shop) {
		$ShopsPPEmails = $ShopsPPEmails . "," . $shop['shop_pp_email'];
	}
	
	$ShopsPPEmails = substr($ShopsPPEmails, 1);
	
	return $ShopsPPEmails;
}

// Getting items details
function getPPEmailItemsDetail($orderId, $taxPercent) {
	$result = getShopsOfOrder($orderId);
	$shopList = json_decode($result, true);

	$items = array();
	
	foreach ($shopList as $shop) {
		$items_temp = getItemDetailsForJson($orderId, $shop['shop_id'], $taxPercent);
		$items = array_merge($items, $items_temp);
	}
	
	return $items;
}
?>
