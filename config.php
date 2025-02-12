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
            // 'Paladin' => ['life'=>10,'magic'=>10,'attack'=>10,'mag'=>10,'defense'=>10,'mddf'=>10,'speed'=>10,'lucky'=>10]
            # 'life','magic','attack','mag','defense','mddf','speed','lucky' 的百分比
            'Warrior' => [30,5,25,5,20,5,5,5],
            'Mage' => [10,30,5,25,10,10,5,5],
            'Priest' => [10,30,5,30,5,10,5,5],
            'Rogue' => [15,10,15,10,10,5,30,5],
            'Paladin' => [30,5,20,5,25,5,5,5],
            'Monster' => [20,30,5,25,5,10,5,0],
        ],
        "mainMenuHeader"=>[
            "game" => "Select",
        ],
        "mainMenuItem" => [
            0 => [ "game" => "Game" ],
            1 => [ "game" => "Logs" ],
            2 => [ "game" => "Exit" ]
        ],
        "monsterHeader" =>  [
            "name"=>"Name",
            "level"=>"Level",
            "money"=>"Reward Money",
            "exp"=>"Reward Exp",
        ],
        "playerHeader" => [
            "level"=>"lv",
            "name"=>"Name",
            "role"=>"Role",
        ],
        "storeHeader" => [
            "name" => "Item Name",
            "attack" => "Attack",
            "money" => "Money",
            "level_requirement" => "Level Req",
            "charactor_requirement" => "Char Req"
        ],
    ];
    return $config[$data];
}

