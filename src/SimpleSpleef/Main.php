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

    /*
     * Get an arena by it's name
     * Returns: Success: Arena - Failure: false
     */
    public function getArenaByName($name)
    {
        if(isset($this->arenas[$name]))
        {
            return $this->arenas[$name];
        }
        else
        {
            return false;
        }
    }

} 