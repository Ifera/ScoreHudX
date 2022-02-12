<?php

declare(strict_types=1);

namespace Ifera\CombatScore;

use Ifera\CombatScore\listeners\TagResolveListener;
use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use jacknoordhuis\combatlogger\CombatLogger;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use function strval;

class Main extends PluginBase{

	private CombatLogger $owningPlugin;

	public function onEnable() : void{
		$this->owningPlugin = $this->getServer()->getPluginManager()->getPlugin("CombatLogger");
		$this->getServer()->getPluginManager()->registerEvents(new TagResolveListener($this), $this);

		$this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function() : void{
			foreach($this->getServer()->getOnlinePlayers() as $player){
				if(!$player->isOnline()){
					continue;
				}

				(new PlayerTagUpdateEvent($player, new ScoreTag("combatscore.duration", strval($this->owningPlugin->getTagDuration($player)))))->call();
			}
		}), 20);
	}

	public function getOwningPlugin() : CombatLogger{
		return $this->owningPlugin;
	}
}
