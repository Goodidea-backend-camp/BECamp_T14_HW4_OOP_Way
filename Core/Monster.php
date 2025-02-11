<?php
namespace Core;

use Core\Character;

class Monster extends Character{
    # * 這邊先以建構子跑新建的流程之後會變成呼叫的函式
    # todo 要把拿player id的拆出去變成獨立函式
    function __construct(){
        // $query = "select * from player where name='$player_name';";
        // $player = operate_DB($query);
        // $this->generate($this->level,$this->role,$player['level']);
        // $attribute_json = json_encode($this->attribute);
        // $reward_json = json_encode($this->reward);
        // if(!empty($player)){
        //     $db->query("insert into monster (name,level,attribute,player_id,reward) values ('{$this->name}','{$this->level}','{$attribute_json}',{$player['id']},'{$reward_json}');");
        // }
    }
    
    public function generate($monster_level,$player_name){
        $attribute_percent = getSetting('attribute_percent');
        $player = $this->operate_DB("select * from player where name='$player_name';",'one');
        # 注意：這邊需要有player data才能使用，因為不想一直寫入所以註冊那邊的SQL指令都先注解掉，用舊角色的功能去測試這段
        while($this->name === NULL){
            $name = $this->rand_name();
            $result = $this->operate_DB("select * from monster where name='$name';",'one');
            if($result === ""){
                $this->name = $name;
            }
        }
        $this->level = $player['level']+$monster_level;
        $this->role = 'Monster';

        $point = calculate_total_point($this->role,$this->level);
        $this->attribute = response_points($attribute_percent[$player['role']],$point,$player['role']);
        
        // 經驗值 = B × ( 1 + M/10 ) x ( 1 + lv-player/lv-monster)
        // 金錢獎勵=fibonacci(lv-monster)
        $this->money = fibonacci($this->level);
        $this->exp = ceil(50*(1+1/10)*(1+$player['level']/$this->level));
    }

    public function register($monster, $role,$playerName){
        $db = $this->DBconnect();
        $attribute = $monster['attribute'];
        $playerID=getId('player','name',$playerName);
        $db->query("insert into monster (name,level,player_id,exp,money) values ('{$monster['name']}',{$monster['level']},'{$playerID['id']}','{$monster['exp']}',{$monster['money']});");
        $monsterID =  getID('monster','name',$monster['name']);
        if($role !== 'Monster'){
            $player= "player";
        }else{
            $player = "monster";
        }
        $db->query("INSERT INTO user_attribute_relationships (user_id,player_or_monster,life,magic,attack,mag,defense,mddf,speed,lucky) VALUES ({$monsterID['id']},'{$player}',{$attribute['life']},{$attribute['magic']},{$attribute['attack']},{$attribute['mag']},{$attribute['defense']},{$attribute['mddf']},{$attribute['speed']},{$attribute['lucky']});");
        $attributeID = getID("user_attribute_relationships",'user_id',$monsterID['id']);
        $db->query("UPDATE monster set attribute_id = '{$attributeID['id']}' WHERE id = {$monsterID['id']};");
    }

    public function rand_name(){
        $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $monster_name = "";
        for ( $i = 0; $i < 6 ; $i++ ){   
            if($i < 4){
                $monster_name .= $chars[mt_rand(10, strlen($chars)-1)];
            }elseif($i===4){
                $monster_name .= '-'.$chars[mt_rand(0, 9)];
            }else{
                $monster_name .= $chars[mt_rand(0, 9)];
            }
        }
        return $monster_name;
    }
}
