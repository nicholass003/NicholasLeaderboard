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

use nicholass003\nicholasleaderboard\entities\TopNPC;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerExperienceChangeEvent;
use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Vector2;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\player\Player;

class EventListener implements Listener
{
    public function __construct(private NicholasLeaderboard $plugin)
    {
        //NOOP
    }

    public function onTopEntityDamaged(EntityDamageEvent $event) : void
    {
        $entity = $event->getEntity();
        $cause = $event->getCause();
        if ($cause === $event::CAUSE_ENTITY_ATTACK){
            if ($entity instanceof TopNPC){
                $event->cancel();
            }
        }
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
        $xp = $player->getXpManager()->getXpLevel();
        $manager->updatePlayerData($player, $manager::DATA_XP, $xp);
    }

    public function onPlayerJump(PlayerJumpEvent $event) : void
    {
        $player = $event->getPlayer();
        $manager = $this->plugin->getPlayerDataManger();
        $manager->updatePlayerData($player, $manager::DATA_JUMPS);
    }

    public function onPlayerMove(PlayerMoveEvent $event) : void 
    {
        $player = $event->getPlayer();
        $from = $event->getFrom();
        $to = $event->getTo();

        if ($from->distance($to) < 0.1){
            return;
        }

        $maxDistance = 16;
        foreach ($player->getWorld()->getNearbyEntities($player->getBoundingBox()->expandedCopy($maxDistance, $maxDistance, $maxDistance), $player) as $entity){
            if ($entity instanceof Player){
                continue;
            }

            $xdiff = $player->getLocation()->x - $entity->getLocation()->x;
            $zdiff = $player->getLocation()->z - $entity->getLocation()->z;
            $angle = atan2($zdiff, $xdiff);
            $yaw = (($angle * 180) / M_PI) - 90;
            $ydiff = $player->getLocation()->y - $entity->getLocation()->y;
            $v = new Vector2($entity->getLocation()->x, $entity->getLocation()->z);
            $dist = $v->distance(new Vector2($player->getLocation()->x, $player->getLocation()->z));
            $angle = atan2($dist, $ydiff);
            $pitch = (($angle * 180) / M_PI) - 90;

            if ($entity instanceof TopNPC){
                $pk = new MovePlayerPacket();
                $pk->actorRuntimeId = $entity->getId();
                $pk->position = $entity->getPosition()->add(0, $entity->getEyeHeight(), 0);
                $pk->yaw = $yaw;
                $pk->pitch = $pitch;
                $pk->headYaw = $yaw;
                $pk->onGround = $entity->onGround;

                $player->getNetworkSession()->sendDataPacket($pk);
            }
        }
    }
}