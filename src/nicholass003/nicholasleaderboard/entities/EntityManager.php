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

namespace nicholass003\nicholasleaderboard\entities;

use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

class EntityManager
{
    public function registerEntity() : void
    {
        $entityFactory = EntityFactory::getInstance();
        $entityFactory->register(TopNPC::class, function(World $world, CompoundTag $nbt) : TopNPC {
            return new TopNPC(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
        }, ['TopNPC Leaderboard']);
    }

    public static function getEntityFormatData() : array
    {
        return [
            "identifier" => "EntityIdentifierType",
            "type" => "EntityType",
            "world" => "EntityWorld",
            "position" => [
                "x" => 0,
                "y" => 0,
                "z" => 0
            ]
        ];
    }
}