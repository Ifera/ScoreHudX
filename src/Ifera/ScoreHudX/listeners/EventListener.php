<?php
declare(strict_types = 1);

namespace Ifera\ScoreHudX\listeners;

use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use Ifera\ScoreHud\event\ServerTagUpdateEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use Ifera\ScoreHudX\ScoreHudX;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use function count;
use function intval;
use function is_null;
use function strval;

class EventListener implements Listener{

	/** @var ScoreHudX */
	private $plugin;

	public function __construct(ScoreHudX $plugin){
		$this->plugin = $plugin;
	}

	public function onJoin(PlayerJoinEvent $event){
		(new ServerTagUpdateEvent(new ScoreTag("scorehudx.online", strval(count($this->plugin->getServer()->getOnlinePlayers())))))->call();
	}

	public function onQuit(PlayerQuitEvent $event){
		(new ServerTagUpdateEvent(new ScoreTag("scorehudx.online", strval(count($this->plugin->getServer()->getOnlinePlayers())))))->call();
	}

	public function onMove(PlayerMoveEvent $event){
		if($event->getTo()->distance($event->getFrom()) < 1){
			return;
		}

		$player = $event->getPlayer();

		(new PlayerTagUpdateEvent($player, new ScoreTag("scorehudx.x", strval(intval($player->getX())))))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("scorehudx.y", strval(intval($player->getY())))))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("scorehudx.z", strval(intval($player->getZ())))))->call();
	}

	public function onTeleport(EntityTeleportEvent $event){
		$player = $event->getEntity();
		$target = $event->getTo()->getLevel();

		if(!$player instanceof Player){
			return;
		}

		if(is_null($target)){
			return;
		}

		(new ServerTagUpdateEvent(new ScoreTag("scorehudx.world_player_count", strval(count($target->getPlayers())))))->call();

		(new PlayerTagUpdateEvent($player, new ScoreTag("scorehudx.level_name", $target->getName())))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("scorehudx.world_name", $target->getName())))->call();

		(new PlayerTagUpdateEvent($player, new ScoreTag("scorehudx.level_folder_name", $target->getFolderName())))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("scorehudx.world_folder_name", $target->getFolderName())))->call();
	}

	public function onItemHeld(PlayerItemHeldEvent $event){
		$player = $event->getPlayer();
		$item = $event->getItem();

		(new PlayerTagUpdateEvent($player, new ScoreTag("scorehudx.item_name", $item->getName())))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("scorehudx.item_id", strval($item->getId()))))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("scorehudx.item_meta", strval($item->getDamage()))))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("scorehudx.item_count", strval($item->getCount()))))->call();
	}
}