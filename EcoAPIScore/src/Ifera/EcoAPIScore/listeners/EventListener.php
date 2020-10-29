<?php
declare(strict_types = 1);

namespace Ifera\EcoAPIScore\listeners;

use Ifera\EcoAPIScore\Main;
use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use onebone\economyapi\event\money\MoneyChangedEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use function is_null;
use function strval;

class EventListener implements Listener{

	/** @var Main */
	private $plugin;

	public function __construct(Main $plugin){
		$this->plugin = $plugin;
	}

	public function onMoneyChange(MoneyChangedEvent $event){
		$username = $event->getUsername();

		if(is_null($username)){
			return;
		}

		$player = $this->plugin->getServer()->getPlayer($username);

		if($player instanceof Player && $player->isOnline()){
			(new PlayerTagUpdateEvent($player, new ScoreTag("ecoapiscore.money", strval($event->getMoney()))))->call();
		}
	}
}