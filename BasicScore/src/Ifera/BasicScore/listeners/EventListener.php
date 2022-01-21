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
use function intval;
use function is_null;
use function strval;

class EventListener implements Listener{

	/** @var Main */
	private $plugin;

	public function __construct(Main $plugin)
        {
	    $this->plugin = $plugin;
	}

	public function onJoin(PlayerJoinEvent $event): void
        {
	    (new ServerTagUpdateEvent(new ScoreTag("basicscore.online", strval(count($this->plugin->getServer()->getOnlinePlayers())))))->call();
	}

	public function onQuit(PlayerQuitEvent $event): void
        {
	    $this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function(int $_): void{
	        (new ServerTagUpdateEvent(new ScoreTag("basicscore.online", strval(count($this->plugin->getServer()->getOnlinePlayers())))))->call();
	    }), 20);
	}
	
	public function onDamage(EntityDamageEvent $event): void
        {
	    $player = $event->getEntity();
	    if(!$player instanceof Player) return;
	    (new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.health", strval(intval($player->getHealth())))))->call();
	    (new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.max_health", strval(intval($player->getMaxHealth())))))->call();
	}
	
	public function onRegainHealth(EntityRegainHealthEvent $event): void
        {
	    $player = $event->getEntity();
	    if(!$player instanceof Player) return;
	    (new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.health", strval(intval($player->getHealth())))))->call();
	    (new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.max_health", strval(intval($player->getMaxHealth())))))->call();
	}
	
	public function onExperienceChange(PlayerExperienceChangeEvent $event): void
        {
	    $player = $event->getEntity();
	    if(!$player instanceof Player) return;
	    (new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.xp_level", strval(intval($player->getXpManager()->getXpLevel())))))->call();
	    (new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.xp_progress", strval(intval($player->getXpManager()->getXpProgress())))))->call();
	    (new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.xp_remainder", strval(intval($player->getXpManager()->getRemainderXp())))))->call();
	    (new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.xp_current_total", strval(intval($player->getXpManager()->getCurrentTotalXp())))))->call();
	}
	
	public function onMove(PlayerMoveEvent $event): void
        {
		$fX = intval($event->getFrom()->getFloorX());
		$fY = intval($event->getFrom()->getFloorY());
		$fZ = intval($event->getFrom()->getFloorZ());
		$tX = intval($event->getTo()->getFloorX());
		$tY = intval($event->getTo()->getFloorY());
		$tZ = intval($event->getTo()->getFloorZ());
		if($fX === $tX && $fY=== $tY && $fZ === $tZ){
			return;
		}
		$player = $event->getPlayer();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.x", strval(intval($player->getPosition()->getX())))))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.y", strval(intval($player->getPosition()->getY())))))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.z", strval(intval($player->getPosition()->getZ())))))->call();
	}

	public function onTeleport(EntityTeleportEvent $event): void
        {
		$player = $event->getEntity();
		$target = $event->getTo()->getLevel();
		if(!$player instanceof Player) return;
		if(is_null($target)) return;
		(new ServerTagUpdateEvent(new ScoreTag("basicscore.world_player_count", strval(count($target->getPlayers())))))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.level_name", $target->getName())))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.world_name", $target->getName())))->call();

		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.level_folder_name", $target->getFolderName())))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.world_folder_name", $target->getFolderName())))->call();
	}

	public function onItemHeld(PlayerItemHeldEvent $event): void
        {
		$player = $event->getPlayer();
		$item = $event->getItem();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.item_name", $item->getName())))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.item_id", strval($item->getId()))))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.item_meta", strval($item->getDamage()))))->call();
		(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.item_count", strval($item->getCount()))))->call();
	}
}
