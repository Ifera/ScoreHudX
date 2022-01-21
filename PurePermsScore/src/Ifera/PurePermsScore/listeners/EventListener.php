<?php
declare(strict_types = 1);

namespace Ifera\PurePermsScore\listeners;

use _64FF00\PurePerms\event\PPGroupChangedEvent;
use Ifera\PurePermsScore\Main;
use Ifera\ScoreHud\event\PlayerTagsUpdateEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;
use function is_null;
use function strval;

class EventListener implements Listener{

	/** @var Main */
	private $plugin;

	public function __construct(Main $plugin){
		$this->plugin = $plugin;
	}

	public function onJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();

		if(is_null($player) || !$player->isOnline()){
			return;
		}

		$this->sendUpdate($player);
	}

	public function onGroupChange(PPGroupChangedEvent $event){
		$player = $event->getPlayer();

		if(!$player instanceof Player || !$player->isOnline()){
			return;
		}

		$this->sendUpdate($player);
	}

	// no better way to detect when the suffix or prefix of a player changes
	public function onPlayerChat(PlayerChatEvent $event){
		$this->sendUpdate($event->getPlayer());
	}

	private function sendUpdate(Player $player): void{
		(new PlayerTagsUpdateEvent($player, [
			new ScoreTag("ppscore.rank", strval($this->plugin->getPlayerRank($player))),
			new ScoreTag("ppscore.prefix", strval($this->plugin->getPrefix($player))),
			new ScoreTag("ppscore.suffix", strval($this->plugin->getSuffix($player)))
		]))->call();
	}
}
