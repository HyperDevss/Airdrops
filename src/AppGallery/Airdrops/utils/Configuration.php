<?php

namespace AppGallery\Airdrops\utils;

use AppGallery\Airdrops\Loader;
use pocketmine\permission\DefaultPermissionNames;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\utils\TextFormat;
use pocketmine\world\particle\Particle;
use pocketmine\world\sound\Sound;

final class Configuration{

    private static array $contents = [];

    public static function load(): void{
        Loader::getInstance()->saveResource("config.yml", true);
        if (!file_exists(Loader::getInstance()->getDataFolder() . 'config.yml')){
            Loader::getInstance()->getLogger()->error(TextFormat::RED . "config.yml file was not found in resources directory");
            Loader::getInstance()->getServer()->getPluginManager()->disablePlugin(Loader::getInstance());
            return;
        }
        self::$contents = yaml_parse_file(Loader::getInstance()->getDataFolder() . 'config.yml');
        $permission = self::getCommand('permission');
        if ($permission !== false && $permission !== "") {
            $opRoot = PermissionManager::getInstance()->getPermission(DefaultPermissions::ROOT_OPERATOR);
            DefaultPermissions::registerPermission(new Permission($permission, "Aidrops command permission"), [$opRoot]);
            Loader::getInstance()->getLogger()->info(TextFormat::GREEN . $permission.' has been registered!');
        }
    }

    public static function getParticle(): ?Particle{
        if (isset(self::$contents['particle'])){
            return new self::$contents['particle'];
        }
        return null;
    }

    public static function getSound(): ?Sound{
        if (isset(self::$contents['sound'])){
            return new self::$contents['sound'];
        }
        return null;
    }

    public static function getCommand(string $arg): mixed{
        if (isset(self::$contents['command']) && isset(self::$contents['command'][$arg])){
            return self::$contents['command'][$arg];
        }
        return false;
    }

    public static function getMaximumItems(): int{
        if (isset(self::$contents['maximum-items'])){
            return self::$contents['maximum-items'];
        }
        return 10;
    }

    public static function getMinCountPerItems(): int{
        if (isset(self::$contents['min-count-per-items'])){
            return self::$contents['min-count-per-items'];
        }
        return 2;
    }

    public static function getMaxCountPerItems(): int{
        if (isset(self::$contents['max-count-per-items'])){
            return self::$contents['max-count-per-items'];
        }
        return 10;
    }

    public static function getItemName(): string{
        if (isset(self::$contents['item-name'])){
            return self::$contents['item-name'];
        }
        return '&l&eAirdrops';
    }

    public static function getItemLore(): array{
        if (isset(self::$contents['item-lore'])){
            return self::$contents['item-lore'];
        }
        return ["&l&eAirdrops Lore", "&l&eAirdrops Lore 2", "&l&eAirdrops Lore 3"];
    }
}
