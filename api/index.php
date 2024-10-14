<?PHP
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    header('Content-Type: application/json; charset=UTF-8');
    ini_set("display_errors", true);
    /**
     * Developed by João Victor Ferreira De Andrade     
     * 
     * @gitHub jungle34
     * 
     */
    
    include_once ".cnf";

    class Rest{
        public $url;
        public $class;
        public $method;
        public $paramns;
        public $headers;        

        /**
         * Rest init function
         */
        function __construct(){  
            // Set the paramns of the request
            $this->paramns = $this->getRequestUrl();             

            $this->checkEndpoint();
            
        }

        /**
         * Call the class and the method in the request                        
         */
        function checkEndpoint(){            

            $this->setClassMethod();

            $this->pathVerify();

            $classFile = "classes/".$this->class.".php";
            require_once $classFile;                        

            if (!class_exists($this->class) or !method_exists($this->class, $this->method)) $this->returnError("Invalid request - can not find the class of the request");

            return call_user_func_array(array(new $this->class, $this->method), array($this->paramns));            
        }        

        /**
         * Get the request paramns        
         */
        function getRequestUrl(){
            $aux = str_replace("/api/", "", $_SERVER["REQUEST_URI"]);
            $aux = explode('?', $aux);
            $paramns = false;

            if(!empty($aux[1])){
                $paramns = array();
                $auxParamns = explode('&', $aux[1]);
                if($auxParamns[0] != ""){
                    foreach($auxParamns as $key => $value){
                        $item = explode('=', $value);
                        if(isset($item[0]) and isset($item[1])) $paramns[$item[0]] = $item[1];
                    }                    
                }
            }

            return $paramns;            
        }                

        /**
         * Returns error message with JSON format
         */
        function returnError($err){
            $return = array("STATUS" => 'ERROR', "MESSAGE" => $err);
            echo json_encode($return);
            die();
        }

        private function pathVerify(){
            $path = __DIR__."/classes/".$this->class.".php";            
            if(!file_exists($path)){
                $this->returnError("Invalid request - can not find the file of the request");
            }
        }

        private function setClassMethod(){
            $tmpUrl = str_replace("/api/", "", $_SERVER["REQUEST_URI"]);                 
            $tmpUrl = explode('?', $tmpUrl);
            $tmpUrl = explode('/', $tmpUrl[0]);

            $this->class = !empty($tmpUrl[0]) ? ucfirst($tmpUrl[0]) : false;
            $this->method = !empty($tmpUrl[1]) ? $tmpUrl[1] : false;

            if(!$this->class) $this->returnError("Insert the class request");
            if(!$this->method) $this->returnError("Insert the method request");
        }
    }

    $request = new Rest();