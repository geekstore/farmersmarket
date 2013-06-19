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
 * Item Class contains all methods related to items and is program representation of the 'items' table in DB
 * Supports getting and setting all variables
 */
class Items{
	protected $itemid;
	
	public function __construct(){	
	}
	
	public function item_info(){
		$dbc=Order::connect();
		$sql="Select * from items where item_id='$this->itemid'";
		$result=mysqli_query($dbc,$sql);
		$row=mysqli_fetch_array($result,MYSQLI_ASSOC);
		return $row;
	}
	public static function get_all_cats(){
		$dbc=Order::connect();
		$sql="Select DISTINCT(category) from items";
		$result=mysqli_query($dbc,$sql);
		$num=mysqli_num_rows($result);
		$i=0;
		while($i++<$num){
			$row=mysqli_fetch_array($result,MYSQLI_ASSOC);
			$retarr[$i]=$row["category"];
		}
		return $retarr;
	}
	
	public static function get_all_by_cat($cat){
		$dbc=Order::connect();
		$sql="Select * from items where category='$cat'";
		$result=mysqli_query($dbc,$sql);
		$num=mysqli_num_rows($result);
		$i=0;
		while($i++<$num){
			$row[$i]=mysqli_fetch_array($result,MYSQLI_ASSOC);
		}
		return $row;
	}
	
	public function set_id($id){
		$this->itemid=$id;
	}
	public function getCost(){
		$dbc=Order::connect();
		$sql="Select item_price from items where item_id='$this->itemid'";
		$result=mysqli_query($dbc,$sql);
		$row=mysqli_fetch_array($result,MYSQLI_ASSOC);
		$price=$row["item_price"];
		return $price;
	}

	public function getItemNameAndPrice(){
		$dbc=Order::connect();
		$sql="Select item_name,item_price from items where item_id='$this->itemid'";
		$result=mysqli_query($dbc,$sql);
		$row=mysqli_fetch_array($result,MYSQLI_ASSOC);
		return $row;
	}
}
?>