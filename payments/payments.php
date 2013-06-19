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
	require './utils/Constants.php';
	
	// read config file
	$ini_array = parse_ini_file('config/payments.ini', true);
	
	if (isset($_GET['orderid'])) {
		$orderId = $_GET['orderid'];
	} else die("Pass order id");

	if (isset($_GET['method']))
		$method = $_GET['method'];
	
	$ppAdaptivePayUrl = "";
	if ($ini_array['general']['live'] == "0")
		$ppAdaptivePayUrl = PP_ADAPTIVE_SANDBOX_URL;
	else
		$ppAdaptivePayUrl = PP_ADAPTIVE_LIVE_URL;

	$totalAmount = 0.0;
	$result = getShopsOfOrder($orderId);
	$shopList = json_decode($result, true);
	foreach ($shopList as $shop) {
		$result = getOrderDetailsForShop($orderId, $shop['shop_id']);
		$result = json_decode($result, true);
		$totalAmount = $totalAmount + doubleval($result['amount']);
	}
	
	$totalAmount = $totalAmount + $totalAmount * doubleval($ini_array['general']['tax_percent']) / 100;
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
		<script src="https://www.paypalobjects.com/js/external/apdg.js" type="text/javascript"></script>
		
		<script  type="text/javascript">
			var orderId ="<?php echo $orderId; ?>";
		</script>
	</head>

	<body> 

<!-- Home Page For Payments -->
		<div data-role="page" id="paymentsHome" >
			<div class="pageheaders" data-theme="a" data-role="header" id="HomeHeader" >
				<div align="center">
				<span style="font-size:1.5em;" data-inline="true">Total 
					<span style="color:orangered;">
						<span style="font-size:.8em"><sup>&#36;</sup></span>
						<span id="TotalamountHeader"><?php echo number_format($totalAmount, 2);?></span>
					</span>
				</span>
				</div>
			</div>
		
			<div data-role="content"  data-theme="d" class="pagecontent" >
				<div id="payment_button" align="center" class="ui-btn-pay" >
					<form method="GET" action="<?php echo $ppAdaptivePayUrl;?>" id="frmPayPalEmail"  onsubmit="checkOutByPayPal();">
						<input type="hidden" size="50" maxlength="32" name="paykey" id ="paykey"/>
						<input type="hidden" name="expType" value="mini" />
						<input type="submit" id="em_authz_button" value="PayPal" data-theme="b" data-icon="pphere" data-iconpos="top" />
					</form>
					<a class="ui-icon-cicon1" data-role="button" id="btnCardIcon" data-icon="cicon" data-theme="b" data-iconpos="top"  data-inline="true" data-theme="b" href="#cardpay"  >Card Pay</a>
					<a class="ui-icon-casicon1" data-role="button" id="btnCashIcon" data-icon="casicon" data-theme="b" data-iconpos="top"  data-role="button" data-inline="true" data-theme="b" onclick="ProcessCashPay()">Cash</a>
					<a class="ui-icon-vicon1 ui-disabled" data-role="button" id="btnVoiceIcon" data-icon="vicon" data-iconpos="top" data-inline="true" data-theme="b" href="#voicepay" >Voice Pay</a>
					<a class="ui-icon-s2p1 ui-disabled" data-role="button" id="btns2pIcon" data-icon="s2pe" data-theme="b" data-iconpos="top" data-role="button" data-inline="true" data-theme="b" href="#share2pay">Share2Pay</a>
				</div>
			</div>
		</div>

