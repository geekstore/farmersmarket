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
 * Shop Class contains all methods related to shops and is program representation of the 'shops' table in DB
 * Supports getting and setting all variables
 */
class Shop{
	protected $shop_id;
	protected $shop_name;
	
	public function __construct(){
	}
	
	public static function getAllShops(){
		$dbc=Order::connect();
		$sql="Select * from shops";
		$result=mysqli_query($dbc,$sql);
		$num=mysqli_num_rows($result);
		for($i=0;$i<$num;$i++){
			$shops[$i]=mysqli_fetch_array($result,MYSQLI_ASSOC);
		}
		return $shops;
	}
	
	public static function getAllCats(){
		$dbc=Order::connect();
		$sql="Select * from categories";
		$result=mysqli_query($dbc,$sql);
		$num=mysqli_num_rows($result);
		for($i=0;$i<$num;$i++){
			$shops[$i]=mysqli_fetch_array($result,MYSQLI_ASSOC);
		}
		return $shops;
	}
	
	public static function getItemsbyCat($cat){
		$dbc=Order::connect();
		$sql="Select * from items,shops,categories where items.cat_id='$cat' and items.shop_id=shops.shop_id and items.cat_id=categories.cat_id";
		$result=mysqli_query($dbc,$sql);
		$num=mysqli_num_rows($result);
		if($num>0){
			for($i=0;$i<$num;$i++){
				$items[$i]=mysqli_fetch_array($result,MYSQLI_ASSOC);
			}
		}
		else return NULL;
		return $items;
	}
	
	public static function getItemsbyShop($shop){
		$dbc=Order::connect();
		$sql="Select * from items,shops,categories where items.shop_id='$shop' and items.shop_id=shops.shop_id and items.cat_id=categories.cat_id";
		$result=mysqli_query($dbc,$sql);
		$num=mysqli_num_rows($result);
		if($num>0){
			for($i=0;$i<$num;$i++){
				$items[$i]=mysqli_fetch_array($result,MYSQLI_ASSOC);
			}
		}
		else return NULL;
		return $items;
	}
	
	public function getShopID(){
		return $this->shop_id;
	}
	public function getShopName(){
		return $this->shop_name;
	}
	public function setShopID($id){
		$this->shop_id=$id;
	}
	public function setShopName($name){
		$this->shop_name=$name;
	}
	public static function getShopIdByShopPPEmail($ppEmail){
		$dbc = Order::connect();
		$sql = "Select shop_id from shops where shop_pp_email='$ppEmail'";
		$result = mysqli_query($dbc, $sql);
		$num = mysqli_num_rows($result);
		if ($num>0){
			$shopId = mysqli_fetch_array($result,MYSQLI_ASSOC);
		}
		else return NULL;
		return $shopId;
	}
}
?>