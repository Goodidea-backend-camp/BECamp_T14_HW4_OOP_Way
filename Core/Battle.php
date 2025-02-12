<?php

namespace Core;

use Core\Traits\Store;

class Battle{
    protected $player;
    protected $monster;
    use Store;

    public function __construct($player,$monster){
        $this->player = $player;
        $this->monster = $monster;
        $this->duel($player,$monster);
    }

    public function duel($player,$monster){
        $alive = '';
        $round = 1;
        $life = $player->attribute['life'];
        $attacker = $this->compare($player->attribute['speed'],$monster->attribute['speed']);
        
        while($player->attribute['life']>0 and $monster->attribute['life']>0){
            echo PHP_EOL."Round {$round}".PHP_EOL;
            // echo "Player:{$player->attribute['speed']} , Monster:{$monster->attribute['speed']}".PHP_EOL;

            $this->showMessage($player->attribute['speed'],$monster->attribute['speed'],$attacker);
            [$alive,$attacker] = $this->roundBattle($attacker,$player,$monster);

            $this->showMessage($player->attribute['speed'],$monster->attribute['speed'],$attacker);
            [$alive,$attacker] = $this->roundBattle($attacker,$player,$monster);

            $round+=1;
        }

        $player->attribute['life'] = $life;
        echo "Player:{$player->attribute['life']} , Monster:{$monster->attribute['life']}".PHP_EOL;
        echo "$alive Win!".PHP_EOL;
    }

    public function roundBattle($attacker,$player,$monster){
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

    public function showMessage($player_life,$monster_life,$who){
        usleep(5000000);
        echo "Player:{$player_life} , Monster:{$monster_life}".PHP_EOL;
        echo "{$who} attack".PHP_EOL;
    }

    public function compare($user_value, $monster_value){
        if($user_value > $monster_value){
            return 'Player';
        }
        return 'Monster';
    }

    // public function writeRecord(){} # 每回合結果寫進文件
}
