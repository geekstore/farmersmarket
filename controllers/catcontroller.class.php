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
 * Category Controller to handle requests related to Item Category
 */
class CatController{
	/**
	* URL: index.php/cat/get	Return: All Different Categories
	* URL: index.php/cat/get/<catid>	Return: All Items with category <catid>
	*/
	public function get($request){
		if(isset($request->url_elements[2])) $cat_id=$request->url_elements[2];
		if(isset($cat_id)){
			$items=Shop::getItemsbyCat($cat_id);
			return $items;
		}
		$cats=Shop::getAllCats();
		return $cats;
	}
}
?>