<?php

namespace Core;

use Core\Creator;

# 攻擊力 = (基礎值 + 等級修正:2*等級) × 稀有度修正
# 價格 = 攻擊力 × 稀有度加成 × 0.8~1.2
# 耐久度 = (基礎值 × 類型修正) + 隨機波動 <- 先不實作

class CreateAx extends Creator{
    public function __construct($user)
    {   
        [$rarity,$moneyWeight,$attackWeight,$attack,$money,$specialEffects] = $this->weapon($user,'Ax');
    }
}
class CreateWand extends Creator{
    public function __construct($user)
    {
        [$rarity,$moneyWeight,$attackWeight,$attack,$money,$specialEffects] = $this->weapon($user,'Wand');
    }
}
class CreateDagger extends Creator{
    public function __construct($user)
    {
        [$rarity,$moneyWeight,$attackWeight,$attack,$money,$specialEffects] = $this->weapon($user,'Dagger');
    }
}

