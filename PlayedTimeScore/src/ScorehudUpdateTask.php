<?php

namespace supercrafter333\PlayedTimeScore;

use pocketmine\scheduler\Task;
use supercrafter333\PlayedTimeScore\listeners\EventListener;

class ScorehudUpdateTask extends Task
{

    public function onRun(): void
    {
        foreach (Main::getInstance()->getServer()->getOnlinePlayers() as $player)
            (new EventListener())->sendUpdate($player);
    }
}