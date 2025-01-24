<?php

use Core\Database;

# Debug用
function dd($value){
    var_dump($value);
    die();
}

# 確認使用者是否用command line進入
function check_user_useInterface(){
    if (php_sapi_name() !== 'cli') {
        echo "你不是用Command line @@";        
        exit;
    }
}

# 接收陣列，和總點數回傳計算後完後的數值
function response_points($array,$points,$role){
    $key = array('life','magic','attack','mag','defense','mddf','speed','lucky');
    $attribute = array();
    if($role === 'Monster'){
        for($i=0;$i<count($key);$i++){
            $attribute[$key[$i]] = floor($points*$array[$i]/100);
        }
    }
    for($i=0;$i<count($key);$i++){
        $attribute[$key[$i]] = ceil($points*$array[$i]/100);
    }
    
    return $attribute;
}
# 計算總點數
function calculate_total_point($role,$level){
    if($role !== 'Monster'){
        return 50+ceil(($level-1)/$level)*(10+$level*1.5);
    }
    return 20+$level*3;
}


# todo : 增加輸入判斷
function getOption($number){
    $option = re_input('option',$number);
    if(intval($option) === $number){
        return 'init';
    }else{
        return $option;
    }
}
function get_userInput($pool){
    $name = re_input('name'); #使用者名稱
    $role = re_input('role'); #職業
    
    # 轉換成職業
    $role = $pool[$role-1];

    return [$name,$role];
}

function re_input($key,$number=''){
    $config = getSetting('Database');
    $db = new Database($config);
    $user_name = '';
    $role = '';
    $option = '';
    switch($key){
        case 'name':
            while(empty($user_name)){
                $user_name = readline("使用者名稱：");
                $player = $db->query("select name from player where name='$user_name'")->find_or_fail('one');
                if(empty($player)){
                    break;
                }else{
                        echo "\033[44;31;4m名字重複，請重新輸入>_<\033[0m\n";
                }
                $user_name = '';
            }
            return $user_name;

        case 'role':
            while(empty($role) or $role<=0 or $role>=6){
                $role = readline("選擇職業 1.Warrior 2.Mage 3.Priest 4.Rogue 5.Paladin：");
            }
            return $role;

        case 'option':
            while(empty($option) or !is_numeric($option) or $option<=0 or $option>$number){
                $option = readline("請選擇要進入的紀錄檔案：");
            }
            return $option;
    }       
}

function fibonacci($n){
    if($n === 1 or $n === 2){
        return 1;
    }else{
        return fibonacci($n-1)+fibonacci($n-2);
    }
}

function random_numbers(){
    $num = range(0,15);
    shuffle($num);
    return array_slice($num, 0, 3);
}

# todo : 顯示使用者輸入讓使用者確認輸入
function check_userSelect(){}

# todo：戰鬥部分先寫成函數
function duel($player,$monster){
    $alive = '';
    $round = 1;
    $life = $player->attribute['life'];
    $attacker = compare($player->attribute['speed'],$monster->attribute['speed']);
    
    while($player->attribute['life']>0 and $monster->attribute['life']>0){
        echo PHP_EOL."Round {$round}".PHP_EOL;
        // echo "Player:{$player->attribute['speed']} , Monster:{$monster->attribute['speed']}".PHP_EOL;

        show_message($player->attribute['speed'],$monster->attribute['speed'],$attacker);
        [$alive,$attacker] = round_battle($attacker,$player,$monster);

        show_message($player->attribute['speed'],$monster->attribute['speed'],$attacker);
        [$alive,$attacker] = round_battle($attacker,$player,$monster);

        $round+=1;
    }

    $player->attribute['life'] = $life;
    echo "Player:{$player->attribute['life']} , Monster:{$monster->attribute['life']}".PHP_EOL;
    echo "$alive Win!".PHP_EOL;
}

function round_battle($attacker,$player,$monster){
    $alive = '';
    if($attacker === 'Player'){
        // $use = readline('使用1.物理攻擊 2.技能 3. 補血');
        //根據職業使用不同方式處理
        $monster->attribute['life'] -= $player->attribute['attack'];
        if($monster->attribute['life']<=0){
            $alive = 'Player';
            // break;
        }
        $attacker = 'Monster';
    }else{
        $player->attribute['life'] -= $monster->attribute['attack']; 
        if($player->attribute['life']<=0){
            $alive = 'Monster';
            // break;
        }
        $attacker = 'Player';
    }
    return [$alive,$attacker];
}

function show_message($player_life,$monster_life,$who){
    echo "Player:{$player_life} , Monster:{$monster_life}".PHP_EOL;
    echo "{$who} attack".PHP_EOL;
}
# 用來比較數值用的
function compare($user_value, $monster_value){
    if($user_value > $monster_value){
        return 'Player';
    }
    return 'Monster';
}
