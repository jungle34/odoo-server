<?PHP 

    require_once "Base.php";

    class Auth extends Base{
        function __construct(){
            $this->Connect();
        }

        private function regexID($id, $regex) {
            preg_match_all($regex, $id, $matches);

            return $matches;
        }

        private function makeArrayNumbers($elements) {
            foreach($elements[0] as $k => $v) {
                $index = false;

                if (preg_match('/\d/m', $v)) {
                    $index = (int)$v;
                } else{
                    $index = (int)round((ord($v) - (ord($v) / 2) / 2), 0, PHP_ROUND_HALF_DOWN);
                }

                if (is_null($v) || !$index) continue;

                $dados[] = $index;
            }

            return $dados;
            
        }

        private function getRandomKey($max) {
            return mt_rand(0, $max);
        }

        private function getRandomKeys($target, $max) {
            $data = [];
            for($i = 0;$i <= $target;$i++) {
                $key = $this->getRandomKey($max);

                if (in_array($key, $data)) {
                    $key = $this->getRandomKey($max);
                }

                $data[] = $key;
            }
            
            return $data;
        }

        private function tokenize($tk, $index) {
            return chr($tk) . chr($index);
        }

        private function randonize($arr) {
            $rdm_keys = $this->getRandomKeys(round(count($arr) / 10, 0, PHP_ROUND_HALF_UP), count($arr));

            foreach($rdm_keys as $k => $v) {
                if (!in_array($v, array_keys($arr))) continue;

                $arr[$v] = utf8_encode($this->tokenize(($arr[$v] ? $arr[$v] : $v), $v));
            }

            $data = [];
            foreach($arr as $k => $v) {
                if (is_string($v)) $data[] = trim($v);
            }

            $random_int = $this->getRandomKeys(5, 64);            
            foreach($random_int as $k => $v) {
                $data[] = $v;
            }

            if (shuffle($data)) {                
                return $data;
            } else {
                throw new Exception("Erro ao gerar o Session_ID");
            }            
        }

        private function subRandonize($str) {
            preg_match_all('/\d{2}/m', $str, $matches);
            
            foreach($matches[0] as $k => $v) {                
                $replaces = trim(chr($v));

                $str = str_replace($v, $replaces, $str);
            }            

            return $str;
        }

        private function makeSessionID($id) {
            $char = $this->makeArrayNumbers($this->regexID($id, '/[a-zA-Z]/m'));
            $digits = $this->makeArrayNumbers($this->regexID($id, '/\d/m'));

            try {
                $rndm_char = $this->randonize($char);
                $rndm_digits = $this->randonize($digits);
            } catch (Exception $e) {
                $this->returnError($e->getMessage());
                die();
            }

            $merged = array_merge($rndm_char, $rndm_digits);

            $retorno = false;
            if (shuffle($merged)) {
                $retorno = array(
                    "TYPE" => 'SUCCESS',
                    "MSG" => 'Session_ID gerado com sucesso',
                    "DATA" => utf8_encode($this->subRandonize(implode("", $merged)))
                );
            }            

            if (!$retorno) $this->retornErr("Erro ao gerar o Session_ID");
            
            return $retorno;
        }
 
        public function getSessionID($parans){
            $session_id = $_COOKIE['session_id'];

            $retorno = $this->makeSessionID($session_id);

            echo json_encode($retorno);
            die();
        }

        public function signUp() {
            $email = !empty($_POST['email']) ? $_POST['email'] : false;
            if (!$email) $this->returnErr("E-mail não informado");

            $pass = !empty($_POST['password']) ? $_POST['password'] : false;
            if (!$pass) $this->returnErr("Senha não informada");

            $salt = !empty($_POST['salt']) ? $_POST['salt'] : false;
            if (!$salt) $this->returnErr("Salt não informado");

            $query = "CALL login_signup(:email, :password, :salt)";

            $variables = array(
                "email" => $email,
                "password" => $pass,
                "salt" => $salt
            );

            try {
                $query = $this->db->prepare($query);
                $query->execute($variables);                
            } catch (PDOException $e) {
                $this->returnErr($e);
                die();
            }            

            $this->returnSuccess("Usuário cadastrado com sucesso");
            die();
        }

        private function getToken($id, $salt) {
            $this->db2->beginTransaction();

            $query = "INSERT INTO login_tokens (user_id, token) VALUES (:user_id, :token)";

            $variables = array(
                "user_id" => $id,
                "token" => $salt
            );

            try {
                $query = $this->db2->prepare($query);
                $query->execute($variables);
            } catch (PDOException $e) {
                $this->returnErr($e);
                die();
            }

            $this->db2->commit();

            return $salt;            
        }

        public function login() {
            $email = !empty($_POST['email']) ? $_POST['email'] : false;
            if (!$email) $this->returnErr("E-mail não informado");

            $senha = !empty($_POST['password']) ? $_POST['password'] : false;
            if (!$senha) $this->returnErr("Senha não informada");

            $salt = !empty($_POST['salt']) ? $_POST['salt'] : false;
            if (!$salt) $this->returnErr("Salt não informado");

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $this->returnErr("E-mail inválido");

            $this->db->beginTransaction();
            
            $query = "CALL login_signin(:email, :password)";

            $variables = array(
                "email" => $email,
                "password" => $senha
            );

            try {
                $query = $this->db->prepare($query);
                $query->execute($variables);
            } catch (PDOException $e) {
                $this->returnErr($e);
                die();
            }            

            $dados = $query->fetchObject();
            
            if (!$dados) $this->returnErr("Usuário não encontrado");
            
            $query->closeCursor();

            $this->db->commit();

            $retorno = array(
                "TYPE" => 'SUCCESS',
                "MSG" => 'Usuário logado com sucesso',
                "TOKEN" => $this->getToken($dados->id, $salt),
                "DATA" => ($dados->login_count > 0)
            );

            echo json_encode($retorno);
            die();
        }
    }
