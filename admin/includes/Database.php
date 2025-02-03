<?php
require_once("config.php");
class Database
{
    /* properties (props)*/
    public $connection;
    /* methods (functions)*/
    public function open_db_connection(){
        $this->connection = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
        if(mysqli_connect_errno()){
            printf("Connectie is mislukt: %s\n", mysqli_connect_error());
            exit();
        }
    }
//    public function query($sql){
//        $result = $this->connection->query($sql);
//        $this->confirm_query($result);
//        return $result;
//    }
    public function confirm_query($result){
        if(!$result){
            die("Query kan niet worden uitgevoerd" . $this->connection->error);
        }
    }
    public function escape_string($string){
        if($string === null){
            return 'NULL';
        }
        return $this->connection->real_escape_string($string);
    }

    public function query($sql, $params = []) {
        // create a prepared statement
        $stmt = $this->connection->prepare($sql);
        // bind the parameters
        if (!empty($params)) {
            $types = "";
            $values = [];
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= "i";
                } elseif (is_float($param)) {
                    $types .= "d";
                } else {
                    $types .= "s";
                }
                $values[] = $param;
            }
            array_unshift($values, $types);
            call_user_func_array([$stmt, "bind_param"], $this->ref_values($values));
        }
        // execute the statement
        $stmt->execute();
        // process the result
        $result = $stmt->get_result();
        // close the statement
        $stmt->close();
        return $result;
    }

    private function ref_values($array) {
        $refs = [];
        foreach ($array as $key => $value) {
            if ($key === 0) {
                $refs[$key] = $value;
            } else {
                $refs[$key] = &$array[$key];
            }
        }
        return $refs;
    }
    public function get_last_insert_id() {
        return mysqli_insert_id($this->connection);
    }

    /* default constructor */
    function __construct(){
        $this->open_db_connection();
    }



}
$database = new Database();
?>