<?php
declare(strict_types = 1);

namespace Ifera\PurePermsScore;

use _64FF00\PurePerms\PurePerms;
use Ifera\PurePermsScore\listeners\EventListener;
use Ifera\PurePermsScore\listeners\TagResolveListener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase{

	/** @var PurePerms */
	private $purePerms;

	public function onEnable(){
		$this->purePerms = $this->getServer()->getPluginManager()->getPlugin("PurePerms");

		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getServer()->getPluginManager()->registerEvents(new TagResolveListener($this), $this);
	}

	public function getPlayerRank(Player $player): string{
		$group = $this->purePerms->getUserDataMgr()->getData($player)["group"];

		return $group === null ? "No Rank" : $group;
	}

	public function getPrefix(Player $player): string{
		$prefix = $this->purePerms->getUserDataMgr()->getNode($player, "prefix");

		return (($prefix === null) || ($prefix === "")) ? "No Prefix" : (string) $prefix;
	}

	public function getSuffix(Player $player): string{
		$suffix = $this->purePerms->getUserDataMgr()->getNode($player, "suffix");

		return (($suffix === null) || ($suffix === "")) ? "No Suffix" : (string) $suffix;
	}
}
