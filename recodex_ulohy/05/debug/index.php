<?php
class AppFrontController
{
    const TEMPLATE_PREFIX = "templates/";
    const PARAMETERS_PREFIX= "parameters/";
    
    //Získání stránky z URL
    private function getPage()
    {
        return $_GET['page'] ?? '';
    }

    //V případě chybně zadaných URL parametrů
    private function urlValidation(string $page)
    {
        if (empty($page) || !preg_match('/^[a-zA-Z\/]+$/', $page)) {
            http_response_code(400);
            exit;
        }
    }

    //Template path fetch and validate
    private function getTemplatePathandValidate(string $page)
    {
        $templatePath = self::TEMPLATE_PREFIX . rtrim($page, '/');
        if (is_dir($templatePath)) {
            $templatePath .= '/index.php';
        } else {
            $templatePath .= '.php';
        }
        if (!file_exists($templatePath)) {
            http_response_code(404);
            exit;
        }
        return $templatePath;
    }

    //Fetch and validate parameters
    private function fetchParameters(string $templatePath): array
    {
        $parametersPath = str_replace(self::TEMPLATE_PREFIX, self::PARAMETERS_PREFIX, $templatePath);
        return file_exists($parametersPath) ? self::validateParameters($parametersPath) : [];
    }

    private function validateParameters(string $parametersFile): array
    {   
        if (file_exists($parametersFile)) {
            $params = include $parametersFile;
        } else {
            $params = [];
        }
        $validatedParameters = [];

        foreach ($params as $param => $type) {
            if (!isset($_GET[$param])) {
                http_response_code(400);
                exit;
            }
            switch ($type) {
                case 'int':
                    try {
                        $validateParameters[$param] = (int)$_GET[$param];
                    } catch (Exception $e) {
                        http_response_code(400);
                        exit; 
                    }
                    $validatedParameters[$param] = (int)$_GET[$param];
                    break;
                case 'string':
                    $validatedParameters[$param] = $_GET[$param];
                    break;
                case is_array($type):
                    if(!in_array($_GET[$param], $type)){
                        http_response_code(400);
                        exit;
                    }
                    $validatedParameters[$param] = $_GET[$param];
                    break;
                default:
                    http_response_code(400);
                    exit;
            }
        }

        return $validatedParameters;
    }

    public function run(): array
    {
        //Kontrola URL
        $page = $this->getPage();
        $this->urlValidation($page);
        //template validation
        $templatePath = $this->getTemplatePathandValidate($page);

        //fetch and validate parameters
        $parameters = $this->fetchParameters($templatePath);
        return [
            'templatePath' => $templatePath,
            'parameters' => $parameters
        ];
    }
}

function render(string $templatePath, array $parameters): void
{
        extract($parameters);
        require "templates/_header.php"; 
        require $templatePath;
        require "templates/_footer.php";
}
//runIt
$appController = new AppFrontController();
$runResult = $appController->run();

// Call renderTemplate with returned values
render($runResult['templatePath'], $runResult['parameters']);