<?php
declare(strict_types = 1);

namespace Ifera\PurePermsScore\listeners;

use Ifera\PurePermsScore\Main;
use Ifera\ScoreHud\event\TagsResolveEvent;
use pocketmine\event\Listener;
use function count;
use function explode;

class TagResolveListener implements Listener{

	/** @var Main */
	private $plugin;

	public function __construct(Main $plugin){
		$this->plugin = $plugin;
	}

	public function onTagResolve(TagsResolveEvent $event){
		$player = $event->getPlayer();
		$tag = $event->getTag();
		$tags = explode('.', $tag->getName(), 2);
		$value = "";

		if($tags[0] !== 'ppscore' || count($tags) < 2){
			return;
		}

		switch($tags[1]){
			case "rank":
				$value = $this->plugin->getPlayerRank($player);
				break;

			case "prefix":
				$value = $this->plugin->getPrefix($player);
				break;

			case "suffix":
				$value = $this->plugin->getSuffix($player);
				break;
		}

		$tag->setValue($value);
	}
}