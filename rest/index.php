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
 * The Base File which receives the request and 
 * calls the appropriate method and 
 * returns back the json encoded response
 */
/**
 * Uncomment the below lines to enable PHP error reporting
error_reporting(-1);
ini_set('display_errors', 1);
 */

/**
 * Class Autoloader - Specify paths to autoload classes from
 */
function autoload_class($class_name) {
    $directories = array(
		'../rest/',
        '../controllers/',
		'../models/'
    );
    foreach ($directories as $directory) {
		//all classnames must be smallcase
        $filename = $directory . strtolower($class_name) . '.class.php';		
        if (is_file($filename)) {
            require($filename);
            break;
        }
    }
}

/**
 * Register autoloader functions.
 */
spl_autoload_register('autoload_class');

/**
 * Parse the incoming request.
 */
$request = new Request();

//PATH INFO has /abc/xyz where abc is the class name and xyz is method name
if (isset($_SERVER['PATH_INFO'])) { 
    $request->url_elements = explode('/', trim($_SERVER['PATH_INFO'], '/'));
}

$request->method = strtoupper($_SERVER['REQUEST_METHOD']);
switch ($request->method) {
    case 'GET':
        $request->parameters = $_GET;
    break;
    case 'POST':
        $request->parameters = $_POST;
    break;
    case 'PUT':
        parse_str(file_get_contents('php://input'), $request->parameters);
    break;
	case 'DELETE':
        parse_str(file_get_contents('php://input'), $request->parameters);
    break;
}

/**
 * Route the request.
 */
if (!empty($request->url_elements)) {
/**
 * Each Controller is named based on the first element in path_info
 * For a URL - index.php/class/method
 * The Controller name is ClassController
 */
    $controller_name = ucfirst($request->url_elements[0]) . 'Controller'; 
	
    if (class_exists($controller_name)) {
        $controller = new $controller_name;
/**
 * The method is second element in path_info
 * For a URL - index.php/class/method
 * The action_name is method
 */
        $action_name = strtolower($request->url_elements[1]);
/**
 * Appropriate method is called based on Controller Name and Action Name
 * Request Object is passed as a parameter to the method call
 */
		$response_str = call_user_func_array(array($controller, $action_name), array($request));
    }
    else {
        header('HTTP/1.1 404 Not Found');
        $response_str = 'Unknown request: ' . $request->url_elements[0];
    }
}
else {
    $response_str = 'Unknown request';
}

/**
 * Send the response to the client.
 */
$response_obj = Response::create($response_str, $_SERVER['HTTP_ACCEPT']);

/**
 * JSON Encode the response_obj and send to client
 */
echo $response_obj->render();