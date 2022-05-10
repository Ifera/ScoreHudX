<?php

namespace supercrafter333\PlayedTimeScore;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use supercrafter333\PlayedTimeScore\listeners\EventListener;
use supercrafter333\PlayedTimeScore\listeners\TagResolveListener;

class Main extends PluginBase
{
    use SingletonTrait;

    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    protected function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new TagResolveListener($this), $this);

        $this->getScheduler()->scheduleRepeatingTask(new ScorehudUpdateTask(), 20);
    }
}