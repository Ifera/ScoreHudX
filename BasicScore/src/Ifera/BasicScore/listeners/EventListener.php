<?php
declare(strict_types = 1);

namespace Ifera\BasicScore\listeners;

use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use Ifera\ScoreHud\event\ServerTagUpdateEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use Ifera\BasicScore\Main;
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

class EventListener implements Listener{

	/** @var Main */
	private $plugin;

	public function __construct(Main $plugin){
	    $this->plugin = $plugin;
	}

	public function onJoin(PlayerJoinEvent $event){
	    (new ServerTagUpdateEvent(new ScoreTag("basicscore.online", (string) count($this->plugin->getServer()->getOnlinePlayers()))))->call();
	}

	public function onQuit(PlayerQuitEvent $event){
	    $this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function(): void{
	        (new ServerTagUpdateEvent(new ScoreTag("basicscore.online", (string) count($this->plugin->getServer()->getOnlinePlayers()))))->call();
	    }), 20);
	}
	
	public function onDamage(EntityDamageEvent $event){
	    $player = $event->getEntity();
	    if(!$player instanceof Player) return;
	    (new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.health", (string) ((int) $player->getHealth()))))->call();
	    (new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.max_health", (string) $player->getMaxHealth())))->call();
	}
	
	public function onRegainHealth(EntityRegainHealthEvent $event){
	    $player = $event->getEntity();
	    if(!$player instanceof Player) return;
	    (new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.health", (string) ((int) $player->getHealth()))))->call();
	    (new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.max_health", (string) $player->getMaxHealth())))->call();
	}
	
	public function onExperienceChange(PlayerExperienceChangeEvent $event){
	    $player = $event->getEntity();
	    if(!$player instanceof Player) return;
	    (new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.xp_level", (string) ((int) $player->getXpManager()->getXpLevel()))))->call();
	    (new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.xp_progress", (string) ((int) $player->getXpManager()->getXpProgress()))))->call();
	    (new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.xp_remainder", (string) ((int) $player->getXpManager()->getRemainderXp()))))->call();
	    (new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.xp_current_total", (string) ((int) $player->getXpManager()->getCurrentTotalXp()))))->call();
	}
	
	public function onMove(PlayerMoveEvent $event){
		$fX = (int) $event->getFrom()->getX();
		$fY = (int) $event->getFrom()->getY();
		$fZ = (int) $event->getFrom()->getZ();
		$tX = (int) $event->getTo()->getX();
		$tY = (int) $event->getTo()->gety();
		$tZ = (int) $event->getTo()->getZ();
		if($fX === $tX && $fY=== $tY && $fZ === $tZ){
			return;
		}
		$player = $event->getPlayer();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.x", (string) ((int) $player->getPosition()->getX()))))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.y", (string) ((int) $player->getPosition()->getY()))))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.z", (string) ((int) $player->getPosition()->getZ()))))->call();
	}

	public function onTeleport(EntityTeleportEvent $event){
		$player = $event->getEntity();
		$target = $event->getTo()->getWorld();
		if(!$player instanceof Player) return;
		(new ServerTagUpdateEvent(new ScoreTag("basicscore.world_player_count", (string) count($target->getPlayers()))))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.level_name", $target->getDisplayName())))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.world_name", $target->getDisplayName())))->call();

		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.level_folder_name", $target->getFolderName())))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.world_folder_name", $target->getFolderName())))->call();
	}

	public function onItemHeld(PlayerItemHeldEvent $event){
		$player = $event->getPlayer();
		$item = $event->getItem();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.item_name", $item->getName())))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.item_id", (string) $item->getId())))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.item_meta", (string) $item->getMeta())))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.item_count", (string) $item->getCount())))->call();
	}
}