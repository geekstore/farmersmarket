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
 * Shop Controller to handle requests related to Shops
 */
class ShopController{
	/*
	* URL: index.php/shop/get/		Return: All Shops
	* URL: index.php/shop/get/<shopid>		Return: Items Available in shop no <shopid>
	*/
	public function get($request){
		if(isset($request->url_elements[2])) $shop_id=$request->url_elements[2];
		if(isset($shop_id)){
			$items=Shop::getItemsbyShop($shop_id);
			return $items;
		}
		$shops=Shop::getAllShops();
		return $shops;
	}

	public function ppemail($request){
		if (isset($request->url_elements[2])) {
			$ppEmail = $request->url_elements[2];
			$ShopPPEmail = Shop::getShopIdByShopPPEmail($ppEmail);
			return $ShopPPEmail;
		}
		return NULL;
	}
}
?>