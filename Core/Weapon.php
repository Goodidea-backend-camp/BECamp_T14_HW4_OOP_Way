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

    public static function create($user,$level){
        if($user->get('role') === 'Mage'){
            $weapon = new CreateWand($user,$level);
        }elseif($user->get('role') === 'Warrior'){
            $weapon = new CreateAx($user,$level);
        }elseif($user->get('role') === 'Rogue'){
            $weapon =  new CreateDagger($user,$level);
        }
        $weapon = (array)$weapon;

        return $weapon['result'];
    }
    
    public function belongTo($weapon){
        $weapon = (array)$weapon; 
        $value = "'".$weapon['name']."','".$weapon['type']."','".$weapon['rarity']."','".$weapon['attack']."','".$weapon['money']."','".$weapon['level_requirement']."','".$weapon['charactor_requirement']."'";
        $dbConfig = getSetting("Database");
        $db = new Database($dbConfig);
        $db->query("INSERT INTO weapon (name, type, rarity, attack, money, level_requirement, charactor_requirement) VALUES ($value)");
        
        $weaponID = $db->query("SELECT id FROM weapon WHERE name='".$weapon['name']."'")->find_or_fail('one');
        $userID = $db->query("SELECT id FROM player WHERE name='".$this->user->get('name')."'")->find_or_fail('one');
        $db->query("INSERT INTO player_weapon_relationships (player_id, weapon_id) VALUES (".$userID['id'].",".$weaponID['id'].")");
        
        if($weapon['specialEffects'] !== NULL){
            foreach($weapon['specialEffects'] as $specialEffects){
                $specialEffect = $specialEffects['id'];
                $db->query("INSERT INTO weapon_special_relationships (weapon_id, special_id) VALUES (".$weaponID['id'].",$specialEffect)");
            }
        }
    }

    public function changeOwner($newOwner){}

    public function limitCheck($user){}

    public function effect(){}
}
