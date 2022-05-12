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
            $cfg = Main::getInstance()->getConfig()->get("dateformat-letters", []);
            if ($dt->y > 0) $str .= "§e" . $dt->y . $cfg["year"] . "§7, ";
            if ($dt->m > 0) $str .= "§e" . $dt->m . $cfg["month"] . "§7, ";
            if ($dt->d > 0) $str .= "§e" . $dt->d . $cfg["day"] . "§7, ";
            if ($dt->h > 0) $str .= "§e" . $dt->h . $cfg["hour"] . "§7, ";
            if ($dt->i > 0) $str .= "§e" . $dt->i . $cfg["minute"] . "§7, ";
            if ($dt->s > 0) $str .= "§e" . $dt->s . $cfg["second"];
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