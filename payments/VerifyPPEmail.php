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

// Checking the payment status of paypal adaptive api and adding record to the payment tables
if (isset($_GET['orderId']) && isset($_GET['payKey'])) {
	$homeDir = $_SESSION['homeDir'];

	$orderId = $_GET['orderId'];
	$payKey = $_GET['payKey'];
	
	$ini_array = parse_ini_file('config/payments.ini', true);

	$liveStatus = "true";
	if ($ini_array['general']['live'] == "0")
		$liveStatus = "false";

	// Getting api credential from ini file which is required for getting payment status
	$live = $liveStatus;
	$api_username = $ini_array['paypal_email']['api_username'];
	$api_password = $ini_array['paypal_email']['api_password'];
	$api_signature = $ini_array['paypal_email']['api_signature'];
	$api_appid = $ini_array['paypal_email']['api_appid'];
	
	$http = "http";
	if (isset($_SERVER['HTTPS']))
		$http = "https";
	$hostName = $_SERVER['HTTP_HOST'];
	$data = "live=$live&api_username=$api_username&api_password=$api_password&api_signature=$api_signature&api_appid=$api_appid&payKey=$payKey";
	
	$url = "$http://$hostName/".$ini_array['general']['pmt_library_url']."/status?" . $data;
	$header = "method: email";

	// Sending request to payment library to get the payment status
	$result = runCurlGet($url, $header);
	$result = json_decode($result, true);
	$shopList = $result['data'];
	
	// adding payment entry to DB if payment status is success
	if ($result['status'] == "success") {
		foreach ($shopList as $shop) {
			$shopPmtStatus = getPaymentStatus($shop['status']);
			$result = getShopIdUsingShopPPEmail($shop['email']);
			$result = json_decode($result, true);
			$shopId = $result['shop_id'];
			
			addRecordToPmtTable($orderId, $shopId, $ini_array['general']['tax_percent'], $shopPmtStatus);
		}
	}
	
	// Redirecting to payment summary page
	header("location:./PmtSummary.php?orderid=$orderId");
} else {
	die ("Pass the PayKey");
}

// Mapping API payment status to DB payment status
function getPaymentStatus($apiPmtStatus) {
	$pmtStatus = "pending";
	
	switch ($apiPmtStatus) {
	case "COMPLETED":
		$pmtStatus = "success";
		break;
	case "PENDING":
		$pmtStatus = "pending";
		break;
	case "CREATED":
		$pmtStatus = "failed";
		break;
	case "PARTIALLY_REFUNDED":
		$pmtStatus = "partially_refunded";
		break;
	case "DENIED":
		$pmtStatus = "rejected";
		break;
	case "PROCESSING":
		$pmtStatus = "in_progress";
		break;
	case "REVERSED":
		$pmtStatus = "payment_returned";
		break;
	case "REFUNDED":
		$pmtStatus = "refunded";
		break;
	case "FAILED":
		$pmtStatus = "failed";
		break;
	default:
		$pmtStatus = "pending";
	}
	
	return $pmtStatus;
}
?>