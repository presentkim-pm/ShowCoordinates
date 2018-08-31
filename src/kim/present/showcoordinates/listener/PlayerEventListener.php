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

namespace kim\present\showcoordinates\listener;

use kim\present\showcoordinates\ShowCoordinates;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\CommandRequestPacket;

class PlayerEventListener implements Listener{
	/** @var ShowCoordinates */
	private $owner = null;

	/**
	 * PlayerEventListener constructor.
	 *
	 * @param ShowCoordinates $owner
	 */
	public function __construct(ShowCoordinates $owner){
		$this->owner = $owner;
	}

	/**
	 * @priority LOWEST
	 *
	 * @param PlayerJoinEvent $event
	 */
	public function onPlayerJoinEvent(PlayerJoinEvent $event) : void{
		$player = $event->getPlayer();
		$this->owner->sendShowCoordinates($player);
	}

	/**
	 * @priority LOWEST
	 *
	 * @param DataPacketReceiveEvent $event
	 */
	public function onDataPacketReceiveEvent(DataPacketReceiveEvent $event) : void{
		$packet = $event->getPacket();
		$player = $event->getPlayer();
		if($packet instanceof CommandRequestPacket && strpos($packet->command, "/gamerule showcoordinates ") === 0){
			if($player->hasPermission("gamerules.showcoordinates")){
				$this->owner->setShowCoordinates($player->getName(), $packet->command === "/gamerule showcoordinates true");
				$this->owner->sendShowCoordinates($player);
			}

			$event->setCancelled();
		}
	}
}