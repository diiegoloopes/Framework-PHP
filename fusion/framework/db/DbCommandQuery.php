<?php
class DbConnection{
    private static $connection = null;
    private static $dsn = 'mysql:dbname=test;host=localhost';
    private static $username = 'root';
    private static $password = '';
    
    private static function connect($dsn, $username, $password){
        try{
            return new PDO($dsn, $username, $password);
        } catch(PDOException $e){
            echo sprintf('Error: %s', $e->getMessage());
        } 
    }
    
    public static function disconnect(){
        self::$connection = null;
    }
    
    public static function getConnection(){
        if(self::$connection == null){
            self::$connection = self::connect(self::$dsn, self::$username, self::$password);
        }
        return self::$connection;
    }
}
/* 
 * Command Query Singleton BETA
 * 
 * A class inicia com um objeto singleton , tornando-a capaz de manter somente
 * uma conexão com o Banco de Dados por vez, muito útil para ser utilizada
 * em aplicações single-threaded. Leia mais sobre singleton {@link http://php.net/manual/en/language.oop5.patterns.php}
 * 
 * Command Query tem por objetivo criar Queries com uma maior velocidade, praticidade e inteligência.
 * 
 * <b>Iniciando:</b>
 * 
 * <code>
 *      $db = new DbCommandQuery();
 * </code>
 * 
 * @author Diego Lopes do Nascimento
 * @copyright (c) 2012 - Diego L. do Nascimento <diiego.lopes01@gmail.com>
 * @version 1.0
 * @license BSD
 */
class DbCommandQuery{
    /*
     * Instância do objeto PDO {@link new PDO()}
     * 
     * @var object
     */
    private $pdoInstance;
    /*
     * @var string
     */
    private $sql;
    /*
     * @var array
     */
    private $params=array();
    /*
     * Flag que controla a amostragem de erros na tela
     * para o usuário.
     * 
     * @var bool
     */
    public static $isDebugging = true;
    
