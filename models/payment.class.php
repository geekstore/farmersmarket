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
 * Payment Class contains all methods related to payments and is program representation of the 'payments' table in DB
 * Supports getting and setting all variables
 */
class Payment{
	protected $order_id;
	protected $shop_id;
	protected $amount;
	protected $pmtStatus;
	
	public function makePayment(){
		$dbc=Order::connect();
		$payID=Order::generateKey(6);
		$sql="Insert into payments (payment_id,order_id,shop_id,amount,status) values ('$payID','$this->order_id','$this->shop_id','$this->amount','$this->pmtStatus')";
		$result=mysqli_query($dbc,$sql);
		if($result) return $payID;
		else return NULL;
	}
	
	public static function setVoicePayCode($orderid,$code){
		$dbc=Order::connect();
		$sql="INSERT INTO voicepay_codes (order_id, code) VALUES ('$orderid', '$code')";
		$result=mysqli_query($dbc,$sql);
		if($result) return $code;
		else return NULL;
	}
	
	public static function getVoicePayCode($orderid){
		$dbc=Order::connect();
		$sql="Select code from voicepay_codes where order_id='$orderid'";
		$result=mysqli_query($dbc,$sql);
		if($result) return mysqli_fetch_array($result,MYSQLI_ASSOC);
		else return NULL;
	}
	
	public function getPayments(){
		$dbc=Order::connect();
		$sql="Select payments.payment_id,shops.shop_name, payments.amount, payments.status, payments.amount from payments, shops where payments.shop_id = shops.shop_id and payments.order_id='$this->order_id'";
		$result=mysqli_query($dbc,$sql);
		$num=mysqli_num_rows($result);
		for($i=0;$i<$num;$i++){
			$retarr[$i]=mysqli_fetch_array($result,MYSQLI_ASSOC);
		}
		return $retarr;
	}
	
	public function set_order_id($orderid){
		$this->order_id=$orderid;
	}
	public function set_shop_id($shopid){
		$this->shop_id=$shopid;
	}
	public function set_amount($amount){
		$this->amount=$amount;
	}
	public function set_payment_status($pmtStatus){
		$this->pmtStatus=$pmtStatus;
	}
}
