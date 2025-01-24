<?php

namespace Core;

require_once('Interfaces/EquipmentInterface.php');

use Core\Interfaces\Equipment;
use Core\CreateAx;
// use Core\CreateWand;
// use Core\CreateDagger;

class Weapon implements Equipment{
    public $user;#用來存放class avatar 或是 monster的資料
    
    public function __construct($user)
    {
        $this->user = $user;
    }

    public static function create($user){
        if($user->get('role') === 'Mage'){
            return new CreateWand();
        }elseif($user->get('role') === 'Warrior'){
            return new CreateAx();
        }elseif($user->get('role') === 'Rogue'){
            return new CreateDagger();
        }
    }
    public function belongTo($user){}

    public function changeOwner($newOwner){}

    public function limitCheck($user){}

    public function effect(){}
}
