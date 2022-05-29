<?php

declare(strict_types=1);

namespace AppGallery\Airdrops\item;

use AppGallery\Airdrops\utils\Configuration;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\item\ItemBlock;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\utils\TextFormat;

class AirdropItem extends ItemBlock {

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIds::OBSERVER, 1), BlockFactory::getInstance()->get(BlockLegacyIds::OBSERVER, 1));
        $this->setCustomName(TextFormat::colorize(Configuration::getItemName()))->setLore(Configuration::getItemLore());
    }

}