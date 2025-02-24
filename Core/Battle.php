<?php

namespace Core;

use Core\Traits\Store;
use Core\Message;

class Battle{
    protected $player;
    protected $monster;
    protected $roundEffectRecord;
    use Store;

    public function __construct($player,$monster){
        $this->player = $player;
        $this->roundEffectRecord = [
            "effectRound"=>[]
        ];
        $this->monster = $monster;
        $this->duel($player,$monster);
    }

    public function duel($player,$monster){  
        $alive = '';
        $round = 1;
        $life = $player->attribute['life'];
        $mp = $player->attribute['magic'];
        $attacker = $this->compare($player->attribute['speed'],$monster->attribute['speed']);
        
        while($player->attribute['life']>0 and $monster->attribute['life']>0){
            echo PHP_EOL."Round {$round}".PHP_EOL;
            for($i=0;$i<2;$i++){
                $this->showMessage($player->attribute['life'],$monster->attribute['life'],$attacker);
                [$alive,$attacker] = $this->roundBattle($attacker,$player,$monster);
                if($player->attribute['life']<=0 or $monster->attribute['life']<=0){
                    break 2;
                }
            }
            $round+=1;
        }
        
        echo "Player:{$player->attribute['life']} , Monster:{$monster->attribute['life']}".PHP_EOL;
        $this->battleResult($player,$monster);
        if($alive === "Player"){
            $player->attribute['life'] = $life;
            $player->attribute["magic"] = $mp;
        }
    }

    public function roundBattle($attacker,$player,$monster){
        $message = new Message();
        $db = new Database(getSetting('Database'));
        $selectItme = getSetting("battleItem");
        $selectHeader = getSetting("battleHeader");
        // against
        $alive = '';
        if($attacker === 'Player'){
            // $use = readline('使用1.物理攻擊 2.技能 3. 補血');
            [$userSelectOption, $totalOption] = $message->printTable($selectHeader,$selectItme,"");
            while(1){
                switch($userSelectOption){
                    case 1:
                        $resultArray = $this->calculateWeapon($player);
                        if(count($resultArray) !== 0){
                            $player->attribute['attack'] += $resultArray['weaponAttack'];
                            // $round = $this->weaponEffect($resultArray,$player,$monster);
                        }
                        $monster->attribute['life'] -= $player->attribute['attack'];
                        break 2;
                    case 2:
                        echo PHP_EOL."Skill".PHP_EOL;

                        $playerMP = $player->attribute['magic'];
                        $resultArray = $this->calculateSkill($player);
                        $skillItem = array();
                        $skillHeader = getSetting('skillHeader');

                        if(count($resultArray)!==0){
                            foreach($resultArray as $key=>$item){
                                $skillItem[] = [
                                    'name'=>$key,
                                    'wasteMP'=>$item["wastMP"]    
                                ];
                            }
                            [$skillSelectOption, $skillTotalOption] = $message->printTable($skillHeader,$skillItem,"");
                            $skillName = $skillItem[$skillSelectOption-1]['name'];
                            $chooseSkill = $resultArray[$skillName];
                            if($playerMP > $chooseSkill['wastMP']){
                                $monster->attribute['life'] -= $chooseSkill["skillAttack"];
                            }
                            $monster->attribute['life'] -= $player->attribute['attack'];
                            break 2;
                        }else{
                            echo PHP_EOL."沒擁有任何技能請重新選擇".PHP_EOL;
                            $userSelectOption = 1000;
                            break;
                        }
                        
                        
                    case 3:
                        echo PHP_EOL."Supplements".PHP_EOL;
                        $supplyItem = array();
                        $supplyHeader = getSetting('supplyHeader');
                        $supplyInBag = $player->get('bag');
                        $supplyInBag = array_filter($supplyInBag, function ($key) {
                            return strpos($key, '-')=== false && $key !== "total";
                        }, ARRAY_FILTER_USE_KEY);
                        if(count($supplyInBag)>0){
                            foreach($supplyInBag as $key=>$item){
                                $supplyItem[] = [
                                    'name'=>$key,
                                    'number'=>$item
                                ];
                            }
                            [$supplySelectOption, $supplyTotalOption] = $message->printTable($supplyHeader,$supplyItem,"");
                            $selectItemName = $supplyItem[$supplySelectOption-1]['name'];

                            $supplyID = getID("supply","name",$selectItemName);
                            $playerID = getID('player','name',$player->name);
                            $query = "SELECT
                                            effect_variable,
                                            effect_value 
                                        FROM
                                            supply 
                                        WHERE
                                            NAME = '{$selectItemName}'";

                            $supplyBagQuery = "UPDATE player_supply_relationships 
                                                SET quantity = quantity - 1 
                                                WHERE
                                                    player_id ={ $playerID } 
                                                    AND supply_id ={ $supplyID}";
                            $effect = $db->query($query)->find_or_fail("one");
                            $effectAttribute = $effect['effect_variable'];
                            $effectValue = $effect['effect_value'];
                            $player->attribute[$effectAttribute] += $effectValue ;
                            $db->query($supplyBagQuery);
                        }
                        else{
                            echo PHP_EOL."沒擁有任何補品請重新選擇".PHP_EOL;
                            $userSelectOption = 1000;
                            break;
                        }
                        break 2;
                    default:
                        [$userSelectOption, $totalOption] = $message->printTable($selectHeader,$selectItme,"");
                }
            }
            if($monster->attribute['life']<=0){
                $alive = 'Player';
            }
            $attacker = 'Monster';
        }else{
            $player->attribute['life'] -= $monster->attribute['attack']; 
            // dd($this->calculateSkill($monster));
            if($player->attribute['life']<=0){
                $alive = 'Monster';
                // break;
            }
            $attacker = 'Player';
        }
        return [$alive,$attacker];    
    }

