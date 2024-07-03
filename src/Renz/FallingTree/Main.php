<?php

namespace Renz\FallingTree;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class Main extends PluginBase implements Listener {

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onBlockBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        $block = $event->getBlock();

        if ($this->hasFallingTreePermission($player) && $this->isLog($block)) {
            $this->makeTreeFall($block);
        }
    }

    private function hasFallingTreePermission(Player $player): bool {
        return $player->hasPermission("fallingtree.use");
    }

    private function isLog(Block $block): bool {
        $logBlocks = [
            VanillaBlocks::OAK_LOG(),
            VanillaBlocks::SPRUCE_LOG(),
            VanillaBlocks::BIRCH_LOG(),
            VanillaBlocks::JUNGLE_LOG(),
            VanillaBlocks::ACACIA_LOG(),
            VanillaBlocks::DARK_OAK_LOG()
        ];

        foreach ($logBlocks as $logBlock) {
            if ($block->getTypeId() === $logBlock->getTypeId()) {
                return true;
            }
        }

        return false;
    }

    private function makeTreeFall(Block $block): void {
        $world = $block->getPosition()->getWorld();
        $x = $block->getPosition()->getX();
        $y = $block->getPosition()->getY();
        $z = $block->getPosition()->getZ();

        for ($i = $y + 1; $i < $world->getMaxY(); $i++) {
            $currentBlock = $world->getBlockAt($x, $i, $z);
            if ($this->isLog($currentBlock)) {
                $world->setBlockAt($x, $i, $z, VanillaBlocks::AIR());
                $world->dropItem(new Vector3($x, $i, $z), $currentBlock->asItem());
            } else {
                break;
            }
        }
    }
}