    /*
        * Get Instance
     * 
     * Retorna a variável que contem o objeto PDO.
     * 
     * @return object
     */
    private function getInstance(){
        if($this->pdoInstance == null){
            $this->pdoInstance = DbConnection::getConnection(); // realiza a conexão usando singleton
        }
        return $this->pdoInstance;
    }
    /*
     * GetLastInsertId
     * 
     * Retorna o último ID inserido através do PDO::lastInsertId {@link PDO::lastInsertId} método.
     * 
     * @return integer
     */
    public function getLastInsertId(){
        return $this->getInstance()->lastInsertId();
    }
    /*
     * Query
     * 
     * Executa a QUERY com o PDOStatement e retorna o objeto já tradado, 
     * tornando possível ser aproveitar alguns métodos da própria class PDO como output.
     * 
     * <code>
     *      $db = new DbCommandQuery();
     *      $users = $db->query("SELECT * FROM users")->fetchAll(); {@link PDO::fetchAll()}
     *      var_dump($users); // Imprimirá um array com múltiplos registros.
     *      
     * </code>
     * 
     * @param string
     * @return object
     */
    public function query($sql){
        return $this->queryInternal($sql);
    }
    /*
     * Delete
     * 
     * Executa o DELETE statement especificado na query e retorna o valor 
     * em inteiro com a mesma funcionalidade da função {@link mysql_affected_rows}
     * 
     * <code>
     *      $db = new DbCommandQuery();
     *      $delete = $db->delete('nome_da_tabela', 'id = :id', array(':id' => 10));
     *      var_dump($delete);
     * 
     *      // Se for bem sucedido, a saída será 1 , senão 0.
     * </code>
     * 
     * @param string
     * @param string
     * @param array
     * @return integer
     */
    public function delete($tableName, $condition = null, $params=array()){
        
        if(!is_null($condition))
            $condition = "WHERE {$condition}";
        
        $sqlStatement = "DELETE FROM {$tableName} {$condition}";
        
        $statement = $this->queryInternal($sqlStatement, $params);
        return @$statement->rowCount(); 
    }
    /*
     * Insert
     * 
     * Executa o INSERT statement especificado na query e retorna o valor 
     * em inteiro com a mesma funcionalidade da função {@link mysql_affected_rows}
     * O parâmetro $columns deve ser com o formato array('column'=>'value').
     * 
     * <code>
     *      $db = new DbCommandQuery();
     *      $insert = $db->insert('nome_da_tabela', array('nome' => 'Diego', 'idade'=>18));
     *      var_dump($insert);
     *      // Se for bem sucedido, a saída será 1 , senão 0.
     * </code>
     * 
     * @param string
     * @param array
     * @return integer
     */
    public function insert($tableName, array $columns){
        $prepare = array();
        foreach($columns as $key=>$value)
            $prepare[':'.$key] = $value;
        $values = implode(',',array_keys($prepare));
        $columnsInternal = implode(',', array_keys($columns));
        
        $sqlStatement = "INSERT INTO {$tableName} ({$columnsInternal}) VALUES({$values})";
        
        $statement = $this->queryInternal($sqlStatement, $columns);
        
        return @$statement->rowCount();
    }
    /*
     * Insert
     * 
     * Executa o UPDATE statement especificado na query e retorna o valor 
     * em inteiro com a mesma funcionalidade da função {@link mysql_affected_rows}
     * 
     * <code>
     *      $db = new DbCommandQuery();
     *      $update = $db->update('nome_da_tabela', array('nome'=>'Diego', 'id = :id', array(':id'=>1)));
     *      var_dump($update);
     *      // Se for bem sucedido, a saída será 1 , senão 0.
     * </code>
     * 
     * @param string
     * @param array
     * @param string
     * @param array
     * @return integer
     */
    public function update($tableName, array $columns, $condition = null, $params = array()){
        $columnsInternal = array();
        $paramsInternal = array();
        foreach($columns as $key => $value){
            $columnsInternal[] = sprintf('%s = %s',$key,':'.$key);
            $paramsInternal[':'.$key] = $value;
        }
        if(count($params)>0 && !is_null($condition)){
            foreach($params as $key=>$value)
                $paramsInternal[$key] = $value;
        }
        
        if(!is_null($condition))
            $condition = 'WHERE '.$condition;
        
        $columns = implode(',', $columnsInternal);
        $sqlStatement = "UPDATE {$tableName} SET {$columns} {$condition}";
        
        $statement = $this->queryInternal($sqlStatement, $paramsInternal);
        
        return @$statement->rowCount();
    }
    /*
     * Select
     * 
     * Concatena parte do SELECT statement à variável publica SQL como um fragmento, possibilitando
     * o uso do método chaining.
     * 
     * <code>
     *      $db = new DbCommandQuery();
     *      $db->select('usuario, senha');
     *      // ou $db->select(array('usuario', 'senha'));
     * </code>
     * 
     * @param mixed
     * @return object
     */
    public function select($columns='*'){
        if(is_array($columns))
            $columns = implode(', ',$columns); 
        
        $this->sql = "SELECT {$columns} ";
        return $this;
    }
    /*
     * From
     * 
     * @param string
     * @param string
     * @return object
     */
    public function from($tableName,$alias = 't'){
        $this->sql .= " FROM {$tableName} AS {$alias} ";
        return $this;
    }
    /*
     * Where
     * 
     * @param string
     * @param array
     * @return object
     */
    public function where($condition, $params = array()){
        $this->sql .= " WHERE {$condition} ";
        
        if(count($params)>0)
            $this->params = $params;
        
        return $this;
    }
    /*
     * Ordery By
     * 
     * @param string
     * @return object
     */
    public function orderBy($expression){
        $this->sql .= " ORDER BY {$expression}";
        return $this;
    }
    /*
     * Join
     * 
     * Concatena parte do INNER JOIN statement à variável publica SQL como um fragmento, possibilitando
     * o uso do método chaining.
     * 
     * <code>
     *      $db = new DbCommandQuery();
     *      $db->select('usuario, senha')
     *         ->from('users'),
     *         ->join('usersMeta AS um', 'um.id = t.id')
     * </code>
     * 
     * @param string
     * @param string
     * @return object
     */
    public function join($firstExpression, $secondExpression){
        $this->sql .= " INNER JOIN {$firstExpression} ON ({$secondExpression})";
        return $this;
    }
    /*
     * Left Join
     * 
     * @param string
     * @param string
     * @return object
     */
    public function leftJoin($firstExpression, $secondExpression){
        $this->sql .= " LEFT JOIN {$firstExpression} ON ({$secondExpression})";
        return $this;
    }
    /*
     * Right Join
     * 
     * @param string
     * @param string
     * @return object
     */
    public function rightJoin($firstExpression, $secondExpression){
        $this->sql .= " RIGHT JOIN {$firstExpression} ON ({$secondExpression})";
        return $this;
    }
    /*
     * Limit
     * 
     * @param integer
     * @param integer
     * @return object
     */
    public function limit($offset = 1, $numrows = 1){
        $this->sql .= " LIMIT {$offset}, {$numrows}";
        return $this;
    }
    /*
     * Query All
     * 
     * Método que retorna múltiplos registros de uma query. Este método é muito
     * útil quando é necessário ter múltiplos registros. Possui a mesma funcionalidade 
     * do método queryAll {@link PDO::fetchAll()}.
     * 
     * <code>
     *      $db = new DbCommandQuery();
     *      $users = $db->select('usuario, senha')->from('users')->queryAll();
     *      var_dump($users); // A saída será um array com vários resultados dentro.
     * </code>
     * 
     * @return array
     */
    public function queryAll(){
        return $this->queryInternal($this->sql, $this->params)->fetchAll();
    }
    /*
     * Query Row
     * 
     * Método que retorna um único resultado. Este método é muito
     * útil quando é necessário ter o primeiro registro da consulta. Possui a mesma funcionalidade 
     * do método fech {@link PDO::fech()}.
     * 
     * Você pode usar o parâmetro opcional $assoc, flag que controla se o resultado
     * será retornado em um array com somente índices {@link mysql_fetch_assoc()} ou com resultado
     * mistos {@link mysql_fetch_row()}
     * 
     * <code>
     *      $db = new DbCommandQuery();
     *      $user = $db->select('usuario, senha')->from('users')->queryRow();
     *      var_dump($user); // A saída será um array com vários resultados dentro.
     * </code>
     * 
     * @return array
     */
    public function queryRow($assoc = false){
        $sth = $this->queryInternal($this->sql, $this->params);
        return $assoc == false ? @$sth->fetch() : @$sth->fetch(PDO::FETCH_ASSOC);
    }
    /*
     * Query Object
     * 
     * Método que retorna um único resultado. Este método é muito
     * útil quando é necessário ter o primeiro registro da consulta. Possui a mesma funcionalidade 
     * do método fetch {@link PDO::fetch(FETCH::OBJ)}.
     * 
     * <code>
     *      $db = new DbCommandQuery();
     *      $user = $db->select('usuario, senha')->from('users')->queryObject();
     *      var_dump($user->username); // A saída será um objeto
     * </code>
     * 
     * @return object
     */
    public function queryObject(){
        return @$this->queryInternal($this->sql, $this->params)->fetch(PDO::FETCH_OBJ);
    }
    
