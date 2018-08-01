<?php

namespace kim\present\showcoordinates\listener;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\{
	CommandRequestPacket, GameRulesChangedPacket
};

class PacketListener implements Listener{
	/**
	 * @priority LOWEST
	 *
	 * @param DataPacketReceiveEvent $event
	 */
	public function onDataPacketReceiveEvent(DataPacketReceiveEvent $event) : void{
		$packet = $event->getPacket();
		$player = $event->getPlayer();
		if($packet instanceof CommandRequestPacket){
			if(strpos($packet->command, "/gamerule showcoordinates ") === 0){
				$pk = new GameRulesChangedPacket();
				$pk->gameRules = [
					"showcoordinates" => [1, $packet->command === "/gamerule showcoordinates true"]
				];
				$player->dataPacket($pk);

				$event->setCancelled();
			}
		}
	}
}