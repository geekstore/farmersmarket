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

/**
 * Payment Controller to handle requests related to Payment
 */
class PaymentController{
	/**
	* URL: index.php/payment/pay/<orderid>?shopid=<shopid>&amount=<amount>&status=<status>
	* Return: Return payment ID after entering the payment details into DB
	*/
	public function pay($request){
		$order_id=$request->url_elements[2];
		$shop_id=$request->parameters["shopid"];
		$amount=$request->parameters["amount"];
		$pmtStatus=$request->parameters["status"];
		$payment=new Payment();
		$payment->set_order_id($order_id);
		$payment->set_shop_id($shop_id);
		$payment->set_amount($amount);
		$payment->set_payment_status($pmtStatus);
		$paymentID=$payment->makePayment();
		return $paymentID;
	}
	/**
	* URL: index.php/payment/voicepay/<orderid>		Return: Return VoicePay Code for <orderid>
	* URL: index.php/payment/voicepay/<orderid>?code=<code>
	* Return: Return VoicePay Code for <orderid> after setting it to <code> in DB
	*/
	public function voicepay($request){
		$order_id=$request->url_elements[2];
		if(isset($request->parameters["code"])) $code=$request->parameters["code"];
		if(isset($code)) {
			return Payment::setVoicePayCode($order_id,$code);
		}
		else return Payment::getVoicePayCode($order_id);
	}
	/**
	* URL: index.php/payment/get/<orderid>
	* Return: Return All Payment Entries for <orderid>
	*/
	public function get($request){
		$order_id=$request->url_elements[2];
		$payment=new Payment();
		$payment->set_order_id($order_id);
		$payments=$payment->getPayments();
		return $payments;
	}
}
?>