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

namespace nicholass003\nicholasleaderboard\utils;

use nicholass003\nicholasleaderboard\NicholasLeaderboard;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\Config;

class TopLeaderboard
{
    public function __construct(private NicholasLeaderboard $plugin)
    {
        //NOOP
    }

    public function getTopPlayerSkinLeaderboardByType(string $type, Config $data) : CompoundTag
    {
        $player_name = "";
        foreach ($data->getAll() as $name => $stats){
            if ($stats[$type] > 0){
                $top_stats = $stats[$type];
                $player_name = $name;
            }
        }
        return $this->plugin->getServer()->getOfflinePlayerData($player_name);
    }

    public function getTopLeaderboardData(string $type) : string
    {
        $data = NicholasLeaderboard::$data;
        $manager = $this->plugin->getPlayerDataManger();
        $result = "";
        $all_data = $data->getAll();
        if (count($all_data) > 0){
            $player_data = [];
        
            foreach ($all_data as $player_name => $data){
                if (isset($data[$type])){
                    $player_data[$player_name] = $data[$type];
                }
            }
        
            arsort($player_data);
        
            $num = 1;
            foreach ($player_data as $player_name => $value){
                $result .= $manager->getTopFormat($num, $player_name, $type, $value) . "\n";
                if ($num >= 10){
                    break;
                }
                ++$num;
            }
        } else {
            return (T::RED . "Data doesn't exist.");
        }
        return $result;
    }
}