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
	if (isset($_GET['orderid'])) {
		$orderId = $_GET['orderid'];
	} else die("Pass order id");

	$amtDisplayText = "charged";
	if (isset($_GET['s2p']))
		$amtDisplayText = "requested";
?>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<html>
	<head>
		<title>Payment</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="../css/jquery.mobile-1.3.1.min.css" />
		<link rel="stylesheet" href="styles/main.css" />
		<script src="../js/jquery-1.9.1.min.js"></script>
		<script src="../js/jquery.mobile-1.3.1.min.js"></script>
		<script src="js/payments.js" type="text/javascript"></script>
	</head>
	<body onload="loadTotalAmount();"> 
<!-- Payment Summary --->			
		<div data-role="page" id="pmt_summary" >
			<div id="success_header" class="pageheaders">
				<ul data-role="listview" data-inline="true">
					<li class="ui-body ui-body-b" style="background-color:rgb(254,48,12);border:none;padding-top:20;padding-bottom:20">
						<fieldset class="ui-grid-b">
							<div class="ui-block-a" align="center" style="width:100%;">
								<span style="color:white;">
									<span style="font-size:1em"><sup>&#36;</sup></span>
									<span id="chargedTotalAmount" style="font-size:1.5em;"></span>
								</span>
								<span id="totalAmountDisplayText" style="font-size:1.5em;color:white" data-inline="true"><?php echo $amtDisplayText;?></span>
							</div>
						</fieldset>
					</li>
				</ul>
			</div>
			<div data-role="content">
				<div class="ui-body ui-body-b">
					<h4 align="center">Thanks for your payment.</h4>
					<h4 align="center">Below is your payment summary</h4>
				</div>
			</div>
			<table align="center"  border = "0" style = "color: #00000; text-align = center; border:none" id="pay_sum_table">
			<?php
				// Showing payment summary including shop name, amount, status and QR code
				$result = getPaymentDetails($orderId);
				$pmtsDetails = json_decode($result, true);
				$totalAmount = 0.0;
				if (count($pmtsDetails) > 0) {
					foreach ($pmtsDetails as $shopPmtDetails) {
						$shopName = $shopPmtDetails['shop_name'];
						$pmtStatus = $shopPmtDetails['status'];
						$shopAmount = number_format($shopPmtDetails['amount'], 2);
						if ($pmtStatus == "success" || $amtDisplayText == "requested" && $pmtStatus == "pending") {
							$totalAmount = $totalAmount + doubleval($shopAmount);
						}
						$qrText = $shopPmtDetails['payment_id'] . ";" . $shopPmtDetails['amount'];
						echo "<tr><td width='150'>$shopName<br>Status: $pmtStatus<br>Amount: <span style='font-size:.8em'><sup>&#36;</sup></span>$shopAmount<td width='150'><img src = 'https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl=$qrText' height='150' width='150'/>\r\n";
					}
				}
				else {
					echo "<h4 align='center'>Internal system errors.</h4>";
				}
			?>
			</table>
			<input type="hidden" name="totalAmount" id="totalAmount" value="<?php echo $totalAmount; ?>" />
			<div id="payment_button" align="center" style="margin-top:2%"> 
				<h3 style="color: white;text-shadow: none;">Send Receipt to </h3>
				<div class="ui-btn-success" data-role="controlgroup" data-type="horizontal" >	
					<a class="ui-icon-pph" data-role="button"  id="btnReceiptToMobile" data-icon="pphere" data-theme="b" data-iconpos="top" data-inline="true" href="#" onClick="printOrder('<?php echo $orderId; ?>');">Print</a>
					<a class="ui-icon-vicon"  data-role="button" id="btnReceiptToEmail" data-icon="pphere" data-theme="b" data-iconpos="top" data-inline="true" href="#popupEmail" data-rel="popup" >Email</a>
					<a class="ui-icon-vicon" data-role="button" id="btnNoReceipt" data-icon="pphere" data-theme="b" data-iconpos="top"  data-inline="true" href="#SendReceiptByEmail">No Thanks</a>
				</div>
				<div data-role="popup" id="popupEmail" data-theme="a" class="ui-corner-all" style="padding:10px 20px;" >
					<h3>Please enter your Email</h3>
					<label for="email" class="ui-hidden-accessible">Email:</label>
					<input type="text" name="emailForMailing" id="emailForMailing" value="" placeholder="<email>" data-theme="a" />
					<button type="button" id="btnSendEmail" data-theme="b" onclick = "sendEmail('<?php echo $orderId;?>');">Ok</button>
				</div>
			</div>
		</div>
	</body>
</html>	