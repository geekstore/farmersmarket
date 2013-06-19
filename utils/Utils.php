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

function runCurl($url, $method = 'GET', $postvals = null){
	$curl = curl_init($url);

	//GET request: send headers and return data transfer
	if ($method == 'GET'){
		$options = array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_SSLVERSION => 3
				);
		curl_setopt_array($curl, $options);
		//POST / PUT request: send post object and return data transfer
	} else {
		$options = array(
				CURLOPT_URL => $url,
				CURLOPT_POST => 1,
				CURLOPT_VERBOSE => 1,
				CURLOPT_POSTFIELDS => $postvals,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_SSLVERSION => 3
				);
		curl_setopt_array($curl, $options);
	}

	$result = curl_exec($curl);
	curl_close($curl);

	return $result;
}

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

function createOrderFileForPrint($orderId)	
{
	$hostName = $_SERVER['HTTP_HOST'];
	$path = substr($_SERVER['PHP_SELF'],0, strrpos($_SERVER['PHP_SELF'], '/'));
	$http = "http";
	if (isset($_SERVER['HTTPS']))
		$http = "https";
	
	// Getting all shops of orders
	$url1 = "$http://$hostName$path/rest/index.php/order/get/$orderId";
	$result = runGetFileContents($url1);
	$shopList = json_decode($result, true);

	// Creating file named as "<order>.txt" at ./temp folder
	$fp = fopen("./temp/" . $orderId . ".txt", 'w');

	foreach($shopList as $shop)
	{
		$shopId = $shop['shop_id'];

		fwrite($fp, str_pad($shop['shop_name'], 48, " ", STR_PAD_BOTH) . "\r\n\r\n\r\n");
		fwrite($fp, "Item Name\t\tPrice\tQty\tSubTotal\r\n");
		fwrite($fp, "------------------------------------------------\r\n\r\n");

		// Getting all shop items of order
		$url2 = "$http://$hostName$path/rest/index.php/order/get/$orderId?shop_id=$shopId";
		$result = runGetFileContents($url2);
		$result = json_decode($result, true);
		$shopItems = $result['items'];
		$shopAmount = $result['amount'];

		foreach($shopItems as $item)
		{
			$itemId = $item['item_id'];
			$itemQty = $item['qty'];

			// Getting item details
			$url3 = "$http://$hostName$path/rest/index.php/item/get/$itemId";
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

function createOrderFileForEmail($orderId)	
{
	$hostName = $_SERVER['HTTP_HOST'];
	$path = substr($_SERVER['PHP_SELF'],0, strrpos($_SERVER['PHP_SELF'], '/'));
	$http = "http";
	if (isset($_SERVER['HTTPS']))
		$http = "https";
		
	// Getting all shops of orders
	$url1 = "$http://$hostName$path/rest/index.php/order/get/$orderId";
	$result = runGetFileContents($url1);
	$shopList = json_decode($result, true);

	// Creating file named as "<order>.txt" at ./temp folder
	$fp = fopen("./temp/" . $orderId . ".txt", 'w');
	fwrite($fp, "<html>\r\n");
	fwrite($fp, "<body>\r\n");

	foreach($shopList as $shop)
	{
		$shopId = $shop['shop_id'];

		fwrite($fp, "<table>\r\n");
		fwrite($fp, "<tr><th colspan=4 align=center>" . $shop['shop_name'] . "\r\n");
		fwrite($fp, "<tr><th>Item Name<th>Price<th>Qty<th>SubTotal\r\n");

		// Getting all shop items of order
		$url2 = "$http://$hostName$path/rest/index.php/order/get/$orderId?shop_id=$shopId";
		$result = runGetFileContents($url2);
		$result = json_decode($result, true);
		$shopItems = $result['items'];
		$shopAmount = $result['amount'];

		foreach($shopItems as $item)
		{
			$itemId = $item['item_id'];
			$itemQty = $item['qty'];

			// Getting item details
			$url3 = "$http://$hostName$path/rest/index.php/item/get/$itemId";
			$result = runGetFileContents($url3);
			$result = json_decode($result, true);

			$itemName = $result['item_name'];
			$itemPrice = number_format(intval($result['item_price']), 2);
			$itemSubTotal = number_format(intval($itemQty) * intval($result['item_price']), 2);
			fwrite($fp, "<tr><td>$itemName<td align=right>\$$itemPrice<td align=right>$itemQty<td align=right>\$$itemSubTotal\r\n");
		}

		fwrite($fp, "<tr><th colspan=4 align=right>Total: \$" . number_format($shopAmount, 2) . "\r\n");
		fwrite($fp, "</table>\r\n");
		fwrite($fp, "<br><br>\r\n");
	}

	fwrite($fp, "</html>\r\n");
	fwrite($fp, "</body>\r\n");
	fclose($fp);
}
?>
