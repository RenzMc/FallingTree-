<?php

namespace Renz\FallingTree;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\block\Wood;
use pocketmine\block\Block;
use pocketmine\player\Player;
use pocketmine\math\Facing;

class Main extends PluginBase implements Listener {

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onBlockBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        $block = $event->getBlock();

        if ($this->hasFallingTreePermission($player) && $block instanceof Wood) {
            $this->makeTreeFall($block);
        }
    }

    private function hasFallingTreePermission(Player $player): bool {
        return $player->hasPermission("fallingtree.use");
    }

    private function makeTreeFall(Block $block): void {
        $blockPos = $block->getPosition();
        foreach (Facing::ALL as $face) {
            $sideBlock = $block->getSide($face);
            if ($sideBlock instanceof Wood && $block->hasSameTypeId($sideBlock)) {
                $blockPos->getWorld()->useBreakOn($sideBlock->getPosition());
                $this->makeTreeFall($sideBlock);
            }
        }
    }
}
