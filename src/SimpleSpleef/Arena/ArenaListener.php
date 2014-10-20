<?php

namespace SimpleSpleef\Arena;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerQuitEvent;

class ArenaListener extends Arena implements Listener {

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

} 