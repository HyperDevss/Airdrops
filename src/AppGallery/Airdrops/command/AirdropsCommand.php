<?php

declare(strict_types=1);

namespace AppGallery\Airdrops\command;

use AppGallery\Airdrops\menu\AirdropsMenu;
use InvalidArgumentException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\lang\KnownTranslationFactory;
use pocketmine\network\mcpe\protocol\DimensionDataPacket;
use pocketmine\network\mcpe\protocol\types\DimensionData;
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use pocketmine\network\mcpe\protocol\types\DimensionNameIds;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class AirdropsCommand extends Command{

    public string $permission;

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if (isset($args[0])){
            switch (strtolower($args[0])){
                case 'edit':
                    $this->parseEdit($sender, $commandLabel, $args);
                    break;
                case 'see':
                    $this->parseSee($sender, $commandLabel, $args);
                    break;
                case 'get':
                    array_shift($args);
                    $this->parseGet($sender, $commandLabel, ($args ?? []));
                    break;
                case 'all':
                    array_shift($args);
                    $this->parseAll($sender, $commandLabel, ($args ?? []));
                    break;
                default:
                    $this->parseUsage($sender, $commandLabel);
            }
        }
    }

    private function parseUsage(CommandSender $sender, string $commandLabel): void{
        $sender->sendMessage(TextFormat::colorize(str_replace('{command}', $commandLabel, $this->getUsage())));
    }

    private function parseGet(CommandSender $sender, string $commandLabel, array $args): void{
        if ($sender instanceof Player && $this->testPermission($sender)){
            if (isset($args[0])){
                if (is_numeric($args[0])){
                    $sender->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::OBSERVER, 1, intval($args[0])));
                    $sender->sendMessage(TextFormat::GREEN . 'You have received x ' . $args[0] . ' Airdrops!');
                } else {
                    $sender->sendMessage(TextFormat::RED . 'Invalid args');
                }
            } else {
                $sender->sendMessage(TextFormat::RED . "Usage: /$commandLabel get (int: number of airdrops)");
            }
        }else{
            $sender->sendMessage(KnownTranslationFactory::pocketmine_command_error_permission($commandLabel)->prefix(TextFormat::RED));
        }
    }

    private function parseAll(CommandSender $sender, string $commandLabel, array $args): void{
        if ($sender instanceof Player && $this->testPermission($sender)){
            if (isset($args[0])){
                if (is_numeric($args[0])){
                    foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
                        $onlinePlayer->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::OBSERVER, 1, intval($args[0])));
                        $onlinePlayer->sendMessage(TextFormat::GREEN . 'You have received x ' . $args[0] . ' Airdrops!');
                    }
                } else {
                    $sender->sendMessage(TextFormat::RED . 'Invalid args');
                }
            } else {
                $sender->sendMessage(TextFormat::RED . "Usage: /$commandLabel get (int: number of airdrops)");
            }
        }else{
            $sender->sendMessage(KnownTranslationFactory::pocketmine_command_error_permission($commandLabel)->prefix(TextFormat::RED));
        }
    }

    private function parseEdit(CommandSender $sender, string $commandLabel, array $args): void{
        if ($sender instanceof Player && $this->testPermission($sender)) new AirdropsMenu($sender, true); else{
            $sender->sendMessage(KnownTranslationFactory::pocketmine_command_error_permission($commandLabel)->prefix(TextFormat::RED));
        }
    }

    private function parseSee(CommandSender $sender, string $commandLabel, array $args): void{
        if ($sender instanceof Player) new AirdropsMenu($sender, true); else{
            $sender->sendMessage(KnownTranslationFactory::pocketmine_command_error_permission($commandLabel)->prefix(TextFormat::RED));
        }
    }
}
