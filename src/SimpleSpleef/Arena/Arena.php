<?php

namespace SimpleSpleef\Arena;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\ItemBlock;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\scheduler\PluginTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SimpleSpleef\Main;

class Arena extends PluginTask implements Listener{

    //Name of the arena
    private $arena_name;

    //Array of players that are in the arena
    public $players = array();

    //Arena spawn
    private $spawn;

    //The main plugin
    private $plugin;

    //All broken snow blocks
    private $broken = array();

    //Arena enabled
    public $enabled = false;

    //Arena active? (game running)
    public $active = false;

    //Second
    public $second = 60;

    /*
     * Create a new arena
     */
    public function __construct($name, $spawn, Main $mainplugin)
    {
        $this->arena_name = $name;
        $this->spawn = $spawn;
        $this->plugin = $mainplugin;
        Server::getInstance()->getPluginManager()->registerEvents($this, $mainplugin);
        $this->second = $this->plugin->getConfig()->get("wait");
    }

    /*
     * Running every second
     */
    public function onRun($currentTick)
    {
        $this->second -= 1;
        if($this->second == 10)
        {
            if(count($this->players) < 2)
            {
                foreach($this->players as $p)
                {
                    if($p instanceof Player)
                    {
                        $p->sendMessage(TextFormat::AQUA."[SimpleSpleef] ".TextFormat::GOLD."Not enough players.");
                        $this->second += 30;
                    }
                }
            }
        }
        if($this->second < 6 and $this->second > -1)
        {
            foreach($this->players as $p)
            {
                if($p instanceof Player)
                {
                    $p->sendMessage(TextFormat::AQUA."[SimpleSpleef] ".TextFormat::GOLD."Starting in ".TextFormat::GREEN. $this->second);
                }
            }
        }
        if($this->second == 0)
        {
            foreach ($this->players as $p)
            {
                if ($p instanceof Player)
                {
                    $this->active = true;
                }
            }
        }
    }

    /*
     * Add a player to the arena
     * Returns: void
     */
    public function addPlayer(Player $player)
    {
        if($this->enabled == true)
        {
            $this->players[$player->getName()] = $player;
            $player->arena = $this;
        }
        else
        {
            $player->sendMessage(TextFormat::AQUA."[SimpleSpleef] ".TextFormat::GOLD."This arena is disabled.");
        }
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
            unset($player->arena);
            $player->teleport(Server::getInstance()->getDefaultLevel()->getSafeSpawn());
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

    public function onDeath(PlayerDeathEvent $event)
    {
        $player = $event->getEntity();
        if(isset($this->players[$player->getName()]))
        {
            /*
             * Remove a player from the arena when it dies
             */
            $this->removePlayer($player);
        }
    }

    public function onQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();
        if(isset($this->players[$player->getName()]))
        {
            /*
             * Remove a player from the arena when it disconnects
             */
            $this->removePlayer($player);
        }
    }

    public function onBreak(BlockBreakEvent $event)
    {
        if($this->active == false)
        {
            $event->setCancelled();
        }
        if(isset($this->players[$event->getPlayer()->getName()]))
        {
            if($event->getBlock()->getID() != ItemBlock::SNOW_BLOCK)
            {
                $event->setCancelled();
            }
            else
            {
                if($this->active == true)
                {
                    $block = $event->getBlock();
                    $event->setInstaBreak(true);
                    $block = new Position($block->getX(), $block->getY(), $block->getZ(), $block->getLevel());
                    $this->broken[] = $block;
                }
            }
        }
    }

} 