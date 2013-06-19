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
//Checking if orderid and sharetopayers list are given in the post parameters
if (isset($_POST['orderId']) && isset($_POST['s2p_payers']))
{
	$orderId = $_POST['orderId'];
	$s2p_payers = $_POST['s2p_payers'];
	//Loading the values from ini file into array
	$ini_array = parse_ini_file('config/payments.ini', true);
	//Getting the list of shops in the order
	$result = getShopsOfOrder($orderId);
	$shopList = json_decode($result, true);
	//Looping through each shop
	foreach($shopList as $shop)
	{	
		$liveStatus = true;
		if ($ini_array['general']['live'] == "0")
			$liveStatus = false;

		//generating json data for each shop
		$shopId = $shop['shop_id'];
		$inputJsonArray = array (
			"authentication" => array (
				"live" => $liveStatus
			),
			"payee" => getS2PPayeeDetails($shop), //get the payee details
			"payer" => array (
				"funding_instrument" => array (
					"sharetopay" => array (
						"payerslist" => $s2p_payers	 //appending the payers list got from the user				
					)
				)
			),
			"items" => getItemDetailsForJson($orderId, $shopId, $ini_array['general']['tax_percent']),//getting item details
			"transaction" => getS2PPaymentDetails($orderId,$shopId) //getting payment details
		);		
			
		$inputJson = json_encode($inputJsonArray);
		//Making call to the library
		$http = "http";
		if (isset($_SERVER['HTTPS']))
			$http = "https";
		$hostName = $_SERVER['HTTP_HOST'];
		$libraryUrl = "$http://$hostName/" . $ini_array['general']['pmt_library_url']."/checkout";
		$header = "method: sharepay";

		// Sending request to sharetopay apis 
		$result = runCurlPost($libraryUrl, $header, $inputJson);			
		$result = json_decode($result, true);		
		$pmtStatus = $result['status'];		
		if(isset($result['status']))
		{
			if($result['status']== 'success')
				$pmtStatus='pending';
			else
				$pmtStatus='failed';
		
		//adding the payment details to database
		addRecordToPmtTable($orderId, $shopId, $ini_array['general']['tax_percent'], $pmtStatus);
		}		
	}
}
else
{
	echo "Invalid request.";
}
/**
 * This funtion will prepare payee details for JSON data
 */
function getS2PPayeeDetails($shop) {
    $payee['eventname'] =$shop['shop_name'];
    $payee['businessdesc'] = $shop['shop_name'];
    $payee['email'] = $shop['shop_pp_email'];
    $payee['creator'] = $shop['shop_pp_email'];
    return $payee;
}

/**
 * Returns Payment Details in JSON format
 */

function getS2PPaymentDetails($orderId,$shopId) {

	$orderItems= getOrderDetailsForShop($orderId,$shopId); 
	$orderItems=json_decode($orderItems,true);
	$amount=$orderItems['amount'];
	$payment['minamount']=$amount;
	$payment['maxamount']=$amount;
    return $payment;
}
?>
