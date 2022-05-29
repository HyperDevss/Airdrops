<?php

namespace AppGallery\Airdrops\menu;

use AppGallery\Airdrops\Loader;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class AirdropsMenu {

    public function __construct(Player $player, bool $editable = false){
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $menu->setName(TextFormat::GREEN . ($editable ? 'Airdrops Edit' : 'Airdrops Menu'));
        $menu->getInventory()->setContents(Loader::getInstance()->getInventory()->getContents());
        if (!$editable) {
            $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
                return $transaction->discard();
            });
        } else {
            $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory): void {
                Loader::getInstance()->getInventory()->setContents($inventory->getContents())->save();
                $player->sendMessage(TextFormat::GREEN . 'Haz puesto el contenido de los airdrops!');
            });
        }
        $menu->send($player);
    }

}