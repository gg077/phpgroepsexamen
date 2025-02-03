<?php
    function classAutoLoader($class){//User
        $class = strtolower($class); //user
        $the_path= "includes/{$class}.php";

        if(is_file($the_path)){
            if(file_exists($the_path) && !class_exists($class)){
                include($the_path);
            }else{
                die("This file name {$class}.php was not found!");
            }
        }
    }
    spl_autoload_register('classAutoLoader');
?>