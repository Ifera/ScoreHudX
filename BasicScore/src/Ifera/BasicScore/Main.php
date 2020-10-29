<?php
declare(strict_types = 1);

namespace Ifera\BasicScore;

use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use Ifera\ScoreHud\event\ServerTagUpdateEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use Ifera\ScoreHud\ScoreHudSettings;
use Ifera\BasicScore\listeners\EventListener;
use Ifera\BasicScore\listeners\TagResolveListener;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use function date;
use function strval;

class Main extends PluginBase{

	public function onEnable(){
		$this->saveDefaultConfig();

		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getServer()->getPluginManager()->registerEvents(new TagResolveListener($this), $this);

		$task = new ClosureTask(function(int $_): void{
			foreach($this->getServer()->getOnlinePlayers() as $player){
				(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.ping", strval($player->getPing()))))->call();
			}

			(new ServerTagUpdateEvent(new ScoreTag("basicscore.load", strval($this->getServer()->getTickUsage()))))->call();
			(new ServerTagUpdateEvent(new ScoreTag("basicscore.tps", strval($this->getServer()->getTicksPerSecond()))))->call();

			(new ServerTagUpdateEvent(new ScoreTag("basicscore.time", strval(date(ScoreHudSettings::getTimeFormat())))))->call();
			(new ServerTagUpdateEvent(new ScoreTag("basicscore.date", strval(date(ScoreHudSettings::getDateFormat())))))->call();
		});

		$this->getScheduler()->scheduleRepeatingTask($task, ((int) $this->getConfig()->get("update-period", 5)) * 20);
	}
}