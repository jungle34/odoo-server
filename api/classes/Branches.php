<?PHP 

include_once "Base.php";

class Branches extends Base {
    function __construct() {
        $this->checkToken();
        $this->Connect();
    }

    private function buildJobExec() {
        return array(
            "job" => "create_new_branch",
            "user" => $this->auth,
            "content" => $_POST
        );
    }

    public function create() {
        $query = "INSERT INTO jobs (exec) VALUES (:exec)";

        $variables = array(
            "exec" => json_encode($this->buildJobExec())
        );

        try {
            $query = $this->db->prepare($query);
            $query->execute($variables);
        } catch(PDOException $e) {
            $this->returnError($e);
        }

        echo json_encode(array("TYPE" => "SUCCESS", "MESSAGE" => "Branch na fila de execução"));
        die();
    }
}