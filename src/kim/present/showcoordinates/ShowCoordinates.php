<?php

namespace kim\present\showcoordinates;

use kim\present\showcoordinates\listener\PlayerEventListener;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\permission\Permission;
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
		//Load permission's default value from config
		$config = $this->getConfig();
		$permissions = $this->getServer()->getPluginManager()->getPermissions();
		$defaultValue = $config->getNested("permission.main");
		if($defaultValue !== null){
			$permissions["gamerules.showcoordinates"]->setDefault(Permission::getByName($config->getNested("permission.main")));
		}

		//Register event listeners
		$this->getServer()->getPluginManager()->registerEvents(new PlayerEventListener(), $this);
	}

	/**
	 * @Override for multilingual support of the config file
	 *
	 * @return bool
	 */
	public function saveDefaultConfig() : bool{
		$resource = $this->getResource("lang/{$this->getServer()->getLanguage()->getLang()}/config.yml");
		if($resource === null){
			$resource = $this->getResource("lang/eng/config.yml");
		}

		if(!file_exists($configFile = "{$this->getDataFolder()}config.yml")){
			$ret = stream_copy_to_stream($resource, $fp = fopen($configFile, "wb")) > 0;
			fclose($fp);
			fclose($resource);
			return $ret;
		}
		return false;
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