    public function calculateSkill($user){
        $resultArray = [];
        $db = new Database(getSetting('Database'));
        if($user->get('role') === 'Monster'){
            $userID = getID("monster","name",$user->name);
            $role = "Monster";
           
        }else{
            $userID = getID("player","name",$user->name);
            $role = "Player";
        }
        
        $query = "SELECT
                    {$role}.name AS name,
                    {$role}.level AS level,
                    user_attribute_relationships.attack AS attack,
                    skill.name AS skillName,
                    skill.attack AS skillAttack,
                    skill.mp AS skillMP,
                    skill.grow_mp AS skiilGrowMP,
                    skill.grow_value AS skiilGrowValue,
                    skill.grow_attack AS skiilGrowAttack,
                    special_effects.description AS effectDescrip,
                    special_effects.effect_variable AS effectVar,
                    special_effects.attacker_effect_value AS effectAttackValue,
                    special_effects.enemy_effect_value AS effectEnemyValue,
                    special_effects.round AS effectRound
                FROM
                    {$role}
                    JOIN user_attribute_relationships ON {$role}.attribute_id = user_attribute_relationships.id AND user_attribute_relationships.player_or_monster=LOWER('{$role}')
                    JOIN skill_user_relationships ON {$role}.id = skill_user_relationships.user_id AND skill_user_relationships.player_or_monster=LOWER('{$role}')
                    JOIN skill ON skill_user_relationships.skill_id = skill.id
                    JOIN special_effects ON skill.effect_id = special_effects.id
                WHERE
                    {$role}.id = {$userID}";
        
        $results = $db->query($query)->find_or_fail("all");
        if($results !== ''){
            $userMag = $user->attribute['mag'];
            foreach($results as $result){
                $skillName = $result['skillName'];
                $resultArray[$skillName] = ["skillAttack"=>$result['skillAttack']+$result['skiilGrowAttack']*$result['level']*$userMag];
                $resultArray[$skillName] +=['wastMP'=>$result['skillMP']+$result['skiilGrowMP']*$result['level']];
                
                $resultArray[$skillName] += ['effectVar' => $result['effectVar']];
                $resultArray[$skillName] += ['effectDescrip' => $result['effectDescrip']];
                if($result['effectEnemyValue'] !== NULL && $result['effectEnemyValue']<0){
                    $resultArray[$skillName] += ['effectEnemyValue' => $resultArray[$skillName]['skillAttack'] * $result['effectEnemyValue']];
                }elseif($result['effectEnemyValue'] !== NULL && $result['effectEnemyValue']>0){
                    $resultArray[$skillName] += ['effectEnemyValue' => $result['effectEnemyValue']];
                }elseif($result['effectAttackValue'] !== NULL){
                    $resultArray[$skillName] += ['effectAttackerValue' => $result['effectAttackValue']];
                }
                $resultArray[$skillName] += ['round' => $result['effectRound']];
            }

            return $resultArray; 
        }else{
            return array();
        }
    }

