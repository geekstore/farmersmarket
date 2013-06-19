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
require 'Common.php';

$orderId = $_POST['orderId'];

// Creating temporary file
createOrderFileForPrint($orderId);

// Printing temporary file and deleting after that
exec("lpr -r ../../temp/" . $orderId . ".txt");

// Creates the temporary file for print
function createOrderFileForPrint($orderId)	
{
	$hostName = $_SERVER['HTTP_HOST'];
	$homeDir = $_SESSION['homeDir'];
	$http = "http";
	if (isset($_SERVER['HTTPS']))
		$http = "https";
	
	// Getting all shops of orders
	$url1 = "$http://$hostName$homeDir/rest/index.php/order/get/$orderId";
	$result = runGetFileContents($url1);
	$shopList = json_decode($result, true);

	// Creating file named as "<order>.txt" at ./temp folder
	$fp = fopen("../../temp/" . $orderId . ".txt", 'w');

	foreach($shopList as $shop)
	{
		$shopId = $shop['shop_id'];

		fwrite($fp, str_pad($shop['shop_name'], 48, " ", STR_PAD_BOTH) . "\r\n\r\n\r\n");
		fwrite($fp, "Item Name\t\tPrice\tQty\tSubTotal\r\n");
		fwrite($fp, "------------------------------------------------\r\n\r\n");

		// Getting all shop items of order
		$url2 = "$http://$hostName$homeDir/rest/index.php/order/get/$orderId?shop_id=$shopId";
		$result = runGetFileContents($url2);
		$result = json_decode($result, true);
		$shopItems = $result['items'];
		$shopAmount = $result['amount'];

		foreach($shopItems as $item)
		{
			$itemId = $item['item_id'];
			$itemQty = $item['qty'];

			$http = "http";
			if (isset($_SERVER['HTTPS']))
				$http = "https";
				
			// Getting item details
			$url3 = "$http://$hostName$homeDir/rest/index.php/item/get/$itemId";
			$result = runGetFileContents($url3);
			$result = json_decode($result, true);

			$itemName = $result['item_name'];
			$itemPrice = number_format(intval($result['item_price']), 2);
			$itemSubTotal = number_format(intval($itemQty) * intval($result['item_price']), 2);
			fwrite($fp, str_pad($itemName, 24) . "\$$itemPrice\t$itemQty\t". str_pad("\$" . $itemSubTotal, 8, " ", STR_PAD_LEFT) . "\r\n");
		}

		fwrite($fp, "------------------------------------------------\r\n\r\n");
		fwrite($fp, "\t\t\t\tTotal:\t" . str_pad("\$" . number_format($shopAmount, 2), 8, " ", STR_PAD_LEFT));
		fwrite($fp, "\r\n\r\n\r\n\r\n");
	}

	fclose($fp);
}
?>
