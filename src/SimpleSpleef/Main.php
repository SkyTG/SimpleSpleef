<?php

namespace SimpleSpleef;

use pocketmine\plugin\PluginBase;
use SimpleSpleef\Arena\Arena;

class Main extends PluginBase {

    private $arenas = array();

    public function onEnable()
    {

    }


    /*
     * Create an arena
     * Returns: bool
     */
    public function createArena($name)
    {
        if(!isset($this->arenas[$name]))
        {
            $arena = new Arena($name);
            $this->arenas[$arena->getName()] = $arena;
            return true;
        }
        else
        {
            return false;
        }
    }

} 