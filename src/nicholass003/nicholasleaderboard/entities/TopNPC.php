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

use pocketmine\entity\Human;
use pocketmine\nbt\tag\CompoundTag;

class TopNPC extends Human
{
    public const ENTITY_TYPE_HUMAN = "human";
    public const ENTITY_TYPE_TEXT = "text";

    public const SCALE_HUMAN = 1.0;
    public const SCALE_TEXT = 0.00000000001;

    public string $leaderboard_type = "";
    public string $identifier_type = "";

    public function getName() : string 
    {
        return "TopNPC";
    }

    public function initEntity(CompoundTag $nbt) : void 
    {
        parent::initEntity($nbt);
        $this->setNameTagAlwaysVisible(true);
        $this->setNameTagVisible(true);
        $this->setMaxHealth(10);
    }

    public function onUpdate(int $currentTick) : bool 
    {
        $this->setMotion($this->getMotion()->withComponents(0, 0, 0));
        $this->setGravity(0.0);

        if ($this->isOnFire()){
            $this->extinguish();
        }

        return parent::onUpdate($currentTick);
    }

    public function saveNBT() : CompoundTag
    {
        $nbt = parent::saveNBT();
        $nbt->setString("type", $this->getEntityTopLeaderboardType());
        return $nbt;
    }


    public function getEntityTopLeaderboardType() : string
    {
        return $this->leaderboard_type;
    }

    public function setEntityTopLeaderboardType(string $type) : void
    {
        $this->leaderboard_type = $type;
    }

    public function getEntityIdentifierType() : string
    {
        return $this->identifier_type;
    }

    public function setEntityIdentifierType(string $identifier) : void
    {
        $this->identifier_type = $identifier;
    }

    public function getEntityScale(string $type) : float
    {
        if ($type === self::ENTITY_TYPE_HUMAN){
            return self::SCALE_HUMAN;
        } elseif ($type === self::ENTITY_TYPE_TEXT){
            return self::SCALE_TEXT;
        }
    }
}