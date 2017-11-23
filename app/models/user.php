<?php

class User extends BaseModel {
    
    public $id, $tunnus, $salasana;
    
    public function __construct($attributes = null) {
        parent::__construct($attributes);
    }
    
    public static function find($id){
        $query = DB::connection()->prepare('SELECT * FROM Kayttaja WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();
        
        if($row){
            return new User(array('id' => $row['id'], 'tunnus' => $row['tunnus']));
        }
        
        return null;
    }
    
    public static function authenticate($tunnus, $salasana){
        //$password = crypt($password);
        $query = DB::connection()->prepare('SELECT * FROM Kayttaja WHERE tunnus = :tunnus AND salasana = :salasana LIMIT 1');
        $query->execute(array('tunnus' => $tunnus, 'salasana' => $salasana));
        $row = $query->fetch();
        
        if($row){
            return new User(array('id' => $row['id'], 'tunnus' => $row['tunnus']));
        }
        
        return null;
    }
    
}
