<?php

namespace SimpleSpleef;

use pocketmine\level\Position;
use pocketmine\plugin\PluginBase;
use SimpleSpleef\Arena\Arena;

class Main extends PluginBase {

    private $arenas = array();

    public function onEnable()
    {

    }

    /*
     * Create an arena
     * Returns: Arena
     */
    public function createArena($name, Position $spawn)
    {
        if(!isset($this->arenas[$name]))
        {
            $arena = new Arena($name);
            $arena->setSpawn($spawn);
            $this->arenas[$arena->getName()] = $arena;
            $this->saveArena($arena);
            return $this->arenas[$arena->getName()];
        }
        else
        {
            return false;
        }
    }

    /*
     * Get an arena by it's name
     * Returns: Arena
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

    /*
     * Save an arenas data as json in the resources
     * Returns: void
     */
    public function saveArena(Arena $arena)
    {
        $name = $arena->getName();
        $spawn = $arena->getSpawn();
        //Make spawn out of string
        if($spawn instanceof Position)
        {
            $spawn = $spawn->getX()." ".$spawn->getY()." ".$spawn->getZ()." ".$spawn->getLevel()->getName();
        }

        $arena_data = array(
            "name" => $name,
            "spawn" => $spawn
        );

        $arena_data = json_encode($arena_data);
        file_put_contents($this->getDataFolder()."/arenas/".$name."/data.json", $arena_data);
    }

    /*
     * Load a saved arena
     * Returns: Arena
     */
    public function loadArena($name)
    {
        $data = file_get_contents($this->getDataFolder()."/arenas/".$name."/data.json");
        $data = json_decode($data, true);
        $spawn = explode(" ", $data["spawn"]);
        $spawn = new Position($spawn[0], $spawn[1], $spawn[2], $this->getServer()->getLevelByName($spawn[3]));
        $arena = $this->createArena($data["name"], $spawn);
        return $arena;
    }

} 