<?php

namespace kim\present\showcoordinates;

use kim\present\showcoordinates\listener\PlayerEventListener;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class ShowCoordinates extends PluginBase{
	/**
	 * Called when the plugin is enabled
	 */
	public function onEnable() : void{
		//Register event listeners
		$this->getServer()->getPluginManager()->registerEvents(new PlayerEventListener(), $this);
	}

	/**
	 * @param Player $player
	 * @param bool   $whether
	 */
	public final static function setShowCoordinates(Player $player, bool $whether){
		$player->namedtag->setByte("ShowCoordinates", (int) $whether);

		$pk = new GameRulesChangedPacket();
		$pk->gameRules = [
			"showcoordinates" => [1, $whether]
		];
		$player->dataPacket($pk);
	}
}