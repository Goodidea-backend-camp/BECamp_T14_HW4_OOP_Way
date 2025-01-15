<?php

#把設定的函式抽出來獨立一個檔案，並切做切分讓這邊的東西以後可以依據不一樣的設定進行擴充
function get_setting(){
    return [
        "Database"=>[
            "host" => "mysql",
            "port" => 3306,
            "dbname" => "rpg",
            "charset" => "utf8mb4",
            "mysql_user" => 'root',
            'mysql_pass'=> 'root',
        ],
    ];
}

