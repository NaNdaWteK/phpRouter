<?php
class Router{

    private $params = array();
    private $url = array();
    private $controller;
    private $namespace;
    private $method;

    const NAMESPACE_URL_INDEX = 0;
    const METHOD_URL_INDEX = 1;
    const NOT_FOUND_CODE = 404;
    const MACHED = true;

    public function __construct()
    {
        $this->url = self::parse();
        self::loadFiles();
        self::checkMethod();
        self::setParams();
    }

    public function doRequest($route, $method)
    {
        $mached = false;
        if($this->namespace == $route){
            $mached = self::matchMethod(self::removeGuions($method));
        }

        return $mached;
    }

    public function sendError()
    {
        http_response_code(self::NOT_FOUND_CODE);
        echo json_encode(['status' => 'error', 'code' => self::NOT_FOUND_CODE, 'message' => 'Not found']);
    }

    private function parse()
    {
        if( isset($_GET['url'] ) ){
            return explode('/', filter_var(rtrim($_GET['url'] , '/'), FILTER_SANITIZE_URL));
        }
    }

    private function matchMethod($method)
    {
        if($this->method == $method){
            self::action();
            return self::MACHED;
        }
    }

    private function action()
    {
        echo json_encode(self::invoke());
    }

    private function loadFiles()
    {
        if(file_exists('./system/'.$this->url[self::NAMESPACE_URL_INDEX].'/Controller.php')){
            require_once './system/'.$this->url[self::NAMESPACE_URL_INDEX].'/Controller.php';
            self::loadControler();
        }
    }
    private function loadControler()
    {
        self::setNamespace();
        self::upController();
        self::removeParam(self::NAMESPACE_URL_INDEX);
    }
    private function upController()
    {
        $controller ='\\'.$this->namespace.'\\'.'Controller';
        $this->controller = new $controller();
    }

    private function checkMethod()
    {
        if(isset($this->url[self::METHOD_URL_INDEX])){
            self::method_exists();
        }
    }
    private function method_exists()
    {
        $method = self::removeGuions($this->url[self::METHOD_URL_INDEX]);
        if(method_exists($this->controller, $method)){
            self::setMethod($method);
        }
    }
    private function setMethod($method)
    {
        $this->method = $method;
        self::removeParam(self::METHOD_URL_INDEX);
    }

    private function setParams()
    {
        $this->params = $this->url ? array_values($this->url) : [];
    }
    private function invoke()
    {
        return call_user_func_array([$this->controller, $this->method], $this->params);
    }
    private function setNamespace()
    {
        $this->namespace = $this->url[self::NAMESPACE_URL_INDEX];
    }
    private function removeGuions($method)
    {
        return str_replace('-', '', $method);
    }
    private function removeParam($index){
        unset($this->url[$index]);
    }

}
