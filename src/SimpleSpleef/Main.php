<?php

namespace SimpleSpleef;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextWrapper;
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
            $arena = new Arena($name, $spawn, $this);
            $arena->setSpawn($spawn);
            $this->arenas[$arena->getName()] = $arena;
            $this->saveArena($arena);
            $this->getServer()->getScheduler()->scheduleRepeatingTask($arena, 20);
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




    /*
     * Command Handler
     */
    public function onCommand(CommandSender $sender, Command $command, $label, array $args)
    {
        switch($command->getName())
        {
            case 'ss':
                    switch($args[0])
                    {
                        case 'arena':
                                switch($args[1])
                                {
                                    case 'create':
                                            if($sender instanceof Player)
                                            {
                                                $spawn = $sender->getPosition();
                                                $arena = $this->createArena($args[2], $spawn);
                                                if($arena != false)
                                                {
                                                    $sender->sendMessage(TextFormat::AQUA."[SimpleSpleef] ".TextFormat::GOLD."Created arena ".$args[2]);
                                                }
                                                else
                                                {
                                                    $sender->sendMessage(TextFormat::DARK_RED."Error while creating the arena.");
                                                }
                                            }
                                        break;
                                    case 'edit':
                                            switch($args[2])
                                            {
                                                case 'spawn':
                                                        if($sender instanceof Player)
                                                        {
                                                            $arena = $this->getArenaByName($args[3]);
                                                            if($arena instanceof Arena)
                                                            {
                                                                $arena->setSpawn($sender->getPosition());
                                                                $sender->sendMessage(TextFormat::AQUA."[SimpleSpleef] ".TextFormat::GOLD."Set new arena spawn.");
                                                            }
                                                        }
                                                    break;
                                                case 'state':
                                                        if($sender instanceof Player)
                                                        {
                                                            $arena = $this->getArenaByName($args[3]);
                                                            if($arena instanceof Arena)
                                                            {
                                                                $arena->enabled = $args[4];
                                                                if($arena->enabled == false)
                                                                {
                                                                    $sender->sendMessage(TextFormat::AQUA."[SimpleSpleef] ".TextFormat::GOLD."Disabled arena.");
                                                                }
                                                                else
                                                                {
                                                                    $sender->sendMessage(TextFormat::AQUA."[SimpleSpleef] ".TextFormat::GOLD."Enabled arena.");
                                                                }
                                                            }
                                                        }
                                                    break;
                                            }
                                        break;
                                }
                            break;
                    }
                break;
            case 'spleef':
                    if($sender instanceof Player)
                    {
                        switch($args[0])
                        {
                            case 'join':
                                    $arena = $this->getArenaByName($args[1]);
                                    if($arena instanceof Arena)
                                    {
                                        $arena->addPlayer($sender);
                                    }
                                break;
                            case 'leave':
                                    $arena = $sender->arena;
                                    if($arena instanceof Arena)
                                    {
                                        $arena->removePlayer($sender);
                                    }
                                break;
                        }
                    }
                    else
                    {
                        $sender->sendMessage(TextFormat::RED."Please run this command in-game.");
                    }
                break;
        }
    }

} 