    public function calculateWeapon($user){
        $resultArray = [];
        $db = new Database(getSetting('Database'));
        $userID = getID("player","name",$user->name);
        $query = "SELECT
                    player.name AS name,
                    player.level AS level,
                    weapon.attack AS weaponAttack,
                    weapon_special_relationships.special_id AS weaponSpecialID,
                    special_effects.name AS effectName,
                    special_effects.effect_variable AS effectValue,
                    special_effects.description AS effectDescript,
                    special_effects.attacker_effect_value AS effectAttackValue,
                    special_effects.enemy_effect_value AS effectEnemyValue,
                    special_effects.round AS effectRound
                FROM
                    player
                    LEFT JOIN weapon ON player.weapon_id = weapon.id 
                    LEFT JOIN weapon_special_relationships ON weapon.id = weapon_special_relationships.weapon_id
                    LEFT JOIN special_effects ON weapon_special_relationships.special_id = special_effects.id
                WHERE
                    player.id = {$userID}";
        
        $results = $db->query($query)->find_or_fail("all");

        $resultArray['weaponAttack'] = $results[0]['weaponAttack'];
        foreach($results as $result){
            if($result['effectEnemyValue'] !== NULL && $result['effectEnemyValue']<0){
                $resultArray[$result['effectName']] = [
                    "effectDescript"=>$result['effectDescript'],
                    "effectValue"=>$result['effectValue'],
                    "effectEnemyValue"=>$result['effectEnemyValue']*$result['weaponAttack'],
                    "effectRound"=>$result['effectRound']
                ];
            }elseif($result['effectEnemyValue'] !== NULL && $result['effectEnemyValue']>0){
                $resultArray[$result['effectName']] = [
                    "effectDescript"=>$result['effectDescript'],
                    "effectValue"=>$result['effectValue'],
                    "effectEnemyValue"=>$result['effectEnemyValue'],
                    "effectRound"=>$result['effectRound']
                ];
            }elseif($result['effectAttackValue'] !== NULL){
                $resultArray[$result['effectName']] = [
                    "effectDescript"=>$result['effectDescript'],
                    "effectValue"=>$result['effectValue'],
                    "effectAttackValue"=>$result['effectAttackValue'],
                    "effectRound"=>$result['effectRound']
                ];
            }
        }
        
        return $resultArray;
    }

    // public function weaponEffect($effect,$player,$monster){
    //     print_r($effect);
    //     unset($effect['weaponAttack']);
    //     $keys = array_keys($effect);
    //     $chosen = rand(0,count($effect)-1);
    //     $dice = rand(0,100);
    //     $chosenEffect = $effect[$keys[$chosen]];
    //     foreach($this->roundEffectRecord['effectRound'] as $key=>$roundEffect){
    //         $roundEffect['round']-=1;
    //         if(array_key_exists('effectEnemyValue',$roundEffect) AND $roundEffect['effectEnemyValue']<0){
    //             $player->attribute[$roundEffect['effectEnemyValue']] += -1*$roundEffect['effectEnemyValue'];
    //         }elseif(array_key_exists('effectEnemyValue',$roundEffect) AND $roundEffect['effectEnemyValue']>0 AND $roundEffect['effectValue'] === NULL){
    //             $monster->attribute[$roundEffect['effectEnemyValue']] -= $player->attribute[$roundEffect['effectEnemyValue']] * $roundEffect['effectEnemyValue'];
    //         }
            
    //         if($roundEffect['round']===0){
    //             unset($this->roundEffectRecord['effectRound'][$key]);
    //         }
    //     }

