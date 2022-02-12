<?php

declare(strict_types=1);

namespace Ifera\EcoAPIScore\listeners;

use Ifera\EcoAPIScore\Main;
use Ifera\ScoreHud\event\TagsResolveEvent;
use onebone\economyapi\EconomyAPI;
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

		if($tags[0] !== 'ecoapiscore' || count($tags) < 2){
			return;
		}

		if($tags[1] === "money"){
			$value = EconomyAPI::getInstance()->myMoney($event->getPlayer());
		}

		$tag->setValue(strval($value));
	}
}
