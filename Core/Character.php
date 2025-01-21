<?php

# parentes class
namespace Core;

use Core\Database;
// [ life => 生命值, magic => 魔力值, attack => 物理攻擊, mag => 魔法攻擊, defense => 物理防禦, mddf=>魔法防禦, speed => 速度, lucky => 幸運值]
class Character{
    protected $level;# 角色等級
    public $name; # 名稱
    protected $role; #角色   // Warrior（戰士），Mage（法師），Priest（牧師），Rogue（盜賊），Paladin（騎士） Monster(怪物)
    public $attribute; # 存放角色屬性
    
    public function get($value){
        return $this->$value;
    }

    public function get_config($data){
        $config = get_setting();
        return $config[$data];
    }

    public function operate_DB($query){
        $db = $this->DBconnect();
        return $db->query($query)->find_or_fail();
    }

    public function DBconnect(){
        $config = $this->get_config('Database');
        return new Database($config);
    }
}
