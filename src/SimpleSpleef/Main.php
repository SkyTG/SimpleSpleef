<?php

namespace SimpleSpleef;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\tile\Sign;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextWrapper;
use SimpleSpleef\Arena\Arena;
use SimpleSpleef\Arena\ArenaSchedule;

class Main extends PluginBase implements Listener{

    private $arenas = array();

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        //Load all created arenas
        $arenas = file_get_contents($this->getDataFolder()."arenas.txt");
        if($arenas == false or $arenas == null)
        {
            $arenas = explode("\n", $arenas);
            if(count($arenas) > 1)
            {
                foreach($arenas as $arena)
                {
                    $this->loadArena($arena);
                }
            }

        }

        //Schedule the arenas
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new ArenaSchedule($this), 20);

    }

    /*
     * Returns all arenas
     */
    public function getAllArenas()
    {
        return $this->arenas;
    }

    /*
     * When clicked on sign
     */
    public function onInteract(PlayerInteractEvent $event)
    {
        if($event->getBlock()->getID() == 323 or $event->getBlock()->getID() == 63 or $event->getBlock()->getID() == 68)
        {
            $sign = $event->getPlayer()->getLevel()->getTile($event->getBlock());
            if($sign instanceof Sign)
            {
                $text = $sign->getText();
                if($text[0] == "[Spleef]")
                {
                    $arena = $this->getArenaByName($text[1]);
                    if($arena instanceof Arena)
                    {
                        $arena->addPlayer($event->getPlayer());
                    }
                }
            }
        }
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
            $arena->setName($name);
            $this->arenas[$arena->getArenaName()] = $arena;
            $this->saveArena($arena);
            $this->getServer()->getPluginManager()->registerEvents($arena, $this);
            return $this->arenas[$arena->getArenaName()];
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
        $name = $arena->getArenaName();
        $spawn = $arena->getSpawn();
        //Make spawn out of string
        if($spawn instanceof Position)
        {
            $spawn_loc = $spawn->getX()." ".$spawn->getY()." ".$spawn->getZ();
            if(isset($spawn->level))
            {
                $spawn_level = $spawn->level->getName();
            }
            else
            {
                $spawn_level = $this->getServer()->getDefaultLevel()->getName();
            }

            $arena_data = array(
                "name" => $name,
                "spawn" => $spawn_loc,
                "level" => $spawn_level
            );

            $arena_data = json_encode($arena_data);
            file_put_contents($this->getDataFolder()."arenas/".$name."/data.json", $arena_data);
            $arenas = file_get_contents($this->getDataFolder()."arenas.txt");
            $arenas = $arenas."\n".$arena->getArenaName();
            file_put_contents($this->getDataFolder()."arenas.txt", $arenas);
        }
    }

    /*
     * Load a saved arena
     * Returns: Arena
     */
    public function loadArena($name)
    {
        $data = file_get_contents($this->getDataFolder()."arenas/".$name."/data.json");
        $data = json_decode($data, true);
        $spawn = explode(" ", $data["spawn"]);
        $spawn = new Position($spawn[0], $spawn[1], $spawn[2], $this->getServer()->getLevelByName($data["level"]));
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
                                            return true;
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
                                                        return true;
                                                    break;
                                                case 'state':
                                                        if($sender instanceof Player)
                                                        {
                                                            $arena = $this->getArenaByName($args[3]);
                                                            if($arena instanceof Arena)
                                                            {
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
                                                        return true;
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
                                    if($arena instanceof Arena and isset($sender->arena) == false)
                                    {
                                        $arena->addPlayer($sender);
                                    }
                                    else
                                    {
                                        $sender->sendMessage(TextFormat::AQUA."[SimpleSpleef] ".TextFormat::GOLD."You're already in an arena.");
                                    }
                                    return true;
                                break;
                            case 'leave':
                                    $arena = $sender->arena;
                                    if(!isset($sender->arena))
                                    {
                                        $sender->sendMessage(TextFormat::AQUA."[SimpleSpleef] ".TextFormat::GOLD."You're not in an arena.");
                                    }
                                    else
                                    {
                                        if($arena instanceof Arena)
                                        {
                                            $arena->removePlayer($sender);
                                        }
                                    }
                                    return true;
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