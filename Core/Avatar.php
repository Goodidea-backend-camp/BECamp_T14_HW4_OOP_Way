<?php
namespace Core;

require_once('Interfaces/ObserverInterface.php');

use Core\Character;
use Core\Database;
use Core\Interfaces\OBserver;

class Avatar extends Character implements Observer{
    protected $id;
    protected $money;
    protected $death;
    protected $exp;
    protected $killed;

    function __construct(){
        // $this->init($username,$role);
        // $db = $this->DBconnect();
        // $attribute_json = json_encode($this->attribute);
        // $db->query("insert into player (name,level,role,attribute,money) values ('{$this->name}',{$this->level},'{$this->role}','{$attribute_json}',{$this->money});");
    }

    public function init($username,$role){
        $attribute_percent = getSetting('attribute_percent');
        $this->name = $username;
        $this->level = 1;
        $this->role = $role;
        $this->money = 50;
        $this->death = 0;
        $point = calculate_total_point($this->role,$this->level);
        $this->attribute = response_points($attribute_percent[$role],$point,$role);
    }
    
    public function load($query){
        $number = 0;
        $old_records = $this->operate_DB($query,'all');
        // $old_records = $this->load('select * from player');
        echo str_pad( 'Options', 10 ).str_pad( 'Lv', 10 ) . str_pad( 'Name', 10 ) . str_pad('Role', 10 ) . "\n";
        foreach($old_records as $record){
            $number+=1;
            echo str_pad( $number, 10 ).str_pad( $record['level'], 10 ) . str_pad( $record['name'], 10 ) . str_pad( $record['role'], 10 ) . "\n";
        }
        $number+=1;
        echo str_pad( $number, 10 ). '全新角色' . "\n";
        return [$number,$old_records];
    }

    public function reload($playerData){
        $this->id = $playerData['id'];
        $this->level = $playerData['level'];
        $this->name = $playerData['name'];        
        $this->role = $playerData['role'];
        $this->attribute = json_decode($playerData['attribute'],1);
        $this->money = $playerData['money'];
        $this->exp = $playerData['exp'];
        $this->killed = $playerData['killed'];
        $this->death = $playerData['death'];
        return [$this->name,$this->role];    
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
