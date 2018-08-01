<?php

namespace kim\present\showcoordinates\listener;

use kim\present\showcoordinates\ShowCoordinates;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\CommandRequestPacket;

class PlayerEventListener implements Listener{
	/**
	 * @priority LOWEST
	 *
	 * @param PlayerJoinEvent $event
	 */
	public function onPlayerJoinEvent(PlayerJoinEvent $event) : void{
		$player = $event->getPlayer();
		ShowCoordinates::setShowCoordinates($player, (bool) $player->namedtag->getByte(ShowCoordinates::TAG_PLUGIN, 0));
	}

	/**
	 * @priority LOWEST
	 *
	 * @param DataPacketReceiveEvent $event
	 */
	public function onDataPacketReceiveEvent(DataPacketReceiveEvent $event) : void{
		$packet = $event->getPacket();
		if($packet instanceof CommandRequestPacket && strpos($packet->command, "/gamerule showcoordinates ") === 0){
			ShowCoordinates::setShowCoordinates($event->getPlayer(), $packet->command === "/gamerule showcoordinates true");

			$event->setCancelled();
		}
	}
}