<?php
declare(strict_types = 1);

namespace Ifera\BasicScore;

use Ifera\BasicScore\listeners\EventListener;
use Ifera\BasicScore\listeners\TagResolveListener;
use Ifera\BasicScore\utils\Utils;
use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use Ifera\ScoreHud\event\ServerTagsUpdateEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use Ifera\ScoreHud\ScoreHudSettings;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\Process;
use function date;
use function number_format;
use function round;
use function strval;

class Main extends PluginBase{

	public function onEnable(){
		$this->saveDefaultConfig();

		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getServer()->getPluginManager()->registerEvents(new TagResolveListener($this), $this);

		//if(!Utils::resolveDependency($this)){
		//	return;
		//}

		$task = new ClosureTask(function(int $_): void{
			//if(!Utils::resolveDependency($this)){
			//	return;
			//}

			foreach($this->getServer()->getOnlinePlayers() as $player){
				(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.ping", strval($player->getNetworkSession()->getPing()))))->call();
			}

			(new ServerTagsUpdateEvent([
				new ScoreTag("basicscore.load", strval($this->getServer()->getTickUsage())),
				new ScoreTag("basicscore.tps", strval($this->getServer()->getTicksPerSecond())),
				new ScoreTag("basicscore.time", strval(date(ScoreHudSettings::getTimeFormat()))),
				new ScoreTag("basicscore.date", strval(date(ScoreHudSettings::getDateFormat())))
			]))->call();

			if((bool) $this->getConfig()->get("enable-memory-tags", false)){
				$rUsage = Process::getRealMemoryUsage();
				$mUsage = Process::getAdvancedMemoryUsage();

				$globalMemory = "MAX";
				if($this->getServer()->getProperty("memory.global-limit") > 0){
					$globalMemory = number_format(round($this->getServer()->getProperty("memory.global-limit"), 2), 2) . " MB";
				}

				(new ServerTagsUpdateEvent([
					new ScoreTag("basicscore.memory_main_thread", strval(number_format(round(($mUsage[0] / 1024) / 1024, 2), 2) . " MB")),
					new ScoreTag("basicscore.memory_total", strval(number_format(round(($mUsage[1] / 1024) / 1024, 2), 2) . " MB")),
					new ScoreTag("basicscore.memory_virtual", strval(number_format(round(($mUsage[2] / 1024) / 1024, 2), 2) . " MB")),
					new ScoreTag("basicscore.memory_heap", strval(number_format(round(($rUsage[0] / 1024) / 1024, 2), 2) . " MB")),
					new ScoreTag("basicscore.memory_global", $globalMemory)
				]))->call();
			}
		});

		$this->getScheduler()->scheduleRepeatingTask($task, ((int) $this->getConfig()->get("update-period", 5)) * 20);
	}
}
