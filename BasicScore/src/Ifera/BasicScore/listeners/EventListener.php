<?php

declare(strict_types=1);

namespace Ifera\BasicScore\listeners;

use Ifera\BasicScore\Main;
use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use Ifera\ScoreHud\event\ServerTagUpdateEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerExperienceChangeEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use function count;
use function intval;
use function is_null;
use function strval;

class EventListener implements Listener{

	private Main $plugin;

	public function __construct(Main $plugin){
		$this->plugin = $plugin;
	}

	public function onJoin(PlayerJoinEvent $event){
		(new ServerTagUpdateEvent(new ScoreTag("basicscore.online", strval(count($this->plugin->getServer()->getOnlinePlayers())))))->call();
	}

	public function onQuit(PlayerQuitEvent $event){
		$this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function() : void{
			(new ServerTagUpdateEvent(new ScoreTag("basicscore.online", strval(count($this->plugin->getServer()->getOnlinePlayers())))))->call();
		}), 20);
	}

	public function onDamage(EntityDamageEvent $event){
		$player = $event->getEntity();
		if(!$player instanceof Player) return;
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.health", strval(intval($player->getHealth())))))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.max_health", strval(intval($player->getMaxHealth())))))->call();
	}

	public function onRegainHealth(EntityRegainHealthEvent $event){
		$player = $event->getEntity();
		if(!$player instanceof Player) return;
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.health", strval(intval($player->getHealth())))))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.max_health", strval(intval($player->getMaxHealth())))))->call();
	}

	public function onExperienceChange(PlayerExperienceChangeEvent $event){
		$player = $event->getEntity();
		if(!$player instanceof Player) return;
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.xp_level", strval(intval($player->getXpManager()->getXpLevel())))))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.xp_progress", strval(intval($player->getXpManager()->getXpProgress())))))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.xp_remainder", strval(intval($player->getXpManager()->getRemainderXp())))))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.xp_current_total", strval(intval($player->getXpManager()->getCurrentTotalXp())))))->call();
	}

	public function onMove(PlayerMoveEvent $event){
		$fX = intval($event->getFrom()->getX());
		$fY = intval($event->getFrom()->getY());
		$fZ = intval($event->getFrom()->getZ());
		$tX = intval($event->getTo()->getX());
		$tY = intval($event->getTo()->gety());
		$tZ = intval($event->getTo()->getZ());
		if($fX === $tX && $fY === $tY && $fZ === $tZ){
			return;
		}
		$player = $event->getPlayer();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.x", strval(intval($player->getPosition()->getX())))))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.y", strval(intval($player->getPosition()->getY())))))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.z", strval(intval($player->getPosition()->getZ())))))->call();
	}

	public function onTeleport(EntityTeleportEvent $event){
		$player = $event->getEntity();
		$target = $event->getTo()->getWorld();
		if(!$player instanceof Player) return;
		if(is_null($target)) return;
		(new ServerTagUpdateEvent(new ScoreTag("basicscore.world_player_count", strval(count($target->getPlayers())))))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.level_name", $target->getDisplayName())))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.world_name", $target->getDisplayName())))->call();

		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.level_folder_name", $target->getFolderName())))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.world_folder_name", $target->getFolderName())))->call();
	}

	public function onItemHeld(PlayerItemHeldEvent $event){
		$player = $event->getPlayer();
		$item = $event->getItem();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.item_name", $item->getName())))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.item_id", strval($item->getId()))))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.item_meta", strval($item->getMeta()))))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.item_count", strval($item->getCount()))))->call();
	}
}
