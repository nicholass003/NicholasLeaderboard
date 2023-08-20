<?php

/*
 *       _      _           _                ___   ___ ____  
 *      (_)    | |         | |              / _ \ / _ \___ \ 
 * _ __  _  ___| |__   ___ | | __ _ ___ ___| | | | | | |__) |
 *| '_ \| |/ __| '_ \ / _ \| |/ _` / __/ __| | | | | | |__ < 
 *| | | | | (__| | | | (_) | | (_| \__ \__ \ |_| | |_| |__) |
 *|_| |_|_|\___|_| |_|\___/|_|\__,_|___/___/\___/ \___/____/ 
 *                                                           
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * @author nicholass003
 * @link https://github.com/nicholass003/
 * 
 */


declare(strict_types=1);

namespace nicholass003\nicholasleaderboard;

use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;

class PlayerDataManager
{
    public const DATA_BREAKS = "breaks";
    public const DATA_DEATHS = "deaths";
    public const DATA_JUMPS = "jumps";
    public const DATA_KILLS = "kills";
    public const DATA_PLACES = "places";
    public const DATA_XP = "xp";

    public function init(NicholasLeaderboard $plugin) : void
    {
        $plugin_manager = $plugin->getServer()->getPluginManager();

        $plugin_manager->registerEvent(PlayerJoinEvent::class, function(PlayerJoinEvent $event) : void {
            $player = $event->getPlayer();
            $this->setPlayerData($player);
        }, EventPriority::MONITOR, $plugin);
    }

    public function getPlayerData(string $type) : mixed
    {
        $config = NicholasLeaderboard::$data;
        $name = $player->getName();
        return $config->get($name);
    }

    public function updatePlayerData(Player $player, string $type, int $xp = 0) : void
    {
        $config = NicholasLeaderboard::$data;
        $name = $player->getName();
        $old_data = $config->get($name);
        if ($type === self::DATA_XP){
            $config->setNested($name . "." . $type, $xp);
            $config->save();
        } else {
            $old_value = $old_data[$type];
            $config->setNested($name . "." . $type, $old_value + 1);
            $config->save();
        }
    }

    public function setPlayerData(Player $player) : void
    {
        $config = NicholasLeaderboard::$data;
        $name = $player->getName();
        $format = $this->getDataFormat();
        if (!$config->exists($name)){
            $config->set($name, $format);
            $config->save();
        }
    }

    public function resetPlayerData(Player $player, string $type) : void
    {
        $config = NicholasLeaderboard::$data;
        $name = $player->getName();
        $format = $this->getDataFormat();
        $config->set($name, $format[$type] * 0);
        $config->save();
    }

    public function getTopFormat(int $rank, string $player_name, string $type, int $value) : string
    {
        return str_replace(["{rank}", "{player}", "{name}", "{value}"], [(string) $rank, $player_name, $type, (string) $value], NicholasLeaderboard::getInstance()->getConfig()->get("top-message-format"));
    }

    public function getDataFormat() : array
    {
        return [
            self::DATA_BREAKS => 0,
            self::DATA_DEATHS => 0,
            self::DATA_JUMPS => 0,
            self::DATA_KILLS => 0,
            self::DATA_PLACES => 0,
            self::DATA_XP => 0
        ];
    }
}