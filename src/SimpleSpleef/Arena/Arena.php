<?php

namespace SimpleSpleef\Arena;

use pocketmine\level\Position;
use pocketmine\Player;

class Arena {

    //Name of the arena
    private $arena_name;

    //Array of players that are in the arena
    private $players = array();

    //Arena spawn
    private $spawn;

    /*
     * Create a new arena
     */
    public function __construct($name)
    {
        $this->arena_name = $name;
    }

    /*
     * Add a player to the arena
     * Returns: void
     */
    public function addPlayer(Player $player)
    {
        $this->players[$player->getName()] = $player;
    }

    /*
     * Remove a player from the arena
     * Returns: bool
     */
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

    public function getSpawn()
    {
        return $this->spawn;
    }

    /*
     * Setters
     */
    public function setName($name)
    {
        $this->arena_name = $name;
    }

    public function setSpawn(Position $pos)
    {
        $this->spawn = $pos;
    }

} 