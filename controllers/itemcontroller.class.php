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
 * Interface which all checkout related package classes should implement
 * to make the calls from modules homogeneous
*/

/**
 * Item Controller to handle requests related to Items
 */
class ItemController{
	/*
	* URL: index.php/item/get/<itemid>		Return: Item Name and Price for itemID <itemid>
	*/
	public function get($request){
		$item=new Items();
		$item->set_id($request->url_elements[2]);
		$result = $item->getItemNameAndPrice();
		return $result;
	}
}
?>