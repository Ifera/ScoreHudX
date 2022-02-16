<?php
declare(strict_types = 1);

namespace Ifera\EcoAPIScore;

use Ifera\EcoAPIScore\listeners\EventListener;
use Ifera\EcoAPIScore\listeners\TagResolveListener;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase{

	protected function onEnable(): void{
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getServer()->getPluginManager()->registerEvents(new TagResolveListener($this), $this);
	}
}