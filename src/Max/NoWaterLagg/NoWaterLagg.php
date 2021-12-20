<?php

namespace Max\NoWaterLagg;

use pocketmine\plugin\PluginBase;
use pocketmine\block\{Block, Air, Water};
use pocketmine\event\Listener;
use pocketmine\event\block\BlockUpdateEvent;

class NoWaterLagg extends PluginBase implements Listener {
	public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onBlockUpdate(BlockUpdateEvent $event) {
		$block = $event->getBlock();
		if (($block instanceof Water) and $block->getDamage() != 0 and $block->getDamage() < 8) {
			if ($block->getDamage() == 1) {
				if ($block->getLevel()->getBlockAt($block->getX(), $block->getY() + 1, $block->getZ()) instanceof Air) {
					foreach ($block->getHorizontalSides() as $horizontalSide) {
						if (($horizontalSide instanceof Water) and ($horizontalSide->getDamage() == 0 or $horizontalSide->getDamage() >= 8)) {
							return;
						}
					}
					if ($this->isNotSource($block->getLevel()->getBlockAt($block->getX(), $block->getY() - 1, $block->getZ()))) {
						for ($y = $block->getY(); $y >= 0; $y--) {
							$blockY = $block->getLevel()->getBlockAt($block->getX(), $y, $block->getZ());
							if ($this->isNotSource($blockY)) {
								$blockY->getLevel()->setBlock($blockY, Block::get(Block::AIR));
							} else {
								break;
							}
						}
					}
				}
			}
		}
	}

	public function isNotSource(Block $block) {
		if (($block instanceof Water) and $block->getDamage() != 0) {
			return True;
		}
		return False;
	}
}
