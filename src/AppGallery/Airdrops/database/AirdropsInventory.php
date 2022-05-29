<?php

namespace AppGallery\Airdrops\database;

use AppGallery\Airdrops\Loader;
use AppGallery\Airdrops\utils\InventorySerializer;
use pocketmine\utils\Filesystem;

final class AirdropsInventory{

    public function __construct(
        private array $contents
    ){}

    /**
     * @return array
     */
    public function getContents(): array{
        return $this->contents;
    }

    /**
     * @param array $contents
     * @return AirdropsInventory
     */
    public function setContents(array $contents): AirdropsInventory{
        $this->contents = $contents;
        return $this;
    }

    public function save(): void{
        Filesystem::safeFilePutContents(Loader::getInstance()->getDataFolder() . 'contents.txt', InventorySerializer::serialize($this->getContents()));
    }

}