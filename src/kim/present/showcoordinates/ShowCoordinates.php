<?php

namespace kim\present\showcoordinates;

use kim\present\showcoordinates\listener\PacketListener;
use pocketmine\plugin\PluginBase;

class ShowCoordinates extends PluginBase{
	/**
	 * Called when the plugin is enabled
	 */
	public function onEnable() : void{
		//Register event listeners
		$this->getServer()->getPluginManager()->registerEvents(new PacketListener(), $this);
	}
}
