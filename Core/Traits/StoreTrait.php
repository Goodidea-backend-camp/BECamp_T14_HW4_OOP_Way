<?php

namespace Core\Traits;

use Core\Database;
use Core\Weapon;
use Core\Message;

trait Store{
    public function dbConnect(){
        $dbConfig = getSetting("Database");
        return new Database($dbConfig);
    }

    public function provide($player){
        $message = new Message();
        $db = $this->dbConnect();
        $playerLevel = $player->get('level');
        $playerRole = $player->get('role');
        $result = $db->query('SELECT weapon_id FROM player_weapon_relationships WHERE player_id = 0')->find_or_fail('all');
        $result = "(".implode(',', array_column($result, 'weapon_id')).")";
        $products = $db->query("SELECT id,name,type,rarity,attack,money,level_requirement,charactor_requirement FROM weapon WHERE id IN $result AND (level_requirement BETWEEN $playerLevel-1 AND $playerLevel+5) AND charactor_requirement = '$playerRole'")->find_or_fail('all');
        $products = $products === "" ? [] : $products;
        $headers = [
            "name" => "Item Name",
            "attack" => "Attack",
            "money" => "Money",
            "level_requirement" => "Level Req",
            "charactor_requirement" => "Char Req"
        ];

        $generateProduct = $this->generateProduct($player,3-count($products));
        $time = 3-count($products);
        for($i=0; $i<$time; ++$i){
            array_push($products,$generateProduct[$i]);
        }
        
        usort($products, function ($a, $b) { return strlen($b['name']) - strlen($a['name']); }); # 把名字最長的放在最陣列前面方便後面的排版
        $message->printTable($headers,$products);
    }

    public function sell(){}
    public function buy(){}

    public function generateProduct($player,$time){
        $products = array();
        $weapon = new Weapon($player);
        for($i=0;$i<$time;$i++){
            $generateWeapon = (array)$weapon->create($player,$player->get('level')+rand(0,5));
            array_push($products, $generateWeapon);
        }
        return $products;
    }
}
