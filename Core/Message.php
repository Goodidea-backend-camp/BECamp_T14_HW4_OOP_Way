<?php

namespace Core;

require_once('Interfaces/ObserverInterface.php');

use Core\Interfaces\Subject;

class Message implements Subject{
    private $observers = [];
    private $message;

    public function attach($observer){
        echo "歡迎挑戰者 $observer->name 進入.";
        $this->observers[] = $observer;
    }
    public function detach($observer){
        $index = array_search($observer,$this->observers);
        // if($index!== false){
        unset($observers[$index]);
        // }
    }
    public function notify(){
        foreach($this->observers as $observer){
            $observer->update($this->message);
        }
    }

    public function setmessage($message){
        $this->message = $message;
        $this->notify();
    }

    public function printTable(array $header, array $items, string $lastItem){
        $columnWidths = array(); #需要的字串大小
        $option = 1; #選項
        $format= '%-'.strlen("option")."s | "; #文字格式
        
        foreach($header as $key=>$title){
            $columnWidths[$key] = max(strlen($title),strlen($items[0][$key]))+4;
            $format .= "%-".($columnWidths[$key])."s | ";
        }
        $header = array_merge(["option" => "Option"], $header);
        $format .= "\n";

        # 印出標頭
        printf($format, ...array_values($header));
        echo str_repeat("-", array_sum($columnWidths) + (count($header) * 5)) . "\n";
        unset($header['option']); # 避免下面去用option去找items的東西

        foreach ($items as $item) {
            $row = [];
            $row[] = $option;
            foreach ($header as $key => $title) {
                $row[] = $item[$key] ?? "NULL";
            }
            printf($format, ...$row);
            $option += 1;
        }

        if($lastItem!==""){
            printf("%-".strlen("option")."s %-".strlen($lastItem)."s\n",$option,$lastItem);
            return [re_input('option',$option),$option];
        }
        return [re_input('option',$option-1),$option-1];
    }
}
