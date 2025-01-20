<?php 

namespace Core\Interfaces;

interface Subject{
    public function attach($observer);
    public function detach($observer);
    public function notify();
}

interface Observer{
    public function update($message);
}