    public function queryColumns(){
        return @$this->queryInternal($this->sql, $this->params)->fetch(PDO::FETCH_COLUMNS);
    }
    /*
     * Query Internal
     * 
     * Executa a QUERY a partir do formato que o PDO recebe, com ou sem shortcutes.
     * Retorna o prepared statement da class PDO Statement.
     * 
     * Será retornado um erro, caso a Query seja mal executada por diversos motivos.
     * 
     * O primeiro parâmetro deverá ser passado da seguinte forma:
     * <code>
     *      $sqlStatement = "INSERT INTO users (usuario, senha) VALUES (:usuario, :ssenha)";
     * </code>
     * E o segundo parâmetro deverá ser passado da seguinte forma:
     * <code>
     *      $sqlStatement = "INSERT INTO users (usuario, senha) VALUES (:usuario, :ssenha)";
     *      $params = array(':usuario'=>'dizinho', ':senha'=>'123');
     * </code>
     * 
     * @param string
     * @param array
     * @return object
     */
    private function queryInternal($sqlStatement, $params = array()){
        try {
            if(self::$isDebugging == true)
                $this->getInstance()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $statement = $this->getInstance()->prepare($sqlStatement);
            $this->bindArrayParams($statement, $params);
            
            if($statement->execute())
                DbConnection::disconnect(); // Fecha a conexão pós sucesso na Query, utilizado pra otimizar query
            
        } catch(PDOException $e){
            echo sprintf('Error: %s',$e->getMessage());
        }
        
        return $statement;
    }
    /*
     * Get Param Type
     * 
     * Analisa o valor e retorna o tipo do dado que esse valor possui.
     * Esse método é complemento do método {@link PDO::bindParam}, como
     * terceiro parâmetro.
     * {@see http://php.net/manual/en/pdostatement.bindvalue.php}
     * 
     * @param string
     * @return mixed
     */
    private function getParamType($param){
        $paramType = null;
        
        if(is_null($param) || empty($param))
            $paramType = PDO::PARAM_NULL;
        else if(is_int($param) || is_numeric($param))
            $paramType = PDO::PARAM_INT;
        else if(is_bool($param))
            $paramType = PDO::PARAM_BOOL;
        else if(is_string($param)) {
            $paramType = PDO::PARAM_STR;
        } else
            $paramType = false;
        return $paramType;
    }
    /*
     * Bind Array Params
     * 
     * Faz a junção entre um parâmetro e um valor, escapandoos com 
     * seus respectivos tipos de dados.
     * 
     * entrada: array('nome'=>'Diego')
     * saída: array(':nome'=>'Diego');
     * 
     * @param object PDO statement já com o SQL Statement preparado com placeholders.
     * @param array Um array com seus placeholders e seus respectivos valores.
     * @return void
     */
    private function bindArrayParams(&$statement, $params){
        if(is_object($statement) && $statement instanceof PDOStatement){
            foreach($params as $param => $value){
                $param = ':'.str_replace(':','',$param);
                $statement->bindValue($param, trim($value), $this->getParamType($value));
            }
        }
    }
}

$db = new DbCommandQuery();

$db->insert('users', array('username'=>'diziinho', 'password'=>123));
$id = $db->getLastInsertId();
echo $db->update('users', array('username'=>'dez'), 'id = :id', array(':id'=>$id));
echo $db->delete('users', 'id = :id', array(':id'=>$id));
$user = $db->select()->from('users')->where('id = :id', array(':id' => $db->getLastInsertId()))->queryObject();


/*$insert=$db->insert('users', array('username'=>'dizinho', 'password'=>123));
var_dump($insert); 
$update = $db->update('users', array('username'=>'Janete'), 'id = :id', array(':id'=>9));
var_dump($update); 
$delete = $db->delete('users', 'id = :id', array(':id'=>9));
var_dump($delete);
$results = $db->select('username, password')
   ->from('users')
   ->where('username = :username', array(':username'=>'dizinho'))
   ->orderBy('username ASC')
   ->queryAll();

foreach($results as $result){
    echo sprintf('username: %s | password: %s <br/>', $result['username'], $result['password']);
} */
?>