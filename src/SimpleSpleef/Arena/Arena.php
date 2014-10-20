<?php

namespace SimpleSpleef\Arena;

use pocketmine\Player;

class Arena {

    //Name of the arena
    private $arena_name;

    //Array of players that are in the arena
    private $players = array();

    /*
     * Create a new arena
     */
    public function __construct($name)
    {
        $this->arena_name = $name;
    }

    /*
     * Add a player to the arena
     */
    public function addPlayer(Player $player)
    {
        $this->players[$player->getName()] = $player;
    }

    public function removePlayer(Player $player)
    {
        if(isset($this->players[$player->getName()]))
        {
            unset($this->players[$player->getName()]);
            return true;
        }
        else
        {
            return false;
        }
    }

    /*
     * Getters
     */
    public function getName()
    {
        return $this->arena_name;
    }

} 