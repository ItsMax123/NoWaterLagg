<?php

declare(strict_types=1);

namespace Max\NoWaterLagg;

use pocketmine\block\Air;
use pocketmine\block\StillLava;
use pocketmine\block\StillWater;
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
				if ($this->isStillBlock($block->getLevel()->getBlockAt($block->getX(), $block->getY() + 1, $block->getZ()))) {
					return;
				}
				foreach ($block->getHorizontalSides() as $horizontalSide) {
					if ($this->isStillBlock($horizontalSide)) {
						return;
					}
					if ($this->isSolidBlock($horizontalSide->getLevel()->getBlockAt($horizontalSide->getX(), $horizontalSide->getY() - 1, $horizontalSide->getZ()))) {
						return;
					}
				}
				if ($this->isFlowingBlock($block->getLevel()->getBlockAt($block->getX(), $block->getY() - 1, $block->getZ()))) {
					for ($y = $block->getY(); $y >= 0; $y--) {
						$blockY = $block->getLevel()->getBlockAt($block->getX(), $y, $block->getZ());
						if ($this->isFlowingBlock($blockY)) {
							$blockY->getLevel()->setBlock($blockY, Block::get(Block::AIR));
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

	public function isStillBlock(Block $block) {
		if (($block instanceof Water or $block instanceof Lava) and ($block->getDamage() == 0)) {
			return True;
		}
		return False;
	}

	public function isSolidBlock(Block $block) {
		if (!$block instanceof Water and !$block instanceof Lava and !$block instanceof Air){
			return True;
		}
		return False;
	}
}