    //     if(array_key_exists('effectEnemyValue',$chosenEffect) AND $chosenEffect['effectEnemyValue']< 0 ){
    //         if(!array_key_exists($keys[$chosen],$this->roundEffectRecord['effectRound'])){
    //             $this->roundEffectRecord['effectRound'][$keys[$chosen]] = [
    //                 'effectValue' =>$chosenEffect['effectValue'],
    //                 'effectEnemyValue'=>$chosenEffect['effectEnemyValue'],
    //                 'round'=>$chosenEffect['effectRound']
    //             ];
    //         }else{
    //             $this->roundEffectRecord['effectRound'][$keys[$chosen]]['round'] += $chosenEffect['effectRound'];
    //         }
    //     }elseif(array_key_exists('effectEnemyValue',$chosenEffect) AND $chosenEffect['effectEnemyValue']> 0 AND $chosenEffect['effectValue'] !== NULL){
    //         if(!array_key_exists($keys[$chosen],$this->roundEffectRecord['effectRound'])){
    //             $this->roundEffectRecord['effectRound'][$keys[$chosen]] = [
    //                 'effectValue' =>$chosenEffect['effectValue'],
    //                 'effectEnemyValue'=>$chosenEffect['effectEnemyValue'],
    //                 'round'=>$chosenEffect['effectRound']
    //             ];
    //         }else{
    //             $this->roundEffectRecord['effectRound'][$keys[$chosen]]['round'] += $chosenEffect['effectRound'];
    //         }
    //     }elseif(array_key_exists('effectEnemyValue',$chosenEffect) AND $chosenEffect['effectEnemyValue']> 0 AND $chosenEffect['effectValue'] === NULL AND $dice < $chosenEffect['effectEnemyValue']*100){
    //         if(!array_key_exists($keys[$chosen],$this->roundEffectRecord['effectRound'])){
    //             $this->roundEffectRecord['effectRound'][$keys[$chosen]] = [
    //                 'effectValue' =>$chosenEffect['effectValue'],
    //                 'effectEnemyValue'=>$chosenEffect['effectEnemyValue'],
    //                 'round'=>$chosenEffect['effectRound']
    //             ];
    //         }else{
    //             $this->roundEffectRecord['effectRound'][$keys[$chosen]]['round'] += $chosenEffect['effectRound'];
    //         }
    //     }elseif(array_key_exists('effectAttackValue',$chosenEffect) AND $chosenEffect['effectAttackValue']> 0 AND $chosenEffect['effectValue'] !== 'critical'){
    //         $player->attribute[$chosenEffect['effectValue']] += $chosenEffect['effectAttackValue'];
    //     }elseif(array_key_exists('effectAttackValue',$chosenEffect) AND $chosenEffect['effectAttackValue']> 0 AND $chosenEffect['effectValue'] === 'critical'){
    //         $player->attribute['attack'] += 100;
    //     }
    // }

    public function showMessage($player_life,$monster_life,$who){
        usleep(950000);
        echo PHP_EOL."{$who} Round to be attacker".PHP_EOL;
        echo "Player:{$player_life} , Monster:{$monster_life}".PHP_EOL;
    }

    public function compare($user_value, $monster_value){
        if($user_value > $monster_value){
            return 'Player';
        }
        return 'Monster';
    }

    public function battleResult($player,$monster){
        $db = new Database(getSetting('Database'));
        $monsterID = getID('monster','name',$monster->name);
        $userID = getID('player','name',$player->name);
        
        if((int)$monster->attribute['life'] <= 0){
            echo "{$player->name} Win!".PHP_EOL;
            $playerLevel = $player->level;
            $rewordMoney = $monster->get('money');
            $rewordExperence = $monster->get('exp');
            $monsterQuery = "UPDATE monster SET killed = 1 WHERE id={$monsterID}";
            $playerQuery = "UPDATE player SET killed = killed+1,exp=exp+{$rewordExperence},money=money+{$rewordMoney},level={$playerLevel} WHERE id = {$userID}";

            $db->query($monsterQuery);
            $db->query($playerQuery);
        }elseif((int)$player->attribute['life'] <= 0){
            echo "{$monster->name} Win!".PHP_EOL;
            $playerMoney = $player->get('money');
            $playerDeth = $player->get('death');
            [$money,$deathTime] = $this->revival($playerMoney,$playerDeth);
            $playerQuery = "UPDATE player SET death=death+{$deathTime},money={$money} WHERE id = {$userID}";
            $db->query($playerQuery);      
        }
        
    }

    public function revival($money,$dethTime){
        $message = new Message();
        if($money < pow(2,($dethTime-1))){
            $dethTime+=1;
        }else{
            $revialHeader = getSetting('revialHeader');
            $revialItem = getSetting('revialItem');
            echo "你能進行復活，是否要進行!".PHP_EOL;
            [$selectOption,$allOptions] = $message->printTable($revialHeader,$revialItem,'');
            if($selectOption === 1){
                $money -= pow(2,($dethTime-1));
            }else{
                $dethTime+=1;
            }
        }

        return [$money,$dethTime];
    }
    // public function writeRecord(){} # 每回合結果寫進文件
}
