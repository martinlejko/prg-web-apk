<?php

class Router
{
	private function parameterGetter($controllerClass, $method){
		$params = [];
		$reflectionMethod = new ReflectionMethod($controllerClass, $method);

		foreach ($reflectionMethod->getParameters() as $param) {
			$paramName = $param->getName();
			
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				$paramValue = $_POST[$paramName] ?? null;
			} else {
				$paramValue = $_GET[$paramName] ?? null;
			}

			if ($paramValue == null && !$param->isOptional()) {
				http_response_code(400);
				exit(0);
			}
			$params[] = $paramValue;
		}
		return $params;
	}

	private function actionValidation(){
		if (!isset($_GET['action']) || !preg_match('/^[a-zA-Z_\/]+$/', $_GET['action'])) {
			http_response_code(400);
			return;
		}
	}


	public function dispatch()
	{
		$this->actionValidation();
		$actionSlice = explode('/', $_GET['action']);

		if (count($actionSlice) < 2) {
			http_response_code(400);
			return;
		}


		$method = strtolower($_SERVER['REQUEST_METHOD']) . ucfirst(array_pop($actionSlice));
		$controller = implode('/', $actionSlice);
		$controllerPath = __DIR__ . "/controllers/$controller.php";

		
        if (!file_exists($controllerPath)) {
            http_response_code(404);
            return;
        }
        require_once $controllerPath;
		$controllerClass = ucfirst(end($actionSlice)) . "Controller";
		if (!class_exists($controllerClass)) {
			http_response_code(404);
			return;
		}
		$controllerInstance = new $controllerClass();

		if(!method_exists($controllerInstance, $method)){
			http_response_code(404);
			return;
		}


		try {
			$params = $this->parameterGetter($controllerClass, $method);
			$reflectionMethod = new ReflectionMethod($controllerClass, $method);
			$result = $reflectionMethod->invokeArgs($controllerInstance, $params);

			if ($result == null) {
				http_response_code(204);
				return;
			}
			echo json_encode($result);
		
		} catch (Exception) {
			http_response_code(500);
		}

	}	
}
