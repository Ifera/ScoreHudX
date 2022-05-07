<?php

namespace supercrafter333\PlayedTimeScore\listeners;

use Ifera\ScoreHud\event\TagsResolveEvent;
use pocketmine\event\Listener;
use supercrafter333\PlayedTime\PlayedTimeLoader;
use supercrafter333\PlayedTimeScore\Main;

class TagResolveListener implements Listener
{

    public function __construct(private Main $plugin) {}

    public function onTagResolve(TagsResolveEvent $ev)
    {
        $player = $ev->getPlayer();
        $tag = $ev->getTag();
        $tags = explode('.', $tag->getName(), 2);
        $value = "";

        if ($tags[0] !== 'ptscore' || count($tags) < 2) {
            return;
        }

        switch ($tags[1]) {
            case "total_time":
                $value = PlayedTimeLoader::getInstance()->getPlayedTimeManager()->getTotalTime($player);
                break;

            case "session_time":
                $value = PlayedTimeLoader::getInstance()->getPlayedTimeManager()->getSessionTime($player);
                break;
        }

        $tag->setValue($value);
    }
}