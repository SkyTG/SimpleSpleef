<?php

namespace SimpleSpleef\Arena;

use pocketmine\Player;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;
use SimpleSpleef\Main;

class ArenaSchedule extends PluginTask {

    public function onRun($currentTick)
    {
        $plugin = $this->getOwner();
        if($plugin instanceof Main)
        {
            foreach($plugin->getAllArenas() as $arena)
            {
                if($arena instanceof Arena)
                {
                    if($arena->enabled != false)
                    {
                        $arena->second -= 1;
                        if($arena->second == 10)
                        {
                            if(count($arena->players) < 2)
                            {
                                foreach($arena->players as $p)
                                {
                                    if($p instanceof Player)
                                    {
                                        $p->sendMessage(TextFormat::AQUA."[SimpleSpleef] ".TextFormat::GOLD."Not enough players.");
                                        $arena->second += 30;
                                    }
                                }
                            }
                        }
                        if($arena->second < 6 and $arena->second > -1)
                        {
                            foreach($arena->players as $p)
                            {
                                if($p instanceof Player)
                                {
                                    $p->sendMessage(TextFormat::AQUA."[SimpleSpleef] ".TextFormat::GOLD."Starting in ".TextFormat::GREEN. $arena->second);
                                }
                            }
                        }
                        if($arena->second == 0)
                        {
                            foreach ($arena->players as $p)
                            {
                                if ($p instanceof Player)
                                {
                                    $arena->active = true;
                                }
                            }
                        }
                        if($arena->second == -180)
                        {
                            $arena->resetArena();
                        }


                        if($arena->second < 0)
                        {
                            if(count($arena->players) < 2)
                            {
                                $arena->resetArena();
                            }
                        }
                    }
                }
            }
        }
    }

} 