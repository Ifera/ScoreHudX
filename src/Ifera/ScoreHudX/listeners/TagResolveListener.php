<?php
declare(strict_types = 1);

namespace Ifera\ScoreHudX\listeners;

use Ifera\ScoreHud\event\TagsResolveEvent;
use Ifera\ScoreHud\ScoreHudSettings;
use Ifera\ScoreHudX\ScoreHudX;
use pocketmine\event\Listener;
use function count;
use function date;
use function explode;
use function intval;
use function strval;

class TagResolveListener implements Listener{

	/** @var ScoreHudX */
	private $plugin;

	public function __construct(ScoreHudX $plugin){
		$this->plugin = $plugin;
	}

	public function onTagResolve(TagsResolveEvent $event){
		$player = $event->getPlayer();
		$tag = $event->getTag();
		$tags = explode('.', $tag->getName(), 2);
		$value = "";

		if($tags[0] !== 'scorehudx' || count($tags) < 2){
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

		$tag->setValue(strval($value));
	}
}