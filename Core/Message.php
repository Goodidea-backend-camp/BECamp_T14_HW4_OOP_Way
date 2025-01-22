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
}
