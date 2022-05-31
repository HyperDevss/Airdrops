<?php

declare(strict_types=1);

namespace AppGallery\Airdrops;

use AppGallery\Airdrops\block\Airdrop;
use AppGallery\Airdrops\command\AirdropsCommand;
use AppGallery\Airdrops\database\AirdropsInventory;
use AppGallery\Airdrops\item\AirdropItem;
use AppGallery\Airdrops\utils\Configuration;
use AppGallery\Airdrops\utils\InventorySerializer;
use AppGallery\Airdrops\utils\Randomizer;
use pocketmine\block\BlockFactory;
use pocketmine\item\ItemFactory;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\item\VanillaItems;
use pocketmine\permission\DefaultPermissionNames;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Filesystem;
use pocketmine\utils\NotCloneable;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;

class Loader extends PluginBase{
    use NotCloneable, SingletonTrait;

    private AirdropsInventory $inventory;

    public function onLoad(): void{
        self::$instance = $this;
    }

    public function onEnable(): void{
        $this->loadConfig();
        $this->initBlock();
        $this->initItem();
        $commandMap = $this->getServer()->getCommandMap();
        $command = new AirdropsCommand(Configuration::getCommand('name') ?? 'airdrops', Configuration::getCommand('description') ?? 'Airdrops command!', Configuration::getCommand('usage') ?? "&cUsage: /{command} help", Configuration::getCommand('aliases') ?? []);
        $permission = Configuration::getCommand('permission');
        if ($permission !== false && $permission !== "") {
            $command->setPermission($permission);
        }
        $commandMap->register(Configuration::getCommand('name') ?? 'airdrops', $command);
        if(!InvMenuHandler::isRegistered()){
           InvMenuHandler::register($this);
        }
    }

    private function initBlock(): void{
        BlockFactory::getInstance()->register(new Airdrop(), true);
    }

    private function initItem(): void{
        ItemFactory::getInstance()->register(new AirdropItem(), true);
    }

    public function loadConfig(): void{
        Configuration::load();
        if (!file_exists($this->getDataFolder() . 'contents.txt')){
            Filesystem::safeFilePutContents($this->getDataFolder() . 'contents.txt', InventorySerializer::serialize([5 => VanillaItems::DIAMOND_SWORD()]));
        }
        $contents = file_get_contents($this->getDataFolder() . 'contents.txt');
        $this->inventory = new AirdropsInventory(InventorySerializer::deSerialize($contents));
    }

    /**
     * @return AirdropsInventory
     */
    public function getInventory(): AirdropsInventory{
        return $this->inventory;
    }

}
