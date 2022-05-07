<?php

namespace supercrafter333\PlayedTimeScore;

use pocketmine\plugin\PluginBase;
use supercrafter333\PlayedTimeScore\listeners\EventListener;
use supercrafter333\PlayedTimeScore\listeners\TagResolveListener;

class Main extends PluginBase
{

    protected function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new TagResolveListener($this), $this);
    }
}