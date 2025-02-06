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

    public function weapon($user,$weaponType,$level){
        $rarity = $this->getRarity("select * from rarity",'all');
        $moneyWeight = $this->execQuery("select moneyWeight from rarity where rarity='$rarity'",'one');
        $attackWeight = $this->execQuery("select attackWeight from rarity where rarity='$rarity'",'one');
        $attack = (self::$basicAttackWeapon[$weaponType] + $level*2)*$attackWeight['attackWeight'];
        $money = ceil($attack*$moneyWeight['moneyWeight']*(rand(8,12)/10));
        $specialEffects = $this->specialEffects($rarity);
        $name = $this->rand_name(4,$specialEffects,$rarity,$weaponType);

        return [$name, $weaponType, $rarity, $attack, $money, $specialEffects,$level,$user->get('role')];
    }

    public function specialEffects($rarity){
        $db = self::dbConnect();
        $results = $db->query("SELECT count(*) as table_rows FROM special_effects")->find_or_fail('one');
        $specialRowNumber = $results['table_rows']; # 表special_effects 列數

        if($rarity === 'Common'){
            return NULL;
        }elseif($rarity === 'Rare'){
            $getAbilityID = rand(1,$specialRowNumber);
            $ability = $db->query("SELECT id,name FROM special_effects WHERE id=".$getAbilityID)->find_or_fail('one');
            return $ability;
        }elseif($rarity === 'Epic'){
            $getAbilityNumber = rand(2,4);
            $getAbilityID = implode(',', random_numbers(1,$specialRowNumber,$getAbilityNumber));
            $ability = $db->query("SELECT id,name FROM special_effects WHERE id IN (".$getAbilityID.")")->find_or_fail('all');
            return $ability;
        }
    }

    public function rand_name($number,$specialEffects,$rarit,$weaponType){
        $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $name = "";

        if($specialEffects !== NULL and !array_key_exists('name',$specialEffects)){
            foreach($specialEffects as $specialEffect){
                $name .= $specialEffect['name'];
            }
            $name .= "-";
        }elseif($specialEffects !== NULL){
            $name .=  $specialEffects['name']."-";
        }

        $name .= $rarit.$weaponType."-";
        for ( $i = 0; $i < $number ; $i++ ){   
            $name .= $chars[mt_rand(0,35)];
        }

        return $name;
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
