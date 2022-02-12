<?php

declare(strict_types=1);

namespace Ifera\CombatScore\listeners;

use Ifera\CombatScore\Main;
use Ifera\ScoreHud\event\TagsResolveEvent;
use pocketmine\event\Listener;
use function count;
use function explode;
use function strval;

class TagResolveListener implements Listener{

	private Main $plugin;

	public function __construct(Main $plugin){
		$this->plugin = $plugin;
	}

	public function onTagResolve(TagsResolveEvent $event){
		$tag = $event->getTag();
		$tags = explode('.', $tag->getName(), 2);
		$value = "";

		if($tags[0] !== 'combatscore' || count($tags) < 2){
			return;
		}

		if($tags[1] === "duration"){
			$value = $this->plugin->getOwningPlugin()->getTagDuration($event->getPlayer());
		}

		$tag->setValue(strval($value));
	}
}