<!-- Card Payment --->
		
			<div data-role="page" id="cardpay" >
			<div class="pageheaders" data-theme="a" data-role="header" >
				<h3>Card Pay</h3>
			</div>
			<div data-role="content" class="pagecontent">
				<form name = "frmCardPay" id="frmCardPay">
					<select  name="type" id="ccType" data-mini="false" data-inline="false">
						<option value="">Choose a card type...</option>
						<option value="Visa" selected="true">Visa</option>
						<option value="MasterCard">Master Card</option>
						<option value="Discover">Discover</option>
						<option value="Amex">American Express</option>
						<option value="Switch">Switch</option>
						<option value="Solo">Solo</option>
					</select>
					
					<input type="text" name="first_name" id="ccFirstName" size="10" placeholder="First Name" data-mini="false" value="Joe" />
					<input type="text" name="last_name" id="ccLastName" size="10" placeholder="Last Name" data-mini="false" value="Shopper" />
					<input type="text" name="number" id="ccNumber" size="16" placeholder="16 digit Card Number" value="4417119669820331"  data-mini="false" />
					
					<fieldset data-role="controlgroup" data-type="horizontal">
						<legend><b>Expiry Date</b></legend>
						<select name="expire_month" id="ccExpiryMon" data-mini="false" data-inline="false">
							<option value="01">01</option>
							<option value="02">02</option>
							<option value="03">03</option>
							<option value="04">04</option>
							<option value="05">05</option>
							<option value="06">06</option>
							<option value="07">07</option>
							<option value="08">08</option>
							<option value="09">09</option>
							<option value="10">10</option>
							<option value="11" selected="true">11</option>
							<option value="12">12</option>
						</select>
						<select name="expire_year" id="ccExpiryYear" data-mini="false" data-inline="false">
							<option value="2010">2010</option>
							<option value="2011">2011</option>
							<option value="2012">2012</option>
							<option value="2013">2013</option>
							<option value="2014">2014</option>
							<option value="2015">2015</option>
							<option value="2016">2016</option>
							<option value="2017">2017</option>
							<option value="2018" selected="true">2018</option>
							<option value="2019">2019</option>
							<option value="2020">2020</option>
						</select>
					</fieldset>
					<input type="password" name="cvv2" id="ccCVV" size="10" placeholder="CVV" data-mini="false" data-inline="false" value="874"/>
					<input  type="button" data-inline="false" data-theme="b"   value="Pay" onclick="ProcessCardPay();"/>
				</form>
			</div>
			<div data-role="footer" class="nav-glyphish-example" data-position="fixed" style="height:10%;" >
			</div>
		</div>
		
<!-- Voice Payment --->
		<div data-role="page" id="voicepay" >
			<div data-theme="a" data-role="header" class="pageheaders">
				<h3>Voice Pay</h3>
			</div>
			<div data-role="content" class="pagecontent">
				<form name = "frmVoicePay" id="frmVoicePay" >
					<label for="lsendto"><b>Registered Mobile No:</b></label>
					<input name="phone" id="phone" placeholder="<mobile>" value="" type="text" style="height:3em">
					<input  type="button" data-inline="true" data-theme="b"   value="Submit" onclick="ProcessVoicePay();" />
				</form>
			</div>
			<div data-role="footer" class="nav-glyphish-example" data-position="fixed" style="height:10%;" >
			</div>
		</div>
			
		<!-- Share2Pay --->
		<div data-role="page" id="share2pay" >
			<div data-theme="a" data-role="header" class="pageheaders">
				<h3>Share2Pay</h3>
			</div>
			<div data-role="content" class="pagecontent">
				<form id="frmShare2Pay">
					<label for="lsendto"><b>Request money from</b></label>
					<input name="s2p_payers" id="s2p_payers" placeholder="<email> ; <mobile> ; <fb> ; <tweet>" value="" type="text" style="height:3em">
					<input  type="button" data-inline="true" data-theme="b"  value="Send Request"  onclick="ProcessShareToPay();"/>
				</form>
			</div>
			<div data-role="footer" class="nav-glyphish-example" data-position="fixed" style="height:10%;" >
			</div>
		</div>
		
<!-- Voice Pay Verification --->		
		<div data-role="page" id="VerifyVPCode">
			<div data-theme="a" data-role="header" class="pageheaders">
				<h3>Voice Pay Code Verification</h3>
			</div>
			<div data-role="content" class="pagecontent">
				<form name = "frmVerifyVPCode" id="frmVerifyVPCode" >
					<label for="voicePayCode">Enter the code:</label>
					<input name="code" id="voicePayCode" placeholder="<code>" value="" type="text" style="height:3em">
					<input  type="button" data-inline="true" data-theme="b"  value="Submit" onclick="ProcessVoiceVerification();" />
				</form>
			</div>
			<div data-role="footer" class="nav-glyphish-example" data-position="fixed" style="height:10%;" >
			</div>
		</div>
	</body>
	<script type="text/javascript" charset="utf-8">
		var returnFromPayPal = function(){
			parent.location="<?php $http = "http"; if (isset($_SERVER['HTTPS'])) $http = "https"; echo $http; ?>://"+ parent.location.hostname +"<?php echo $_SESSION['homeDir'];?>/payments/VerifyPPEmail.php?orderId=<?php echo $orderId; ?>&payKey=" + $('#paykey').val();
		}
		var dgFlowMini = new PAYPAL.apps.DGFlowMini({trigger: 'em_authz_button', expType: 'mini', callbackFunction: 'returnFromPayPal'});

		function startMiniFlow(){
			dgFlowMini.startFlow("<?php echo $ppAdaptivePayUrl;?>=");
		}
	</script>
</html>	