<?php

#把設定的函式抽出來獨立一個檔案，並切做切分讓這邊的東西以後可以依據不一樣的設定進行擴充
function getSetting($data){
    $config = [
        "Database"=>[
            "host" => "mysql",
            "port" => 3306,
            "dbname" => "rpg",
            "charset" => "utf8mb4",
            "mysql_user" => 'root',
            'mysql_pass'=> 'root',
        ],
        "attribute_percent"=>[
            # 各個職業的點數分配比例
            # 'life','magic','attack','mag','defense','mddf','speed','lucky' 的百分比
            'Warrior' => [30,5,25,5,20,5,5,5],
            'Mage' => [10,30,5,25,10,10,5,5],
            'Priest' => [10,30,5,30,5,10,5,5],
            'Rogue' => [15,10,15,10,10,5,30,5],
            // 'Paladin' => ['life'=>10,'magic'=>10,'attack'=>10,'mag'=>10,'defense'=>10,'mddf'=>10,'speed'=>10,'lucky'=>10],
            'Paladin' => [30,5,20,5,25,5,5,5],
            'Monster' => [20,30,5,25,5,10,5,0],
        ],
    ];
    return $config[$data];
}

