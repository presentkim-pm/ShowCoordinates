<?php

namespace kim\present\showcoordinates;

use kim\present\showcoordinates\listener\PlayerEventListener;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class ShowCoordinates extends PluginBase{
	public const TAG_PLUGIN = "ShowCoordinates";

	/** @var ShowCoordinates */
	private static $instance;

	/** @return ShowCoordinates */
	public static function getInstance() : ShowCoordinates{
		return self::$instance;
	}

	/**
	 * Called when the plugin is loaded, before calling onEnable()
	 */
	public function onLoad() : void{
		self::$instance = $this;
	}

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
		$player->namedtag->setByte(self::TAG_PLUGIN, (int) $whether);

		$pk = new GameRulesChangedPacket();
		$pk->gameRules = [
			"showcoordinates" => [1, $whether]
		];
		$player->dataPacket($pk);
	}
}