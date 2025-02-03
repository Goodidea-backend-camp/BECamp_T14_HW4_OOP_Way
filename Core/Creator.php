<?php

namespace Core;

use Core\Database;

# 技能和武器都會繼承這個class讓子類可以不用複寫下面的code
class Creator{
    protected static $basicAttackWeapon = ["Sword"=>10, "Dagger"=>8, "Wand"=>6, "Ax"=>12];

    public static function dbConnect(){
        $dbConfig = getSetting("Database");
        return new Database($dbConfig);
    }
    public function execQuery($query,$oneOrAll){
        $db = self::dbConnect();
        return $db->query($query)->find_or_fail($oneOrAll);
    }

    public function weapon($user,$weaponType){
        $rarity = $this->getRarity("select * from rarity",'all');
        $moneyWeight = $this->execQuery("select moneyWeight from rarity where rarity='$rarity'",'one');
        $attackWeight = $this->execQuery("select attackWeight from rarity where rarity='$rarity'",'one');
        $attack = (self::$basicAttackWeapon[$weaponType] + $user->get("level")*2)*$attackWeight['attackWeight'];
        $money = ceil($attack*$moneyWeight['moneyWeight']*(rand(8,12)/10));
        $specialEffects = $this->specialEffects($rarity);

        return [$rarity,$moneyWeight,$attackWeight,$attack,$money,$specialEffects];
    }

    public function specialEffects($rarity){
        $db = self::dbConnect();
        $results = $db->query("SELECT count(*) as table_rows FROM special_effects")->find_or_fail('one');
        $specialRowNumber = $results['table_rows']; # 表special_effects 列數

        if($rarity === 'Common'){
            return NULL;
        }elseif($rarity === 'Rare'){
            $getAbilityID = rand(1,$specialRowNumber);
            $ability = $db->query("SELECT * FROM special_effects WHERE id=".$getAbilityID)->find_or_fail('one');
            return $ability;
        }elseif($rarity === 'Epic'){
            $getAbilityNumber = rand(2,4);
            $getAbilityID = implode(',', random_numbers(1,$specialRowNumber,$getAbilityNumber));
            $ability = $db->query("SELECT * FROM special_effects WHERE id in (".$getAbilityID.")")->find_or_fail('all');
            return $ability;
        }
    }

    public function getRarity($query,$oneOrAll){
        $db = self::dbConnect();
        $results = $db->query($query)->find_or_fail($oneOrAll);
        $rarity=array();
        $random = rand(1, 100);
        foreach($results as $result){
            $rarity[$result['rarity']] = $result['percen'];
        }
        if($random<=$rarity['Common']){
            return 'Common';
        }elseif($random<=$rarity['Rare']){
            return 'Rare';
        }elseif($random<=$rarity['Epic']){
            return 'Epic';
        }
    }
}
