<?php
namespace Core;

require_once('Interfaces/ObserverInterface.php');

use Core\Character;
use Core\Database;
use Core\Interfaces\OBserver;
use Core\Message;

class Avatar extends Character implements Observer{
    protected $id;
    protected $death;
    protected $killed;

    function __construct(){
        
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

    public function register($username, $role){
        $this->init($username,$role);
        $db = $this->DBconnect();
        $db->query("insert into player (name,level,role,money) values ('{$this->name}',{$this->level},'{$this->role}',{$this->money});");
        $playerID =  getID('player','name',$username);
        if($role !== 'Monster'){
            $player= "player";
        }else{
            $player = "monster";
        }
        $db->query("INSERT INTO user_attribute_relationships (user_id,player_or_monster,life,magic,attack,mag,defense,mddf,speed,lucky) VALUES ({$playerID['id']},'{$player}',{$this->attribute['life']},{$this->attribute['magic']},{$this->attribute['attack']},{$this->attribute['mag']},{$this->attribute['defense']},{$this->attribute['mddf']},{$this->attribute['speed']},{$this->attribute['lucky']});");
        $attributeID = getID("user_attribute_relationships",'user_id',$playerID['id']);
        $db->query("UPDATE player SET attribute_id = '{$attributeID['id']}' WHERE id = {$playerID['id']} ;");
    }
    
    public function load($query){
        $message = new Message;
        $header = [
            "level"=>"lv",
            "name"=>"Name",
            "role"=>"Role",
        ];
        $old_records = $this->operate_DB($query,'all');
        usort($old_records, function ($a, $b) { return strlen($b['name']) - strlen($a['name']); }); # 把名字最長的放在最陣列前面方便後面的排版
        [$selectOption,$allOption] = $message->printTable($header,$old_records,"全新角色");
        return [$selectOption,$allOption,$old_records];
    }

    public function reload($playerData){
        $config = getSetting('Database');
        $db =  new Database($config);
        
        $this->id = $playerData['id'];
        $this->level = $playerData['level'];
        $this->name = $playerData['name'];        
        $this->role = $playerData['role'];
        $this->money = $playerData['money'];
        $this->exp = $playerData['exp'];
        $this->killed = $playerData['killed'];
        $this->death = $playerData['death'];
        
        $playerAttributeId = $playerData['attribute_id'];
        $playerData['attribute'] = ($db->query("Select life,magic,attack,mag,defense,mddf,speed,lucky from  user_attribute_relationships where id = $playerAttributeId")->find_or_fail('one'));
        $this->attribute = $playerData['attribute'];
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
