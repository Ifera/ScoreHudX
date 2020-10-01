<?php
declare(strict_types = 1);

namespace Ifera\ScoreHudX;

use pocketmine\plugin\PluginBase;

class ScoreHudX extends PluginBase{

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
	}
}