<?php
namespace Core;

use PDO;

#如果檔案只包含class，慣例會把檔案名稱第一個字大寫
class Database{
    public $connection;
    public $statement;

    public function  __construct($config)
    {
        $dsn = "mysql:".http_build_query($config, "", ";");#print host=msyql;port=3306;dbname=test_app;charset=utf8mb3

        $this->connection = new PDO($dsn,$config['mysql_user'],$config['mysql_pass'],[
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }
    
    #把要綁定的參數傳進$params裡面
    public function query($query, $params = []){
        #執行sql語句
        $this->statement =  $this->connection->prepare($query);
        $this->statement->execute($params); 
        
        return $this;
    }

    public function find(){
        return $this->statement->fetch();
    }
    public function findall(){
        return $this->statement->fetchALL();
    }
    public function find_or_fail(){
        $result = $this->find();
        if(!$result){
            return "";
        }
        return $result;
    }
}
