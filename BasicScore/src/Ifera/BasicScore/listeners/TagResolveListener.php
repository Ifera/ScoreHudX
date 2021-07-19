<?php
declare(strict_types = 1);

namespace Ifera\BasicScore\listeners;

use Ifera\ScoreHud\event\TagsResolveEvent;
use Ifera\ScoreHud\ScoreHudSettings;
use Ifera\BasicScore\Main;
use pocketmine\event\Listener;
use pocketmine\utils\Process;
use function count;
use function date;
use function explode;
use function intval;
use function number_format;
use function round;
use function strval;

class TagResolveListener implements Listener{

	/** @var Main */
	private $plugin;

	public function __construct(Main $plugin){
	    $this->plugin = $plugin;
	}

	public function onTagResolve(TagsResolveEvent $event){
		$player = $event->getPlayer();
		$tag = $event->getTag();
		$tags = explode('.', $tag->getName(), 2);
		$value = "";
		
		if($tags[0] !== 'basicscore' || count($tags) < 2){
			return;
		}
		
		switch($tags[1]){
			case "name":
			case "real_name":
			    $value = $player->getName();
			break;

			case "display_name":
			    $value = $player->getDisplayName();
			break;

			case "online":
			    $value = count($player->getServer()->getOnlinePlayers());
			break;

			case "max_online":
			    $value = $player->getServer()->getMaxPlayers();
			break;

			case "item_name":
			    $value = $player->getInventory()->getItemInHand()->getName();
			break;

			case "item_id":
			    $value = $player->getInventory()->getItemInHand()->getId();
			break;

			case "item_meta":
			    $value = $player->getInventory()->getItemInHand()->getDamage();
			break;

			case "item_count":
			    $value = $player->getInventory()->getItemInHand()->getCount();
			break;

			case "x":
			    $value = intval($player->getX());
			break;

			case "y":
			    $value = intval($player->getY());
			break;

			case "z":
			    $value = intval($player->getZ());
			break;

			case "load":
			    $value = $player->getServer()->getTickUsage();
			break;

			case "tps":
			    $value = $player->getServer()->getTicksPerSecond();
			break;

			case "level_name":
			case "world_name":
			    $value = $player->getLevelNonNull()->getName();
			break;

			case "level_folder_name":
			case "world_folder_name":
			    $value = $player->getLevelNonNull()->getFolderName();
			break;

			case "ip":
			    $value = $player->getAddress();
			break;

			case "ping":
			    $value = $player->getPing();
			break;
			
			case "health":
			    $value = intval($player->getHealth());
			break;
			
			case "max_health":
			    $value = intval($player->getMaxHealth());
			break;
			
			case "xp_level":
			    $value = intval($player->getXpLevel());
			break;

			case "xp_progress":
			    $value = intval($player->getXpProgress());
			break;
			
			case "xp_remainder":
			    $value = intval($player->getRemainderXp());
			break;
			
			case "xp_current_total":
			    $value = intval($player->getCurrentTotalXp());
			break;

			case "time":
			    $value = date(ScoreHudSettings::getTimeFormat());
			break;

			case "date":
			    $value = date(ScoreHudSettings::getDateFormat());
			break;

			case "world_player_count":
			    $value = count($player->getLevelNonNull()->getPlayers());
			break;
		}

		if((bool) $this->plugin->getConfig()->get("enable-memory-tags", false)){
			$rUsage = Process::getRealMemoryUsage();
			$mUsage = Process::getAdvancedMemoryUsage();

			$globalMemory = "MAX";
			if($this->plugin->getServer()->getProperty("memory.global-limit") > 0){
				$globalMemory = number_format(round($this->plugin->getServer()->getProperty("memory.global-limit"), 2), 2) . " MB";
			}

			switch($tags[1]){
				case "memory_main_thread":
				    $value = strval(number_format(round(($mUsage[0] / 1024) / 1024, 2), 2) . " MB");
				break;

				case "memory_total":
				    $value = strval(number_format(round(($mUsage[1] / 1024) / 1024, 2), 2) . " MB");
				break;

				case "memory_virtual":
				    $value = strval(number_format(round(($mUsage[2] / 1024) / 1024, 2), 2) . " MB");
				break;

				case "memory_heap":
				    $value = strval(number_format(round(($rUsage[0] / 1024) / 1024, 2), 2) . " MB");
				break;

				case "memory_global":
				    $value = $globalMemory;
				break;
			}
		}

		$tag->setValue(strval($value));
	}
}