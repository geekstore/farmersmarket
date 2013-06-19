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
 * Order Class contains all methods related to order and is program representation of the 'orders' table in DB
 * Supports getting and setting all variables
 */
class Order {
	protected $order_id;
	
	public function __construct(){
		
	}
	public static function connect(){
		$ini_array = parse_ini_file('../config/config.ini', true);
		$db="farmer";
		return new mysqli($ini_array['db']['host'],$ini_array['db']['user'],$ini_array['db']['pwd'],$db);
	  }
	 public function new_order(){
		$this->order_id=$this->generateKey(6);
	}
	public function add_item($id,$qty,$shop){
		$dbc=$this->connect();
		$sql="Insert into orders (order_id,item_id,shop_id,qty) values ('$this->order_id','$id','$shop','$qty')";
		$result=$dbc->query($sql);
		return $result;
	}
	public function get_shops(){
		$dbc=$this->connect();
		$sql="Select DISTINCT orders.shop_id,shop_name,shop_pp_email,shop_pp_api_uname,shop_pp_api_passwd,shop_pp_api_sign from orders,shops where order_id='$this->order_id' and orders.shop_id=shops.shop_id";
		$result=mysqli_query($dbc,$sql);
		$num=mysqli_num_rows($result);
		for($i=0;$i<$num;$i++){
			$retarr[$i]=mysqli_fetch_array($result,MYSQLI_ASSOC);
		}
		return $retarr;
	}
	
	public function get_items($shopid){
		$dbc=$this->connect();
		$sql="Select item_id,qty from orders where order_id='$this->order_id' and shop_id='$shopid'";
		$result=mysqli_query($dbc,$sql);
		$num=mysqli_num_rows($result);
		for($i=0;$i<$num;$i++){
			$retarr[$i]=mysqli_fetch_array($result,MYSQLI_ASSOC);
		}
		return $retarr;
	}
	
	public function get_items_without_shop(){
		$dbc=$this->connect();
		$sql="Select item_id,qty from orders where order_id='$this->order_id'";
		$result=mysqli_query($dbc,$sql);
		$num=mysqli_num_rows($result);
		for($i=0;$i<$num;$i++){
			$retarr[$i]=mysqli_fetch_array($result,MYSQLI_ASSOC);
		}
		return $retarr;
	}
	
	public function get_all_items(){
		$dbc=$this->connect();
		$sql="Select item_id,qty from orders where order_id='$this->order_id'";
		$result=mysqli_query($dbc,$sql);
		$num=mysqli_num_rows($result);
		for($i=0;$i<$num;$i++){
			$retarr[$i]=mysqli_fetch_array($result,MYSQLI_ASSOC);
		}
		return $retarr;
	}

	public static function generateKey($len){
			$chars="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
			$key="";
			for($i=0; $i<$len; $i++){
				$index=rand(0,strlen($chars)-1);
				$key=$key.$chars[$index];
			}
			return $key;
		}
	public function set_id($id){
		$this->order_id=$id;
	}

	public function set_type($type){
		$this->order_type=$type;
	}
	public function set_table($id){
		$this->table_id=$id;
	}
	public function get_id(){
		return $this->order_id;
	}
}
?>
