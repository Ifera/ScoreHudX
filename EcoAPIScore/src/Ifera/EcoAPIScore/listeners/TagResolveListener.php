<?php
declare(strict_types = 1);

namespace Ifera\EcoAPIScore\listeners;

use Ifera\ScoreHud\event\TagsResolveEvent;
use Ifera\EcoAPIScore\Main;
use onebone\economyapi\EconomyAPI;
use pocketmine\event\Listener;
use function count;
use function explode;
use function strval;

class TagResolveListener implements Listener{

	/** @var Main */
	private $plugin;

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

		switch($tags[1]){
			case "money":
				$value = EconomyAPI::getInstance()->myMoney($event->getPlayer());
			break;
		}

		$tag->setValue(strval($value));
	}
}