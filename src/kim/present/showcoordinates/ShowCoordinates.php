<?php

/*
 *
 *  ____                           _   _  ___
 * |  _ \ _ __ ___  ___  ___ _ __ | |_| |/ (_)_ __ ___
 * | |_) | '__/ _ \/ __|/ _ \ '_ \| __| ' /| | '_ ` _ \
 * |  __/| | |  __/\__ \  __/ | | | |_| . \| | | | | | |
 * |_|   |_|  \___||___/\___|_| |_|\__|_|\_\_|_| |_| |_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author  PresentKim (debe3721@gmail.com)
 * @link    https://github.com/PresentKim
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0.0
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 */

declare(strict_types=1);

namespace kim\present\showcoordinates;

use kim\present\showcoordinates\listener\PlayerEventListener;
use kim\present\showcoordinates\task\CheckUpdateAsyncTask;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\permission\{
	Permission, PermissionManager
};
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
		$config = $this->getConfig();
		//Check latest version
		if($config->getNested("settings.update-check", false)){
			$this->getServer()->getAsyncPool()->submitTask(new CheckUpdateAsyncTask());
		}

		//Load permission's default value from config
		$permissions = PermissionManager::getInstance()->getPermissions();
		$defaultValue = $config->getNested("permission.main");
		if($defaultValue !== null){
			$permissions["gamerules.showcoordinates"]->setDefault(Permission::getByName($config->getNested("permission.main")));
		}

		//Create enabled data folder
		if(!file_exists($dataFolder = "{$this->getDataFolder()}enabled/")){
			mkdir($dataFolder, 0777, true);
		}

		//Register event listeners
		$this->getServer()->getPluginManager()->registerEvents(new PlayerEventListener($this), $this);
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
	 * @param string $playerName
	 *
	 * @return bool
	 */
	public function isShowCoordinates(string $playerName) : bool{
		return file_exists("{$this->getDataFolder()}enabled/" . strtolower($playerName));
	}

	/**
	 * @param string $playerName
	 * @param bool   $whether
	 */
	public function setShowCoordinates(string $playerName, bool $whether){
		$fileName = "{$this->getDataFolder()}enabled/" . strtolower($playerName);
		if($whether){
			if(file_exists($fileName)){
				unlink($fileName);
			}
		}else{
			file_put_contents($fileName, "");
		}
	}

	/**
	 * @param Player $player
	 */
	public function sendShowCoordinates(Player $player){
		$pk = new GameRulesChangedPacket();
		$pk->gameRules = [
			"showcoordinates" => [1, $this->isShowCoordinates($player->getName())]
		];
		$player->sendDataPacket($pk);
	}
}