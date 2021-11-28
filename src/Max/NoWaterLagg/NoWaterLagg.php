<?php

declare(strict_types=1);

namespace Max\NoWaterLagg;

use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;
use pocketmine\block\Block;
use pocketmine\block\Lava;
use pocketmine\block\Water;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\Listener;

class NoWaterLagg extends PluginBase implements Listener {
	public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onBlockUpdate(BlockUpdateEvent $event) {
		$block = $event->getBlock();
		if ($this->isFlowingBlock($block)) {
			if ($block->getDamage() == 1) {
				foreach ($block->getAllSides() as $adjacentBlock) {
					if ($this->isStillBlock($adjacentBlock)) {
						return;
					}
				}
				if ($this->isFlowingBlock($block->getLevel()->getBlockAt($block->getX(), $block->getY() - 1, $block->getZ()))) {
					for ($y = $block->getY(); $y >= 0; $y--) {
						if ($this->isFlowingBlock($block->getLevel()->getBlockAt($block->getX(), $y, $block->getZ()))) {
							$block->getLevel()->setBlock(new Vector3($block->getX(), $y, $block->getZ()), Block::get(Block::AIR));
						} else {
							break;
						}
					}
				}
			}
		}
	}

	public function isFlowingBlock(Block $block) {
		if (($block instanceof Water or $block instanceof Lava) and $block->getDamage() != 0 and $block->getDamage() != 8) {
			return True;
		}
		return False;
	}

	public function isStillBlock (Block $block) {
		if (($block instanceof Water or $block instanceof Lava) and ($block->getDamage() == 0 or $block->getDamage() == 8)) {
			return True;
		}
		return False;
	}
}
