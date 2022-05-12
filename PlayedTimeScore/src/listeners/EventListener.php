<?php

namespace supercrafter333\PlayedTimeScore\listeners;

use DateInterval;
use Ifera\ScoreHud\event\PlayerTagsUpdateEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;
use supercrafter333\PlayedTime\PlayedTimeLoader;
use supercrafter333\PlayedTimeScore\Main;

class EventListener implements Listener
{

    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();

        if (is_null($player) || !$player->isOnline()) {
            return;
        }

        $this->sendUpdate($player);
    }

    // no better way to detect when the suffix or prefix of a player changes
    public function onPlayerChat(PlayerChatEvent $event)
    {
        $this->sendUpdate($event->getPlayer());
    }

    public function sendUpdate(Player $player): void
    {
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
        (new PlayerTagsUpdateEvent($player, [
            new ScoreTag("ptscore.total_time", $time(PlayedTimeLoader::getInstance()->getPlayedTimeManager()->getTotalTime($player))),
            new ScoreTag("ptscore.session_time", $time(PlayedTimeLoader::getInstance()->getPlayedTimeManager()->getSessionTime($player)))
        ]))->call();
    }
}