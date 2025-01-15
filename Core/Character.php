<?php

# parentes class
namespace Core;

use Core\Database;
// [ life => 生命值, magic => 魔力值, attack => 物理攻擊, mag => 魔法攻擊, defense => 物理防禦, mddf=>魔法防禦, speed => 速度, lucky => 幸運值]
class Character{
    protected $level;# 角色等級
    protected $name; # 名稱
    protected $role; #角色   // Warrior（戰士），Mage（法師），Priest（牧師），Rogue（盜賊），Paladin（騎士） 
    protected $attribute; # 存放角色屬性

    # 各個職業的點數分配比例
    protected $init = [
        'Warrior' => [30,5,25,5,20,5,5,5],
        'Mage' => [10,30,5,25,10,10,5,5],
        #'Priest' => ['life'=>10,'magic'=>10,'attack'=>10,'mag'=>10,'defense'=>10,'mddf'=>10,'speed'=>10,'lucky'=>10],
        'Rogue' => [15,10,15,10,10,5,30,5],
        #'Paladin' => ['life'=>10,'magic'=>10,'attack'=>10,'mag'=>10,'defense'=>10,'mddf'=>10,'speed'=>10,'lucky'=>10],
        'Monster' => [20,30,5,25,5,10,5,0],
    ];

    public function get($value){
        return $this->$value;
    }

    public function DBconnect(){
        $config = get_setting();
        return new Database($config['Database']);
    }
}
