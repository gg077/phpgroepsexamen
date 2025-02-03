<?php

class Session
{
    /*properties*/
    private $signed_in;
    public $user_id;
    public $message;

    /*methods*/
    public function login($user){
        if($user){
            $this->user_id=$_SESSION['user_id']=$user->id;
            $this->signed_in = true;
        }
    }
    public function get_logged_in_user() {
        if ($this->user_id) { // Controleer of user_id in de sessie staat
            return User::find_by_id($this->user_id);
        }
        return null; // Geen ingelogde gebruiker
    }

    public function logout(){
        unset($_SESSION['user_id']);
        unset($this->user_id);
        $this->signed_in = false;
    }
    private function check_the_login(){
        if(isset($_SESSION['user_id'])){
            $this->user_id = $_SESSION['user_id'];
            $this->signed_in = true;
        }else{
            unset($this->user_id);
            $this->signed_in = false;
        }
    }
    public function is_signed_in(){
        return $this->signed_in;
    }
    public function message($msg=""){
        if(!empty($msg)){
            $_SESSION['message'] = $msg;
        }else{
            return $this->message;
        }
    }
    private function check_message(){
        if(isset($_SESSION['message'])){
            $this->message = $_SESSION['message'];
            unset($_SESSION['message']);
        }else{
            $this->message = "";
        }
    }
    /*constructors*/
    //automatisch starten van een session
    function __construct(){
        session_start();
        $this->check_the_login();
        $this->check_message();
    }


}
$session = new Session();
?>

