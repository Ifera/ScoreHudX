<?php
declare(strict_types = 1);

namespace Ifera\ScoreHudX;

use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use Ifera\ScoreHud\event\ServerTagUpdateEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use Ifera\ScoreHud\ScoreHudSettings;
use Ifera\ScoreHudX\listeners\EventListener;
use Ifera\ScoreHudX\listeners\TagResolveListener;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use function date;
use function strval;

class ScoreHudX extends PluginBase{

	public function onEnable(){
		$this->saveDefaultConfig();

		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getServer()->getPluginManager()->registerEvents(new TagResolveListener($this), $this);

		$task = new ClosureTask(function(int $_): void{
			foreach($this->getServer()->getOnlinePlayers() as $player){
				(new PlayerTagUpdateEvent($player, new ScoreTag("scorehudx.ping", strval($player->getPing()))))->call();
			}

			(new ServerTagUpdateEvent(new ScoreTag("scorehudx.load", strval($this->getServer()->getTickUsage()))))->call();
			(new ServerTagUpdateEvent(new ScoreTag("scorehudx.tps", strval($this->getServer()->getTicksPerSecond()))))->call();

			(new ServerTagUpdateEvent(new ScoreTag("scorehudx.time", strval(date(ScoreHudSettings::getTimeFormat())))))->call();
			(new ServerTagUpdateEvent(new ScoreTag("scorehudx.date", strval(date(ScoreHudSettings::getDateFormat())))))->call();
		});

		$this->getScheduler()->scheduleRepeatingTask($task, ((int) $this->getConfig()->get("update-period", 5)) * 20);
	}
}