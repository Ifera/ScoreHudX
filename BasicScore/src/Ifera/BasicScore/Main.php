<?php
declare(strict_types = 1);

namespace Ifera\BasicScore;

use Ifera\BasicScore\listeners\EventListener;
use Ifera\BasicScore\listeners\TagResolveListener;
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

class Main extends PluginBase{

	protected function onEnable() : void{
		$this->saveDefaultConfig();

		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getServer()->getPluginManager()->registerEvents(new TagResolveListener($this), $this);

		//if(!Utils::resolveDependency($this)){
		//	return;
		//}

		$task = new ClosureTask(function(): void{
			//if(!Utils::resolveDependency($this)){
			//	return;
			//}

			foreach($this->getServer()->getOnlinePlayers() as $player){
				(new PlayerTagUpdateEvent($player, new ScoreTag("basicscore.ping", (string) ($player->getNetworkSession()->getPing()))))->call();
			}

			(new ServerTagsUpdateEvent([
				new ScoreTag("basicscore.load", (string) $this->getServer()->getTickUsage()),
				new ScoreTag("basicscore.tps", (string) $this->getServer()->getTicksPerSecond()),
				new ScoreTag("basicscore.time", date(ScoreHudSettings::getTimeFormat())),
				new ScoreTag("basicscore.date", date(ScoreHudSettings::getDateFormat()))
			]))->call();

			if($this->getConfig()->get("enable-memory-tags", false)){
				$rUsage = Process::getRealMemoryUsage();
				$mUsage = Process::getAdvancedMemoryUsage();

				$globalMemory = "MAX";
				if($this->getServer()->getConfigGroup()->getProperty("memory.global-limit") > 0){
					$globalMemory = number_format(round($this->getServer()->getConfigGroup()->getProperty("memory.global-limit"), 2), 2) . " MB";
				}

				(new ServerTagsUpdateEvent([
					new ScoreTag("basicscore.memory_main_thread", number_format(round(($mUsage[0] / 1024) / 1024, 2), 2) . " MB"),
					new ScoreTag("basicscore.memory_total", number_format(round(($mUsage[1] / 1024) / 1024, 2), 2) . " MB"),
					new ScoreTag("basicscore.memory_virtual", number_format(round(($mUsage[2] / 1024) / 1024, 2), 2) . " MB"),
					new ScoreTag("basicscore.memory_heap", number_format(round(($rUsage[0] / 1024) / 1024, 2), 2) . " MB"),
					new ScoreTag("basicscore.memory_global", $globalMemory)
				]))->call();
			}
		});

		$this->getScheduler()->scheduleRepeatingTask($task, ((int) $this->getConfig()->get("update-period", 5)) * 20);
	}
}
