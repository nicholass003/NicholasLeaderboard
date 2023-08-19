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

use nicholass003\nicholasleaderboard\commands\NicholasLeaderboardCommand;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

class NicholasLeaderboard extends PluginBase
{
    use SingletonTrait;

    public const PREFIX = "§b[§eNicholasLeaderboard§b]";
    public static Config $data;
    public PlayerDataManager $player_data_manager;

    protected function onLoad() : void
    {
        $this->saveDefaultConfig();
    }

    protected function onEnable() : void
    {
        self::setInstance($this);
        self::$data = new Config($this->getDataFolder() . "data.json", Config::JSON);

        $this->player_data_manager = new PlayerDataManager();
        $this->player_data_manager->init($this);

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);

        $this->registerCommands();
    }

    protected function onDisable() : void
    {

    }

    private function registerCommands() : void
    {
        $this->getServer()->getCommandMap()->register("nicholasleaderboard", new NicholasLeaderboardCommand($this));
    }

    public function getPlayerDataManger() : PlayerDataManager
    {
        return $this->player_data_manager;
    }
}