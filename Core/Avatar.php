<?php
namespace Core;

require_once('Interfaces/ObserverInterface.php');

use Core\Character;
use Core\Interfaces\OBserver;

class Avatar extends Character implements Observer{ //implements Observer{
    protected $money;
    protected $death_time;

    function __construct($username,$role){
        $this->init($username,$role);
        // $db = $this->DBconnect();
        // $attribute_json = json_encode($this->attribute);
        // $db->query("insert into player (name,level,role,attribute,money) values ('{$this->name}',{$this->level},'{$this->role}','{$attribute_json}',{$this->money});");
    }

    public function init($username,$role){
        $setting = get_setting();
        $attribute_percent = $setting['attribute_percent'];
        $this->name = $username;
        $this->level = 1;
        $this->role = $role;
        $this->money = 50;
        $this->death_time = 0;
        $point = calculate_total_point($this->role,$this->level);
        $this->attribute = response_points($attribute_percent[$role],$point,$role);
    }
    
    public function update($message){
        echo $message;
    }

    #todo : 暫時先把復活的邏輯寫下，後續要再依照情況調整
    // public function revival($money,$deth_time){
    //     if($money < pow(2,($deth_time-1))){
    //         $this->deth_time+=1;
    //     }else{
    //         $revial_or_not = readline("你能進行復活，是否要進行!");
    //         if($revial_or_not === 'yes'){
    //             $this->money -= pow(2,($deth_time-1));
    //         }else{
    //             $this->deth_time+=1;
    //         }
    //     }
    // }
}
