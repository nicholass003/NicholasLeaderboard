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

use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerExperienceChangeEvent;
use pocketmine\player\Player;

class EventListener implements Listener
{
    public function __construct(private NicholasLeaderboard $plugin)
    {
        //NOOP
    }

    public function onPlayerBreakBlock(BlockBreakEvent $event) : void
    {
        $player = $event->getPlayer();
        $manager = $this->plugin->getPlayerDataManger();
        $manager->updatePlayerData($player, $manager::DATA_BREAKS);
    }

    public function onPlayerPlaceBlock(BlockPlaceEvent $event) : void
    {
        $player = $event->getPlayer();
        $manager = $this->plugin->getPlayerDataManger();
        $manager->updatePlayerData($player, $manager::DATA_PLACES);
    }

    public function onPlayerDeath(PlayerDeathEvent $event) : void
    {
        $player = $event->getPlayer();
        $manager = $this->plugin->getPlayerDataManger();
        $manager->updatePlayerData($player, $manager::DATA_DEATHS);
        $death_cause = $player->getLastDamageCause();
        if ($death_cause instanceof EntityDamageByEntityEvent){
            $killer = $death_cause->getDamager();
            if ($killer instanceof Player){
                $manager->updatePlayerData($killer, $manager::DATA_KILLS);
            }
        }
    }

    public function onPlayerExperienceChange(PlayerExperienceChangeEvent $event) : void
    {
        $player = $event->getEntity();
        $manager = $this->plugin->getPlayerDataManger();
        $xp = $event->getNewLevel();
        $manager->updatePlayerData($player, $manager::DATA_XP, $xp);
    }
}