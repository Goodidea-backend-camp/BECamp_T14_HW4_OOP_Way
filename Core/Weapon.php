<?php

namespace Core;

require_once('Interfaces/EquipmentInterface.php');

use Core\Interfaces\Equipment;
use Core\Database;

class Weapon implements Equipment{
    public $user;#用來存放class avatar 或是 monster的資料
    protected $weapon;
    
    public function __construct($user)
    {
        $this->user = $user;
    }

    public static function create($user){
        if($user->get('role') === 'Mage'){
            $weapon = new CreateWand($user);
        }elseif($user->get('role') === 'Warrior'){
            $weapon = new CreateAx($user);
        }elseif($user->get('role') === 'Rogue'){
            $weapon =  new CreateDagger($user);
        }

        return $weapon;
    }
    
    public function belongTo($weapon){
        $weapon = (array)$weapon; 
        $value = "'".$weapon['result']['name']."','".$weapon['result']['type']."','".$weapon['result']['rarity']."','".$weapon['result']['attack']."','".$weapon['result']['money']."','".$weapon['result']['level_requirement']."','".$weapon['result']['charactor_requirement']."'";
        $dbConfig = getSetting("Database");
        $db = new Database($dbConfig);
        $db->query("INSERT INTO weapon (name, type, rarity, attack, money, level_requirement, charactor_requirement) VALUES ($value)");
        
        $weaponID = $db->query("SELECT id FROM weapon WHERE name='".$weapon['result']['name']."'")->find_or_fail('one');
        $userID = $db->query("SELECT id FROM player WHERE name='".$this->user->get('name')."'")->find_or_fail('one');
        $db->query("INSERT INTO player_weapon_relationships (player_id, weapon_id) VALUES (".$userID['id'].",".$weaponID['id'].")");
        
        if($weapon['result']['specialEffects'] !== NULL){
            foreach($weapon['result']['specialEffects'] as $specialEffects){
                $specialEffect = $specialEffects['id'];
                $db->query("INSERT INTO weapon_special_relationships (weapon_id, special_id) VALUES (".$weaponID['id'].",$specialEffect)");
            }
        }
    }

    public function changeOwner($newOwner){}

    public function limitCheck($user){}

    public function effect(){}
}
