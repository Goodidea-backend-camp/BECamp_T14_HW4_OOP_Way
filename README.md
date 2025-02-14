| 所有設計紀錄目前都丟到這邊，有些我還在整理中🫠 -> [設計紀錄](https://app.heptabase.com/w/260fb9c0d6c70307cec8bc3868d604d4719ec72abea5ca6c4cc28bb6501b3c98)

## 作業設計目標

- OOP
    

- 複雜需求
    

- 資料庫設計
    

## 作業說明

### 需求

```
本作業需求有很多不明確之處，需要學員自己發想
```

首先，玩家需要建立一個英雄，並輸入其姓名和選擇職業。

英雄初始等級為 Level 1，會根據職業不同，而有不同的：

- 生命值
    

- 魔力值
    

- 物理攻擊
    

- 魔法攻擊
    

- 物理防禦
    

- 魔法防禦
    

- 速度
    

- 幸運值
    

建立完英雄後，會根據職業配發一把初始武器。

武器可以提供玩家能力值上的提升，某些武器也可以提供特殊效果（如中毒、麻痹等等）。

武器的使用應有限制，例如：

- 法師應不能使用雙手劍
    

- Level 1 的工程師不能使用雙螢幕，至少要 Level 10 才可以
    

- 幸運值要大於 30 才能使用某武器
    

玩家可以用金幣購買武器。武器的所有權為玩家，所以玩家所創的所有英雄都可以使用已購買的武器。

每次遊戲結束，玩家會根據過關數獲得金幣。具體獲得多少金幣會使用費式數列計算。金幣的所有權為玩家。

玩家隨時可以開始一場遊戲。

每場遊戲有不同的關卡，愈前面愈簡單。

每一關會有一隻怪物，怪物會有：

- 名稱
    

- 生命值
    

- 魔力值
    

- 物理攻擊
    

- 魔法攻擊
    

- 物理防禦
    

- 魔法防禦
    

- 速度
    

- 幸運值
    

每回合，玩家和怪物會各自攻擊一次。速度快的先攻擊。

玩家可以選擇使用物理攻擊或魔法攻擊。魔法攻擊需消耗魔力，魔力不足就無法使用。

每次攻擊，怪物會隨機使用物理攻擊和魔法攻擊。

當怪物生命值歸零，該關卡結束。玩家會獲得：

- 經驗值
    

- 生命值恢復
    

- 金幣
    

當玩家生命值歸零，即為該場遊戲結束。玩家可以花費金幣來復活，但復活的花費會指數型提升。（e.g. 這次復活花費 = 上次復活花費 * 2）

每 8 關會有一隻魔王，魔王的各項數據都很高，可能還會有隱藏技能。

打贏魔王，玩家會獲得下列其中三個：

- 雙倍經驗值
    

- 雙倍的生命值恢復
    

- 雙倍的金幣
    

- 關卡道具
    

- 特殊武器
    

為避免玩家中途退出遊戲，玩家可以選擇從上次離開的關卡重新開始。

玩家在主畫面可以查看遊戲記錄，會顯示：

- 使用英雄名稱
    

- 開始時間
    

- 結束時間
    

- 開始遊戲時，英雄的
    
    - 等級
        
    
    - 生命值
        
    
    - 魔力值
        
    
    - 物理攻擊
        
    
    - 魔法攻擊
        
    
    - 物理防禦
        
    
    - 魔法防禦
        
    
    - 速度
        
    
    - 經驗值
        
    
    - 幸運值
        

- 結束遊戲時，英雄的
    
    - 等級
        
    
    - 生命值
        
    
    - 魔力值
        
    
    - 物理攻擊
        
    
    - 魔法攻擊
        
    
    - 物理防禦
        
    
    - 魔法防禦
        
    
    - 速度
        
    
    - 經驗值
        
    
    - 幸運值
        

- 破關數
    

- 總獲得金幣
    

- 總遊戲時長
    

### 驗收標準

### 作業流程

```
本作業略為複雜，考量到是學員的第一個專案型作業，強烈建議按照以下方式執行。
當然，這個作業硬幹也是可以幹的出來，但不建議就是了
```

1. 設計具體遊戲流程和功能
    
    1. 先用各種工具，模擬一下正常的流程是如何執行的
        
    
    1. 先想具體會有哪些武器、會有哪些職業、武器會有什麼功能
        
    
    1. 商業邏輯該如何設計，例如每關會獲得多少經驗值、幸運值運作機制等等
        

1. 設計 DB Schema
    
    1. 資料庫應該開哪些 Table
        
    
    1. 各 Table 應該有什麼欄位，以及欄位的 Type, is_nullable, default value 等等
        

1. 設計 OOP 架構
    
    1. 有哪些 Class、Interface、Trait、Abstract Class
        
    
    1. 各物件裡面有什麼 Property 和 Method
        

### 注意事項

- 或許可以參考 [Building Minicli 系列](https://dev.to/erikaheidi/bootstrapping-a-cli-php-application-in-vanilla-php-4ee)（沒看過只是剛好看到）
    

### 預期時間

三週
