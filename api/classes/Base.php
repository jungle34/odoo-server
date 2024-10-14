<?php

header('Access-Control-Allow-Methods: GET, POST');

header("Access-Control-Allow-Headers: *");


include_once "Database.php";

Class Base{
    public $db;
    public $headers;
    public $user_ID;
    public $caminho = '/var/www/html/odoo-server/';

    public $auth;

	/**
     * Makes the connect with database to check access token
     */
    function Connect(){
        $db = new Database();        
        $this->db = $db->connect();
        
        $db2 = new Database();
        $this->db2 = $db2->connect();
    }

    /**
     * Check if the access token is valid
     */
    function checkToken(){
        $db = new Database();
        $this->base_db = $db->connect();

        $this->headers = $this->getHeaders();

        if (!isset($this->headers['Authorization'])) $this->returnError("Token não informado");        

        $this->auth = $this->isValidToken($this->headers['Authorization']);

        if(!$this->auth) $this->returnError("Token inválido");
    }

    private function isValidToken($token) {
        $query = "CALL check_token(:token)";        

        try{
            $query = $this->base_db->prepare($query);
            $query->execute(array(':token' => $token));
        } catch(PDOException $e){
            $this->returnError($e);
        }

        return $query->fetchObject();
    }

    function format($type, $value){
        switch($type){
            case "DB_DATE":
                $value = explode('/', $value);
                if(sizeof($value) == 3){
                    $ano = $value[2];
                    $mes = $value[1];
                    $dia = $value[0];

                    return "{$ano}-{$mes}-{$dia}";
                }else{
                    $this->returnError("Insira uma data válida");
                }                
            break;
            case "DB_DECIMAL":
                $val = str_replace('.', '', $value);
                $val = str_replace(',', '.', $val);

                return $val;
            break;
        }
    }

    function build_update($variables){
        $aux = "";
        foreach($variables as $field => $val){
            $aux .= "{$field} = :{$field}, ";
        }

        return substr($aux, 0, -2);
    }

    /**
     * Get all request headers or block the rest if doesn't have request headers
     */
    function getHeaders(){
        $aux = array();
        $headers = getallheaders();

        foreach ($headers as $key => $value) {
            $aux[$key] = $value;
        }
        if(empty($aux)) $this->returnError("Request headers can not is empty");
        return $aux;
    } 

    function returnErr($msg){
        echo json_encode(array("TYPE" => 'ERROR', "MSG" => $msg));
        die();
    }

    function returnInfo($msg){
        echo json_encode(array("TYPE" => 'INFO', "MSG" => $msg));
        die();
    }

    function returnSuccess($msg){
        echo json_encode(array("TYPE" => 'SUCCESS', "MSG" => $msg));
        die();
    }

    function checkEmptyVariable($str){
        $aux = !empty($str) ? $str : false;
        if(!$aux) $this->returnError("Requisição inválida");

        return urldecode($aux);
    }

    function formatMoney($val){                        
        $val = str_replace('.', '', $val);
        $val = str_replace(',', '.', $val);
        
        return floatval($val);
    }

}