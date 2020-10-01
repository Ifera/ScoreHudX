<?php
declare(strict_types = 1);

namespace Ifera\ScoreHudX;

use Ifera\ScoreHudX\listeners\EventListener;
use Ifera\ScoreHudX\listeners\TagResolveListener;
use pocketmine\plugin\PluginBase;

class ScoreHudX extends PluginBase{

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getServer()->getPluginManager()->registerEvents(new TagResolveListener($this), $this);
	}
}