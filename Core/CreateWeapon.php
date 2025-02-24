<?php

namespace Core;

use Core\Creator;

# 攻擊力 = (基礎值 + 等級修正:2*等級) × 稀有度修正
# 價格 = 攻擊力 × 稀有度加成 × 0.8~1.2
# 耐久度 = (基礎值 × 類型修正) + 隨機波動 <- 先不實作
class CreateAx extends Creator{
    public $result;
    public function __construct($user,$level)
    {   
        [$name, $weaponType,$rarity,$attack,$money,$specialEffects,$levelRequirement,$charactorRequirement] = $this->weapon($user,'Ax',$level);
        $this->result = ["name"=>$name, "type"=>$weaponType,"rarity"=>$rarity,"attack"=>$attack,"money"=>$money,"specialEffects"=>$specialEffects,"level_requirement"=>$levelRequirement,"charactor_requirement"=>$charactorRequirement];
        $this->returnResult();
    }
    public function returnResult(){
        return $this->result;
    }
}
class CreateWand extends Creator{
    public $result;
    public function __construct($user,$level)
    {
        [$name, $weaponType,$rarity,$attack,$money,$specialEffects,$levelRequirement,$charactorRequirement] = $this->weapon($user,'Wand',$level);
        $this->result = ["name"=>$name, "type"=>$weaponType,"rarity"=>$rarity,"attack"=>$attack,"money"=>$money,"specialEffects"=>$specialEffects,"level_requirement"=>$levelRequirement,"charactor_requirement"=>$charactorRequirement];
        $this->returnResult();
    }
    public function returnResult(){
        return $this->result;
    }
}
class CreateDagger extends Creator{
    public $result;
    public function __construct($user,$level)
    {
        [$name, $weaponType,$rarity,$attack,$money,$specialEffects,$levelRequirement,$charactorRequirement] = $this->weapon($user,'Dagger',$level);
        $this->result = ["name"=>$name, "type"=>$weaponType,"rarity"=>$rarity,"attack"=>$attack,"money"=>$money,"specialEffects"=>$specialEffects,"level_requirement"=>$levelRequirement,"charactor_requirement"=>$charactorRequirement];
        $this->returnResult();
    }
    public function returnResult(){
        return $this->result;
    }
}

