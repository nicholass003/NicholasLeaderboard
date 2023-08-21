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

namespace nicholass003\nicholasleaderboard\task;

use nicholass003\nicholasleaderboard\entities\TopNPC;
use nicholass003\nicholasleaderboard\NicholasLeaderboard;
use pocketmine\entity\Human;
use pocketmine\scheduler\Task;
use pocketmine\world\Position;

class UpdateTask extends Task
{
    public function __construct(private NicholasLeaderboard $plugin)
    {
        //NOOP
    }

    public function onRun() : void
    {
        $top_leaderboard = $this->plugin->getTopLeaderboard();
        foreach ($this->plugin->getServer()->getWorldManager()->getWorlds() as $world){
            foreach ($world->getEntities() as $entity){
                if ($entity instanceof TopNPC){
                    $entity_data = NicholasLeaderboard::$top_leaderboard_entity->getAll();
                    if (empty($entity_data)) return;
                    foreach ($entity_data as $id => $other_data){
                        $world = $other_data["world"];
                        $identifier = $other_data["identifier"];
                        $type = $other_data["type"];
                        $pos = new Position($other_data["position"]["x"], $other_data["position"]["y"], $other_data["position"]["z"], $this->plugin->getServer()->getWorldManager()->getWorldByName($world));
                            if ($entity->getPosition() instanceof $pos){
                                $top_skin = Human::parseSkinNBT($top_leaderboard->getTopPlayerSkinLeaderboardByType($identifier, NicholasLeaderboard::$data));
                                $update_top = $top_leaderboard->getTopLeaderboardData($identifier);
                                $title = $this->plugin->getConfig()->get($identifier);
                                if ($type === "human"){
                                    $player_data = $top_leaderboard->getTopDataPlayerName($identifier);
                                    $entity->setScale($entity->getEntityScale($type));
                                    foreach ($player_data as $player_name => $player_value){
                                        $entity->setNameTag(str_replace(["{player}", "{identifier}", "{value}"], [$player_name, $identifier, $player_value], $this->plugin->getConfig()->get("player-name-format")));
                                    }
                                } else {
                                    $entity->setScale($entity->getEntityScale($type));
                                    $entity->setNameTag($title . "\n" . $update_top);
                                }
                                $entity->setSkin($top_skin);
                            }
                    }
                }
            }
        }
    }
}