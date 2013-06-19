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
 * Order Controller to handle requests related to Order
 */
class OrderController{
	/**
	* URL: index.php/order/get/<orderid>		Return: All Shops from which order no <orderid> has ordered items
	* URL: index.php/order/get/<orderid>?shop_id=<shopid>		Return: All Items ordered from <shopid> in order no. <orderid>
	*/
	public function get($request){
		$order=new Order();
		$order->set_id($request->url_elements[2]);
		if(isset($request->parameters["shop_id"])) $shop_id=$request->parameters["shop_id"];
		if(isset($shop_id)) {
			$list=$order->get_items($shop_id);
			$num=sizeof($list);
			$amount=0;
			$item=new Items();
			for($i=0;$i<$num;$i++){
				$item->set_id($list[$i]["item_id"]);
				$amount+=$item->getCost()*$list[$i]["qty"];
			}
			$retarr["items"]=$list;
			$retarr["amount"]=$amount;
			return $retarr;
		}
		$list=$order->get_shops();
		return $list;
	}
	/**
	* URL: index.php/order/place	Data: POST with order details	Return: Create New Order and return OrderID
	*/
	public function place($request){
		$params=$request->parameters;
		$data=$params["data"];
		$order=json_decode($data);
		
		$orderobj=new Order();
		$orderobj->new_order();
		$orderid=$orderobj->get_id();
		
		$items=$order->items;
		$numitems=sizeof($items);
		for($i=0;$i<$numitems;$i++){
			$item=$items[$i][0];
			$qty=$items[$i][1];
			$shop=$items[$i][2];
			$orderobj->add_item($item,$qty,$shop);
		}
		return $orderid;
	}
	/**
	* URL: index.php/order/amount/<orderid>		Return: Total Amount for OrderID <orderid>
	*/
	public function amount($request){
		$orderid=$request->url_elements[2];
		if(!$orderid) return; //TODO: return to error page
		$order=new Order();
		$order->set_id($orderid);
		$list=$order->get_items_without_shop();
		$num=sizeof($list);
		$amount=0;
		$item=new Items();
		for($i=0;$i<$num;$i++){
			$item->set_id($list[$i]["item_id"]);
			$amount+=$item->getCost()*$list[$i]["qty"];
		}
		return $amount;
	}
	
	
}
?>