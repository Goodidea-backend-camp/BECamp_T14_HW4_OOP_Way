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
        [$option,$lastItemNumber] = $message->printTable($headers,$products,"離開商店");

        if($option === $lastItemNumber){
            return "@w@";
        }else{
            $this->sell($player,$products[$option-1]);
        }
        
        dd($option);
    }

    #從商店賣出給玩家
    public function sell($player,$weapon){
        $db = $this->dbConnect();
        $weaponID = $weapon['id'] ?? NULL;
        $playerID = $player->get('id');
        if($player->get('money')<$weapon['money']){
            echo "你錢不夠喔";
            return;
        }elseif($weaponID !== NULL){
            $db->query("UPDATE player_weapon_relationships SET player_id = $playerID  WHERE weapon_id = $weaponID");
        }else{
            $weaponS = new Weapon($player);
            $weaponS->belongTo($weapon);
        }
        $money = $player->get('money') - $weapon['money'];
        $db->query("UPDATE player SET money = $money  WHERE id = $playerID");
        dd($playerID);
    }
    
    #從玩家手中買進
    public function buy(){

    }

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
