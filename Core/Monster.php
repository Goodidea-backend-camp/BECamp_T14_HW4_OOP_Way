<?php
namespace Core;

use Core\Character;

class Monster extends Character{
    protected $reward = array();

    # $player_level
    # * 這邊先以建構子跑新建的流程之後會變成呼叫的函式
    # todo Avator那邊要在確認是否已經註冊＋要把拿player id的拆出去變成獨立函式，moster要在加id欄位
    function __construct($player_name){
        $this->level = 20;
        $this->role = 'Monster';
        $db = $this->DBconnect();
        $player =  $db->query("select * from player where name='$player_name';")->find_or_fail();
        $this->generate($this->level,$this->role,$player['level']);
        $attribute_json = json_encode($this->attribute);
        $reward_json = json_encode($this->reward);
        if(!empty($player)){
            $db->query("insert into monster (name,level,attribute,player_id,reward) values ('{$this->name}','{$this->level}','{$attribute_json}',{$player['id']},'{$reward_json}');");
        }
    }
    
    public function generate($level,$role,$player_lv){
        $this->name = $this->rand_name();
        $point = calculate_total_point($this->role,$this->level);
        $this->attribute = response_points($this->init[$role],$point,$role);
        // 經驗值 = B × ( 1 + M/10 ) x ( 1 + lv-player/lv-monster)
        // 金錢獎勵=G×(1+ lv-monster/20)
        $this->reward['money'] = ceil(200*(1+$level/20));
        $this->reward['exp'] = ceil(50*(1+1/10)*(1+$player_lv/$level));
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
