<?php
/**
 * Router - A simple request based page router 
 * User: Samuel Zhang
 * Date: 2018-01-01
 */

class Router
{
	private $router_config;
	
	public function __construct($router_config){
		$this->router_config = $router_config;
	}
	
	public function dispatch(){
		
		$result = '';
		$parsed_request = self::parseRequest();
		$request_route = $this->matchRouting($parsed_request['params']);
		$handle_page = $request_route['__page__'];
		return $this->getPageOutput(PAGE_DIR.'/'.$handle_page);
	}
	
	public static function parseRequest(){
		$request_uri = $_SERVER['REQUEST_URI'];
		$result = array(
			'params' => array(),
			'data' => array(
				'get' => array(),
				'post' => array(),
				'header' => array(),
			),
		);
		
		if(isset($_GET) && !empty($_GET)){
			$result['data']['get'] = $_GET;
		}
		
		$http_raw_post_data = file_get_contents('php://input');
		if(isset($http_raw_post_data) && !empty($http_raw_post_data)){
			$result['data']['post'] = $http_raw_post_data;
		}else if(isset($_POST) && !empty($_POST)){
			$result['data']['post'] = $_POST;
		}
		
		$headers = apache_request_headers();
		if($headers){
			$result['data']['header'] = $headers;
		}
		
		$request_uri = preg_replace('/\/$/','',$request_uri);
		if(strpos($request_uri, "?") !== false){
			$request_uri = substr($request_uri, 0, strpos($request_uri, "?"));
		}
		$params = explode("/", $request_uri);
		for($i=1;$i<10;$i++){
			if(isset($params[$i]) && !empty($params[$i])){
				$result['params'][$i] = $params[$i];
			}else{
				break;
			}
		}
		return $result;
	}
	
	private function matchRouting($params){
		$route = $this->router_config;
		if(!$params){
			return $this->router_config['__default__'];
		}
		foreach($params as $param){
			if(isset($route[$param]) && !empty($route[$param])){
				$route = $route[$param];
			}else{
				break;
			}
		}
		if(isset($route['__page__'])){
			return $route;
		}
		return $this->router_config['__undefine__'];
	}
	
	private function getPageOutput($path){
		ob_start();
		include_once $path;
		$result = ob_get_clean();		
		return $result;
	}
	
}