<?php
declare(strict_types = 1);

namespace Ifera\FactionsProScore\listeners;

use Ifera\ScoreHud\event\TagsResolveEvent;
use Ifera\FactionsProScore\Main;
use pocketmine\event\Listener;
use function count;
use function explode;
use function strval;

class TagResolveListener implements Listener
{

	/** @var Main */
	private $plugin;

	public function __construct(Main $plugin)
        {
		$this->plugin = $plugin;
	}

	public function onTagResolve(TagsResolveEvent $event): void
        {
		$tag = $event->getTag();
		$tags = explode('.', $tag->getName(), 2);
		$value = "";

		if($tags[0] !== 'factionsproscore' || count($tags) < 2)
                {
			return;
		}

		switch($tags[1])
                {
			case "faction":
				$value = $this->plugin->getPlayerFaction($event->getPlayer());
			break;

			case "power":
				$value = $this->plugin->getFactionPower($event->getPlayer());
			break;
		}

		$tag->setValue(strval($value));
	}
}
