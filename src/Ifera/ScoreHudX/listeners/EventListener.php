<?php
declare(strict_types = 1);

namespace Ifera\ScoreHudX\listeners;

use Ifera\ScoreHudX\ScoreHudX;
use pocketmine\event\Listener;

class EventListener implements Listener{

	/** @var ScoreHudX */
	private $plugin;

	public function __construct(ScoreHudX $plugin){
		$this->plugin = $plugin;
	}


}