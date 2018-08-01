<?php

namespace kim\present\showcoordinates\listener;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\{
	CommandRequestPacket, GameRulesChangedPacket
};

class PlayerEventListener implements Listener{
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
				$player->namedtag->setByte("ShowCoordinates", $value = (int) $packet->command === "/gamerule showcoordinates true");

				$pk = new GameRulesChangedPacket();
				$pk->gameRules = [
					"showcoordinates" => [1, $value]
				];
				$player->dataPacket($pk);

				$event->setCancelled();
			}
		}
	}
}