<?php
require('DbCommandQuery.class.php');

class ActiveRecord{
    protected $primaryKey;
    protected $tableName;
    private $pdoInstance;   
    private $data;
    
    public function __construct(){
        if($this->pdoInstance == null)
            $this->pdoInstance = new DbCommandQuery();
    }
    
    private function getInstance(){
        return $this->pdoInstance;
    }
    
    public function findByPk($primaryKey){
        $this->data = $this->getInstance()
                   ->select()
                   ->from($this->getTableName())
                   ->where("{$this->getPrimaryKey()} = :{$this->getPrimaryKey()}", array(":{$this->getPrimaryKey()}"=>$primaryKey))
                   ->queryObject();
       return $this->data;
    }
    public function insert($columns){
       $insert = $this->getInstance()->insert($this->getTableName(),$columns);
       
       if($insert != 0)
           $this->setPrimaryKey($this->getInstance()->getLastInsertId());
    }
    public function delete(){
        $primaryKey = $this->getPrimaryKey();
        $id = $this->data->$primaryKey;
        return $this->getInstance()
                    ->delete($this->getTableName(), "{$this->getPrimaryKey()} = :{$this->getPrimaryKey()}", array(":{$this->getPrimaryKey()}"=>$id));
        
    }
    private function getPrimaryKey(){
       return $this->primaryKey;
    }
    private function setPrimaryKey($value){
        $key = $this->getPrimaryKey();
        $this->$key = $value;
    }
    
    private function getTableName(){
       return $this->tableName;
    }
    
}

class Usuario extends ActiveRecord {
    protected $tableName = 'users';
    protected $primaryKey = 'id';
    
    
}

$user = new Usuario();
$user->insert(array('username'=>'diegoz', 'password'=>123));
$result = $user->findByPk($user->id);
var_dump($result);
//var_dump($user->id);
/*$users->delete(); */

?>