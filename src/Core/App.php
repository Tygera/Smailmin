<?php

namespace Smailmin\Core;

use Smailmin\Core\Helper;
use Smailmin\Core\Session;
use Smailmin\Core\Database;

use Smailmin\Container\Container;

class App {
	
	private array $config = [];
	private array $routes = [];
	
	public function __construct(array $config, array $routes) {
		$this->config = $config;
		$this->routes = $routes;
	}
	
	public function run(): void {
	
		Session::start();
		
		$request = new Request($_SERVER, $_GET, $_POST);
		$router = new Router($this->routes);
		$handler = $router->find($request);
		
		$controller_namespace = preg_replace("/Core/", 'Controllers\\', __NAMESPACE__);
		$controller_class  = $controller_namespace . $handler[0][0];
		$controller_method = $handler[0][1];
		$parameters_get    = $handler[1];
		$parameters_route  = $handler[2];
		
		$container = new Container();
		
		$container->set('config', $this->config);
		
		$container->set(\Smailmin\Core\Database::class, function($call) {
			return new \Smailmin\Core\Database($this->config);
		});
		
		$controller = $container->get($controller_class);
		$controller->$controller_method($request);
	}
	
}
