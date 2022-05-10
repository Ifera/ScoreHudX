<?php

namespace supercrafter333\PlayedTimeScore\listeners;

use DateInterval;
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

        $time = function (DateInterval|null $dt): string {
            if ($dt === null) return "§cNOT FOUND";
            $str = "";
            if ($dt->y > 0) $str .= "§e" . $dt->y . "y§7, ";
            if ($dt->m > 0) $str .= "§e" . $dt->m . "m§7, ";
            if ($dt->d > 0) $str .= "§e" . $dt->d . "d§7, ";
            if ($dt->h > 0) $str .= "§e" . $dt->h . "h§7, ";
            if ($dt->i > 0) $str .= "§e" . $dt->i . "i§7, ";
            if ($dt->s > 0) $str .= "§e" . $dt->s . "s";
            return $str;
        };

        switch ($tags[1]) {
            case "total_time":
                $value = $time(PlayedTimeLoader::getInstance()->getPlayedTimeManager()->getTotalTime($player));
                break;

            case "session_time":
                $value = $time(PlayedTimeLoader::getInstance()->getPlayedTimeManager()->getSessionTime($player));
                break;
        }

        $tag->setValue($value);
    }
}