<?php

namespace Core\Interfaces;

interface Equipment {
    public static function create($user);
    public function belongTo($weapon);
    public function changeOwner($newOwner);
    public function limitCheck($user);
    public function effect();
}
