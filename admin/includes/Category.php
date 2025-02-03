<?php

class Category extends Db_object
{
    //properties
    //public,private, protected
    public $id;
    public $name;
    public $created_at;
    public $deleted_at;

    protected static $table_name = 'categories';
    //methods


    /* CRUD */
    /*properties als array voorzien*/
    public function get_properties(){
        return[
            'id'=> $this->id,
            'name'=>$this->name,
            'created_at'=>$this->created_at,
            'deleted_at'=>$this->deleted_at
        ];
    }

}