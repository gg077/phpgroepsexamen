<?php

class Db_object
{
    public static function find_this_query($sql, $values = []){
        global $database;
        $result = $database->query($sql,$values);
        $the_object_array = [];
        while($row = mysqli_fetch_assoc($result)){
            $the_object_array[] = static::instantie($row);
        }
        return $the_object_array;
    }
    public static function instantie($result){
        $calling_class = get_called_class(); //late static binding
        $the_object = new $calling_class;
        foreach($result as $the_attribute => $value){
            if($the_object->has_the_attribute($the_attribute)){
                $the_object->$the_attribute = $value;
            }
        }
        return $the_object;
    }
    public function has_the_attribute($the_attribute){
        $object_properties = get_object_vars($this);
        return array_key_exists($the_attribute, $object_properties);
    }

    public static function find_all(){
        $result = static::find_this_query("SELECT * FROM " . static::$table_name . " ORDER BY id DESC" );
        return $result;
    }
    public static function find_by_id($id){
        //binden van parameters= ($user_id)=PREPARED STATEMENTS
        $result = static::find_this_query("SELECT * FROM ". static::$table_name." WHERE id=?",[$id]);
        return !empty($result) ? array_shift($result): false;
    }
    public static function find_by_email($email) {
        $result = static::find_this_query("SELECT * FROM users WHERE email = ?", [$email]);
        return !empty($result) ? array_shift($result) : false;
    }

    /* CRUD */
    public function create() {
        global $database;

        // Tabelnaam ophalen
        $table = static::$table_name;

        // Properties
        $properties = $this->get_properties();

        // ID verwijderen uit de lijst van properties
        if (array_key_exists('id', $properties)) {
            unset($properties['id']);
        }

        // Voeg een created_at timestamp toe
        $properties['created_at'] = date('Y-m-d H:i:s');

        // Waarden beschermen tegen SQL-injecties
        $escaped_values = array_map([$database, 'escape_string'], $properties);

        // Placeholders in prepared statements
        $placeholders = array_fill(0, count($properties), '?');

        // String van veldnamen gescheiden door komma's.
        $fields_string = implode(',', array_keys($properties));

        // Datatypes string
        $types_string = "";
        foreach ($properties as $value) {
            if (is_int($value)) {
                $types_string .= "i";
            } elseif (is_float($value)) {
                $types_string .= "d";
            } else {
                $types_string .= "s";
            }
        }

        // Voorbereide SQL-statement
        $sql = "INSERT INTO $table ($fields_string) VALUES (" . implode(',', $placeholders) . ")";

        // Query uitvoeren en resultaat controleren
        $result = $database->query($sql, $escaped_values);

        if ($result) {
            return true;  // Succesvolle insert
        } else {
            return false;  // Mislukte insert
        }
    }




    public function update(){
        global $database;
        //tabelnaam ophalen
        $table = static::$table_name;
        //properties
        $properties = $this->get_properties();
        unset($properties['id']);
        //waarden beschermen tegen sql injecties
        $escaped_values = array_map([$database,'escape_string'], $properties);

        //append the id value tot the end of the escaped values array
        $escaped_values[]= $this->id;
        $placeholders = array_fill(0,count($properties), '?');

        //een string van alle veldnamen gescheiden door komma's.
        $fields_string = implode(',',array_keys($properties));
        //datatypes string
        $types_string = "";
        foreach($properties as $value){
            if(is_int($value)){
                $types_string .= "i";
            }elseif(is_float($value)){
                $types_string .= "d";
            }else{
                $types_string .= "s";
            }
        }
        //create sql commando, prepared statement (? welk id)
        //$properties=['email',...,'last-name]
        // email = ?, ... last-name = ?

        $sql = "UPDATE $table SET " . implode(', ', array_map(fn($field) => "$field = ?", array_keys($properties))) . " WHERE id = ?";

        //array_map met fn($field) => "$field = ?":
        //
        //Hiermee wordt voor elk veld een correcte field = ?-toewijzing gemaakt.
        //Bijv.: email = ?, password = ?, first_name = ?, last_name = ?.

        //execute
        $database->query($sql,$escaped_values);
    }

    public function delete(){
        global $database;
        $table = static::$table_name;
        $escaped_id = $database->escape_string($this->id);

        $sql = "DELETE FROM $table WHERE id = ?";
        $params = [$escaped_id];
        $database->query($sql,$params);
    }
    public function soft_delete(){
        global $database;
        $table = static::$table_name;
        $escaped_id = $database->escape_string($this->id);
        $current_timestamp = date('Y-m-d H:i:s');

        //wissen in de database
        $sql = "UPDATE $table SET deleted_at = '$current_timestamp' WHERE id = ?";
        $params = [$escaped_id];
        $database->query($sql,$params);

        //fysische foto wissen op de server
        $file_path = $this->upload_directory.DS.$this->filename;
        if(file_exists($file_path)){
            unlink($file_path);
        }
    }

    public function save(){
        return isset($this->id) ?$this->update() : $this->create();
    }

    public static function count_all(){
        global $database;
        $sql = "SELECT COUNT(*) FROM ". static::$table_name;
        $result = $database->query($sql);
        $row= $result->fetch_array();
        return array_shift($row);
    }
}