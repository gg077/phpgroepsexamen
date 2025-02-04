<?php

class User extends Db_object
{
    //properties
    //public,private, protected
    public $id;
    public $email;
    public $google_id;
    public $password;
    public $first_name;
    public $last_name;
    public $created_at;
    public $deleted_at;
    protected static $table_name = 'users';
    //methods

    public static function verify_user($email, $password) {
        global $database;
        $email = $database->escape_string($email);

        // Zoek gebruiker op e-mail
        $sql = "SELECT * FROM " . self::$table_name . " WHERE email = ? LIMIT 1";
        $the_result_array = self::find_this_query($sql, [$email]);

        if (!empty($the_result_array)) {
            $user = array_shift($the_result_array); // Haal het eerste resultaat op

            // Check of wachtwoord al gehashed is
            if (password_verify($password, $user->password)) {
                return $user; // Wachtwoord is al gehashed → login normaal
            }

            // Controleer of het wachtwoord nog plaintext is
            if ($user->password === $password) {
                // Dit betekent dat het wachtwoord nog in plaintext is opgeslagen
                // 3. Hash het wachtwoord nu en update de database
                $new_hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $user->password = $new_hashed_password;
                $user->update();

                return $user; // Geef de gebruiker terug en laat ze inloggen
            }
        }

        return false; // Geen gebruiker of wachtwoord incorrect
    }




    /* CRUD */
    /*properties als array voorzien*/
    public function get_properties(){
        return[
            'id'=> $this->id,
            'email'=>$this->email,
            'google_id'=>$this->google_id,
            'password'=>$this->password,
            'first_name'=>$this->first_name,
            'last_name'=>$this->last_name,
            'created_at'=>$this->created_at,
            'deleted_at'=>$this->deleted_at
        ];
    }



}