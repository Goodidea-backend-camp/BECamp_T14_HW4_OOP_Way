<?php
namespace Core;

require_once('Interfaces/ObserverInterface.php');

use Core\Character;
use Core\Database;
use Core\Interfaces\OBserver;
use Core\Message;

class Avatar extends Character implements Observer{
    protected $id;
    protected $death;
    protected $killed;
    protected $weaponID;
    protected $bag;

    function __construct(){
        
    }

    public function init($username,$role){
        $attribute_percent = getSetting('attribute_percent');
        $this->name = $username;
        $this->level = 1;
        $this->role = $role;
        $this->money = 50;
        $this->death = 0;
        $point = calculate_total_point($this->role,$this->level);
        $this->attribute = response_points($attribute_percent[$role],$point,$role);
    }

    public function register($username, $role){
        $this->init($username,$role);
        $db = $this->DBconnect();
        $db->query("insert into player (name,level,role,money) values ('{$this->name}',{$this->level},'{$this->role}',{$this->money});");
        $playerID =  getID('player','name',$username);
        if($role !== 'Monster'){
            $player= "player";
        }else{
            $player = "monster";
        }
        $db->query("INSERT INTO user_attribute_relationships (user_id,player_or_monster,life,magic,attack,mag,defense,mddf,speed,lucky) VALUES ({$playerID},'{$player}',{$this->attribute['life']},{$this->attribute['magic']},{$this->attribute['attack']},{$this->attribute['mag']},{$this->attribute['defense']},{$this->attribute['mddf']},{$this->attribute['speed']},{$this->attribute['lucky']});");
        $attributeID = getID("user_attribute_relationships",'user_id',$playerID);
        $db->query("UPDATE player SET attribute_id = '{$attributeID}' WHERE id = {$playerID} ;");
    }
    
    public function load($query){
        $message = new Message;
        $header = getSetting('playerHeader');
        $old_records = $this->operate_DB($query,'all');
        usort($old_records, function ($a, $b) { return strlen($b['name']) - strlen($a['name']); }); # 把名字最長的放在最陣列前面方便後面的排版
        [$selectOption,$allOption] = $message->printTable($header,$old_records,"全新角色");
        return [$selectOption,$allOption,$old_records];
    }

    public function reload($playerData){
        $config = getSetting('Database');
        $db =  new Database($config);
        
        $this->id = $playerData['id'];
        $this->level = $playerData['level'];
        $this->name = $playerData['name'];        
        $this->role = $playerData['role'];
        $this->money = $playerData['money'];
        $this->exp = $playerData['exp'];
        $this->killed = $playerData['killed'];
        $this->death = $playerData['death'];
        $this->weaponID = $playerData['weapon_id'];
        
        $playerAttributeId = $playerData['attribute_id'];
        $playerData['attribute'] = ($db->query("Select life,magic,attack,mag,defense,mddf,speed,lucky from  user_attribute_relationships where id = $playerAttributeId")->find_or_fail('one'));
        $this->attribute = $playerData['attribute'];
        $this->bag = $this->bag();

        $attribute_percent = getSetting('attribute_percent');
        $role=$this->role;
        $this->getLevel($this->exp);
        $point = calculate_total_point($this->role,$this->level);
        $this->attribute = response_points($attribute_percent[$role],$point,$role);
        $query = "SELECT attribute_id FROM player WHERE player.name = '{$this->name}'";
        $db = $this->DBconnect();
        $attributID = $db->query($query)->find_or_fail('one');
        $updateQuery = "UPDATE user_attribute_relationships SET life={$this->attribute['life']},magic={$this->attribute['magic']},attack={$this->attribute['attack']},mag={$this->attribute['mag']},defense={$this->attribute['defense']},mddf={$this->attribute['mddf']},speed={$this->attribute['speed']},lucky={$this->attribute['lucky']} WHERE id={$attributID['attribute_id']} ";
        $db->query($updateQuery);
        
        return [$this->name,$this->role];
    }

    public function update($message){
        echo $message;
    }

    public function Bag(){ 
        $playerID = getID("player","name",$this->name);
        
        $totalInBag = 0;
        $bagArray =[];

        $weaponQuery = "SELECT
                            player.id,
                            player_weapon_relationships.weapon_id AS weaponID,
                            weapon.name AS weapon
                        FROM
                            player
                            JOIN player_weapon_relationships ON player.id = player_weapon_relationships.player_id
                            JOIN weapon  on player_weapon_relationships.weapon_id  = weapon.id
                        WHERE
                            player.id = $playerID";
        $supplyQuery = "SELECT
                            player.id,
                            player_supply_relationships.supply_id AS supplyID,
                            player_supply_relationships.quantity AS quantity,
                            supply.name As supplyNamem
                        FROM
                            player
                            JOIN player_supply_relationships ON player.id = player_supply_relationships.player_id
                            JOIN supply  on player_supply_relationships.supply_id = supply.id
                        WHERE
                            player.id = $playerID";
        $db = new Database(getSetting('Database'));
        $weaponResult = $db->query($weaponQuery)->find_or_fail('all');
        $supplyResult = $db->query($supplyQuery)->find_or_fail('all');
        if($supplyResult !== ''){
            foreach($supplyResult as $supply){
                $bagArray[$supply['supplyNamem']] = $supply["quantity"];
                $totalInBag += ceil($supply["quantity"]/10);
            }
        }
        
        if($weaponResult !== ''){
            foreach($weaponResult as $weapon){
                $bagArray[$weapon['weapon']] = 1;
                $totalInBag += 1;
            }
        }
        $bagArray['total'] = $totalInBag;
        
        return $bagArray;
    }

    public function getLevel($allExp) {
        $a = 5;
        $b = 95;
        $c = -$allExp;
    
        $discriminant = $b * $b - 4 * $a * $c;

        if ($discriminant < 0) {
            return null; 
        }
    
        $sqrtDiscriminant = sqrt($discriminant);
        $n1 = (-$b + $sqrtDiscriminant) / (2 * $a);
        $n2 = (-$b - $sqrtDiscriminant) / (2 * $a);

        if ($n1 > 0) {
            return (int)$n1;
        }
        if ($n2 > 0) {
            return (int)$n2;
        }
    
        return null;
    }
}
