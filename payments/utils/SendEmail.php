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
require_once('../../lib/PHPMailer/class.phpmailer.php');
require_once('./Common.php');

$orderId = $_POST['orderId'];
$recipientEmail = $_POST['recipientEmail'];

// Creating order summary file
createOrderFileForEmail($orderId);
$orderFileName = "../../temp/" . $orderId . ".txt";
$mailContent = file_get_contents($orderFileName);

$ini_array = parse_ini_file('../config/payments.ini', true);

// Creating object of PHP Mailer
$mail = new PHPMailer();

$mail->SetFrom($ini_array['email_for_communication']['email_from']);
$mail->AddReplyTo($ini_array['email_for_communication']['email_from']);

$address = $recipientEmail;
$mail->AddAddress($address);

$mail->Subject = "Order Summary";

$mail->MsgHTML($mailContent);

if ($mail->Send()) {
	echo "Mail Sent.";
} else {
	echo "Mail Failed. Error: " . $mail->ErrorInfo;
}

exec("rm ../../temp/" . $orderId . ".txt");

// Creates the temporary file for send email
function createOrderFileForEmail($orderId)	
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
	fwrite($fp, "<html>\r\n");
	fwrite($fp, "<body>\r\n");

	foreach($shopList as $shop)
	{
		$shopId = $shop['shop_id'];

		fwrite($fp, "<table>\r\n");
		fwrite($fp, "<tr><th colspan=4 align=center>" . $shop['shop_name'] . "\r\n");
		fwrite($fp, "<tr><th>Item Name<th>Price<th>Qty<th>SubTotal\r\n");

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

			// Getting item details
			$url3 = "$http://$hostName$homeDir/rest/index.php/item/get/$itemId";
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
