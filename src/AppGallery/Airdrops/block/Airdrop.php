<?php

declare(strict_types=1);

namespace AppGallery\Airdrops\block;

use AppGallery\Airdrops\Loader;
use AppGallery\Airdrops\utils\Configuration;
use AppGallery\Airdrops\utils\Randomizer;
use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\Opaque;
use pocketmine\block\tile\Chest as ChestTile;
use pocketmine\block\utils\AnyFacingTrait;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\ToolTier;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;
use pocketmine\world\particle\Particle;
use pocketmine\world\Position;
use pocketmine\world\sound\Sound;

class Airdrop extends Opaque{
    use AnyFacingTrait;

    protected int $time = 6;
    protected ?Player $owner = null;

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::OBSERVER, 1, ItemIds::OBSERVER), "Observer", new BlockBreakInfo(3.5, BlockToolType::PICKAXE, ToolTier::WOOD()->getHarvestLevel()));
    }

    public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null): bool {
        if ($player !== null) {
            $this->setOwner($player);
            $x = abs($player->getLocation()->getFloorX() - $this->getPosition()->getX());
            $y = $player->getLocation()->getFloorY() - $this->getPosition()->getY();
            $z = abs($player->getLocation()->getFloorZ() - $this->getPosition()->getZ());
            if ($y > 0 && $x < 2 && $z < 2) {
                $this->setFacing(Facing::DOWN);
            } elseif ($y < -1 && $x < 2 && $z < 2) {
                $this->setFacing(Facing::UP);
            } else {
                $this->setFacing($player->getHorizontalFacing());
            }
        }
        $tx->addBlock($blockReplace->position, $this);
        $this->getPosition()->getWorld()->scheduleDelayedBlockUpdate($this->getPosition(), 2);
        return true;
    }

    public function onBreak(Item $item, ?Player $player = null): bool{
        return false;
    }

    public function onScheduledUpdate(): void {
        if ($this->time <= 0){
            $this->setChest($this->getPosition());
            return;
        }
        if ($this->getOwner() instanceof Player) $this->getOwner()->sendMessage("Opening airdrop in: " . $this->time);
        if (Configuration::getParticle() instanceof Particle) $this->particle();
        if (Configuration::getSound() instanceof Sound) $this->getPosition()->getWorld()->addSound($this->getPosition(), Configuration::getSound());
        $this->time -= 1;
        $this->getPosition()->getWorld()->scheduleDelayedBlockUpdate($this->getPosition(), 20);
    }

    public function setChest(Position $position): bool{
        $position->getWorld()->setBlock($position, VanillaBlocks::CHEST());
        $tile = $position->getWorld()->getTile($position);
        if ($tile instanceof ChestTile){
            $items = Loader::getInstance()->getInventory()->getContents();
            $rand = new Randomizer($items, Configuration::getMinCountPerItems(), Configuration::getMaxCountPerItems(), Configuration::getMaximumItems());
            foreach ($rand->getItems() as $item) {
                $tile->getInventory()->addItem($item);
            }
            return true;
        }
        return false;
    }

    public function particle(): void{
        $center = $this->getPosition()->round();
        for ($yaw = 0; $yaw <= 10; $yaw += (M_PI * 2) / 20) {
            $xx = -sin($yaw) + $center->x;
            $zz = cos($yaw) + $center->z;
            $yy = $center->y;
            $this->getPosition()->getWorld()->addParticle(new Vector3($xx, $yy + 1.5, $zz), Configuration::getParticle());
        }
    }

    /**
     * @return Player|null
     */
    public function getOwner(): ?Player{
        return $this->owner;
    }

    /**
     * @param Player|null $owner
     */
    public function setOwner(?Player $owner): void{
        $this->owner = $owner;
    }

}