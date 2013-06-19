function checkOutByPayPal() {
	var target = "./ProcessPPEmail.php";

	var data = {
		orderId: orderId,
	}
	var request = $.ajax({
        url: target,
        type: "post",
        dataType: 'json',
        data: data,
		async: false,
		cache: false
    })
    .success ( function(response) {
        console.log("Credit Card Success Response = " + response);
		$('#paykey').val(response.data.payKey);
    })
    .error ( function(response) {
        console.log("Credit Card Failed Response = " + response.responseText);
        console.log(target + " returned error");
    });
}

function ProcessCardPay() {
	var target = "./ProcessCardPay.php";
	var ccType = $('#ccType').val();
	var ccNumber = $('#ccNumber').val();
	var ccExpiryMon = $('#ccExpiryMon').val();
	var ccExpiryYear = $('#ccExpiryYear').val();
	var ccCVV = $('#ccCVV').val();
	var ccFirstName = $('#ccFirstName').val();
	var ccLastName = $('#ccLastName').val();

	var data = {
		orderId: orderId,
		ccType: ccType,
		ccNumber: ccNumber,
		ccExpiryMon: ccExpiryMon,
		ccExpiryYear: ccExpiryYear,
		ccCVV: ccCVV,
		ccFirstName: ccFirstName,
		ccLastName: ccLastName,
	}
	var request = $.ajax({
        url: target,
        type: "post",
        dataType: 'json',
        data: data,
		async: false,
		cache: false
    })
    .success ( function(response) {
        console.log("Credit Card Success Response = " + response);
		window.location = "./PmtSummary.php?orderid=" + orderId;
    })
    .error ( function(response) {
        console.log("Credit Card Failed Response = " + response.responseText);
        console.log(target + " returned error");
		window.location = "./PmtSummary.php?orderid=" + orderId;
    });
}

/**
 * ProcessShareToPay	 
 * This function handles the ShareToPay payment.This will redirect the page to payment summary page.
 */
function ProcessShareToPay() {
	var target = "./ProcessShareToPay.php";
	//Getting the payers list from ShareToPay page
	var s2p_payers = $('#s2p_payers').val();	
	var data = {
		orderId: orderId,
		s2p_payers : s2p_payers		
	}
	//Ajax call is made to ProcessShareToPay
	var request = $.ajax({
        url: target,
        type: "post",
        dataType: 'json',
        data: data,
		async: false,
		cache: false
    })
    .success ( function(response) {
        console.log("ShareToPay Success Response = " + response);
		window.location = "./PmtSummary.php?s2p&orderid=" + orderId;
    })
    .error ( function(response) {
        console.log("ShareToPayFailed Response = " + response.responseText);
        console.log(target + " returned error");
		window.location = "./PmtSummary.php?s2p&orderid=" + orderId;
    });
}
	
/**
 * ProcessVoicePay	 
 * This function handles the VoicePay payment.This will redirect the page to payment summary page.
 */
function ProcessVoicePay() {
	var target = "./ProcessVoicePay.php";	
	/*Getting the mobile no. from VoicePay page and splitting
	the country code and mobile no.If country code is not given
	1(US country code) is added by default*/
	var mobile = {};
	var phonenum = $('#phone').val();
	if (phonenum.length > 10) {
		mobile['country_code'] = phonenum.slice(0, phonenum.length - 10);
		mobile['number'] = phonenum.slice(phonenum.length - 10);
	} else {
		mobile['number'] = phonenum;
		mobile['country_code'] = '1';
	}	
	//packing data fields
	var data = {
		orderId: orderId,
		mobileno : mobile		
	}
	//ajax call is made 
	var request = $.ajax({
        url: target,
        type: "post",
        dataType: 'json',
        data: data,
		async: true,
		cache: false
    })
    .success ( function(response) {
        console.log("VoicePay Success Response = " + response);
    	 window.location = "./payments.php?&orderid=" + orderId + "#VerifyVPCode";

    })
    .error ( function(response) {
        console.log("VoicePay Failed Response = " + response.responseText);
        console.log(target + " returned error");
     	window.location = "./payments.php?&orderid=" + orderId + "#VerifyVPCode";

    });
}

/**
 * ProcessVoiceVerification	 
 * This function handles the VoicePay payment.This will redirect the page to payment summary page.
 */
function ProcessVoiceVerification()
{	
	var target = "./ProcessVoicePay.php";	
	//Getting the voice code entered by the user
	var voicePayCode =$('#voicePayCode').val();
	var data = {
		orderId: orderId,
		vpCode : voicePayCode		
	}
	//making ajax call
	var request = $.ajax({
        url: target,
        type: "post",
        dataType: 'json',
        data: data,
        async: false,
        cache: false
    })
    .success ( function(response) {
        console.log("ShareToPay Success Response = " + response);
		window.location = "./PmtSummary.php?&orderid=" + orderId;
    })
    .error ( function(response) {
        console.log("ShareToPayFailed Response = " + response.responseText);
        console.log(target + " returned error");
        window.location = "./PmtSummary.php?&orderid=" + orderId;
    });
}

function ProcessCashPay() {
	var target = "./ProcessCashPay.php";

	var data = {
		orderId: orderId,
	}
	var request = $.ajax({
        url: target,
        type: "post",
        dataType: 'json',
        data: data,
		async: false,
		cache: false
    })
    .success ( function(response) {
        console.log("Cash Success Response = " + response);
		window.location = "./PmtSummary.php?orderid=" + orderId;
    })
    .error ( function(response) {
        console.log("Cash Failed Response = " + response.responseText);
        console.log(target + " returned error");
		window.location = "./PmtSummary.php?orderid=" + orderId;
    });
}

/**
 * Display toast message
 */
function displayToastMessage(sMessage) {
	var container = $(document.createElement("div"));
	container.addClass("toast");

	var message = $(document.createElement("div"));
	message.addClass("message");
	message.text(sMessage);
	message.appendTo(container);

	container.appendTo(document.body);

	container.delay(100).fadeIn("slow", function() {
		$(this).delay(2000).fadeOut("slow", function() {
			$(this).remove();
		});
	});
}

/**
 * Prints order summary
 */
function printOrder(orderId)
{
	var target = "./utils/PrintOrder.php";

	var request = $.ajax({
        url: target,
        type: "post",
        data: "orderId=" + orderId,
    })
    .success (function(response) {
        console.log("Print Success Response = " + response);
		displayToastMessage("Order sent to the printer");
    })
    .error (function(response) {
        console.log("Print Failed Response = " + response.responseText);
		displayToastMessage("Error: Failed to print");
    });
}


/**
 * Sends the order summary email
 */

function sendEmail(orderId)
{
	var target = "./utils/SendEmail.php";
	var recipientEmail = $('#emailForMailing').val();
	console.log("emailForMailing: " + recipientEmail);
                
	if (recipientEmail != null && recipientEmail != "")
	{
		var request = $.ajax({
			url: target,
			type: "post",
			data: "orderId=" + orderId + "&recipientEmail=" + recipientEmail,
		})
		.success (function(response) {
			console.log("Email Success Response = " + response);
			if (response.indexOf("Mail Failed.") > -1)
				displayToastMessage("Error: Failed to send the email");
			else
				displayToastMessage("Email has been sent.");
		})
		.error (function(response) {
			console.log("Email Failed Response = " + response.responseText);
			displayToastMessage("Error: Failed to send the email");
		});
	}

	$("#popupEmail").popup('close');

	return false;
}

function loadTotalAmount() {
	var totalAmount = parseFloat($('#totalAmount').val());
	$('#chargedTotalAmount').text(totalAmount.toFixed(2));
}
