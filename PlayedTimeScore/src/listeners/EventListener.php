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

    private function sendUpdate(Player $player): void
    {
        $time = function (DateInterval $dt): string {
            $str = "";
            if ($dt->y > 0) $str .= "§e" . $dt->y . "y§7, ";
            if ($dt->m > 0) $str .= "§e" . $dt->m . "m§7, ";
            if ($dt->d > 0) $str .= "§e" . $dt->d . "d§7, ";
            if ($dt->h > 0) $str .= "§e" . $dt->h . "h§7, ";
            if ($dt->i > 0) $str .= "§e" . $dt->i . "i§7, ";
            if ($dt->s > 0) $str .= "§e" . $dt->s . "s";
            return $str;
        };
        (new PlayerTagsUpdateEvent($player, [
            new ScoreTag("ptscore.total_time", $time(PlayedTimeLoader::getInstance()->getPlayedTimeManager()->getTotalTime($player))),
            new ScoreTag("ptscore.session_time", $time(PlayedTimeLoader::getInstance()->getPlayedTimeManager()->getSessionTime($player)))
        ]))->call();
    }
}