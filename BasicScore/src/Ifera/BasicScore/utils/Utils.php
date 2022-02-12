<?php

declare(strict_types=1);

namespace Ifera\BasicScore\utils;

use Ifera\ScoreHud\ScoreHud;
use pocketmine\plugin\Plugin;
use function is_null;

class Utils{

	private static ?ScoreHud $scoreHud = null;

	public static function resolveDependency(Plugin $plugin) : bool{
		$server = $plugin->getServer();

		if(is_null(self::$scoreHud)){
			self::$scoreHud = $server->getPluginManager()->getPlugin("ScoreHud");
		}

		if(is_null(self::$scoreHud) || !self::$scoreHud instanceof ScoreHud || self::$scoreHud->isDisabled()){
			$plugin->getLogger()->error("Missing required dependency. ScoreHud plugin not found. Disabling " . $plugin->getDescription()->getName() . ".");
			$plugin->getServer()->getPluginManager()->disablePlugin($plugin);

			return false;
		}

		return true;
	}
}
