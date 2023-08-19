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

namespace nicholass003\nicholasleaderboard\commands;

use nicholass003\nicholasleaderboard\entities\EntityManager;
use nicholass003\nicholasleaderboard\entities\TopNPC;
use nicholass003\nicholasleaderboard\NicholasLeaderboard;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat as T;
use pocketmine\world\Position;

class NicholasLeaderboardCommand extends Command implements PluginOwned
{
    private array $identifier_entity = ["breaks", "deaths", "jumps", "kills", "places", "xp"];

    public function __construct(private NicholasLeaderboard $plugin)
    {
        parent::__construct("nicholasleaderboard", "NicholasLeaderboard Commands", "/nicholasleaderboard <sub-command>", ["nl"]);
        $this->setPermission("nicholasleaderboard.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$this->testPermission($sender)) return;
        if (!$sender instanceof Player){
            $sender->sendMessage(T::RED . "Use this command in-game please.");
        }

        if (!isset($args[0])){
            $sender->sendMessage(T::RED . "Usage: /nicholasleaderboard <sub-command>");
        } else {
            switch (strtolower($args[0])){
                case "create":
                case "spawn":
                    if (!$sender->hasPermission("nicholasleaderboard.command.create")) return;
                    if (!isset($args[1])){
                        $sender->sendMessage(T::RED . "<identifier> is required!");
                        $sender->sendMessage(T::RED . "Usage: /nicholasleaderboard create <identifier> <type>");
                    } else {
                        if (!isset($args[2])){
                            $sender->sendMessage(T::RED . "<type> is required!");
                            $sender->sendMessage(T::RED . "Usage: /nicholasleaderboard create <identifier> <type>");
                        } else {
                            if (!in_array($args[1], $this->identifier_entity)){
                                $sender->sendMessage(T::RED . "Identifier " . $args[1] . " is missing.");
                            }
                            $top_leaderboard = $this->plugin->getTopLeaderboard();
                            $top = $top_leaderboard->getTopLeaderboardData($args[1]);

                            $top_skin = Human::parseSkinNBT($top_leaderboard->getTopPlayerSkinLeaderboardByType($args[1], NicholasLeaderboard::$data));

                            $title = $this->plugin->getConfig()->get($args[1]);

                            $nbt = CompoundTag::create()
                                ->setTag("Name", new StringTag($top_skin->getSkinId()))
                                ->setTag("Data", new ByteArrayTag($top_skin->getSkinData()))
                                ->setTag("CapeData", new ByteArrayTag($top_skin->getCapeData()))
                                ->setTag("GeometryName", new StringTag($top_skin->getGeometryName()))
                                ->setTag("GeometryData", new ByteArrayTag($top_skin->getGeometryData()))
                                ->setString('type', '');

                            $top_entity = new TopNPC(Location::fromObject($sender->getPosition(), $sender->getWorld()), $top_skin, $nbt);
                            $top_entity->setEntityTopLeaderboardType($args[2]);
                            $top_entity->setEntityIdentifierType($args[1]);
                            $top_entity->setScale($top_entity->getEntityScale($args[2]));
                            $top_entity->setNameTagVisible(true);
                            $top_entity->setNameTagAlwaysVisible(true);
                            $top_entity->setNameTag($title . "\n" . $top);

                            $entity_data_format = EntityManager::getEntityFormatData();
                            $x = $sender->getPosition()->getX();
                            $y = $sender->getPosition()->getY();
                            $z = $sender->getPosition()->getZ();
                            $entity_data_format["identifier"] = $args[1];
                            $entity_data_format["type"] = $args[2];
                            $entity_data_format["world"] = $sender->getWorld()->getFolderName();
                            $entity_data_format["position"]["x"] = $x;
                            $entity_data_format["position"]["y"] = $y;
                            $entity_data_format["position"]["z"] = $z;

                            $entity_data = NicholasLeaderboard::$top_leaderboard_entity;
                            $num = 1;
                            if ($entity_data->exists((string) $num)){
                                ++$num;
                                $entity_data->set((string) $num, $entity_data_format);
                                $entity_data->save();
                            }

                            $top_entity->spawnToAll();

                            $sender->sendMessage(T::GREEN . "TopNPC " . $args[1] . " spawned.");
                        }
                    }
                    break;
                case "delete":
                case "kill":
                case "remove":
                    if (!$sender->hasPermission("nicholasleaderboard.command.delete")) return;
                    foreach ($sender->getWorld()->getEntities() as $entity){
                        if (!isset($args[1])){
                            $sender->sendMessage(T::RED . "<identifier> is required!");
                            $sender->sendMessage(T::RED . "Usage: /nicholasleaderboard delete <identifier> <type> <id>");
                        } else {
                            if (!isset($args[2])){
                                $sender->sendMessage(T::RED . "<type> is required!");
                                $sender->sendMessage(T::RED . "Usage: /nicholasleaderboard delete <identifier> <type> <id>");
                            } else {
                                if (!in_array($args[1], $this->identifier_entity)){
                                    $sender->sendMessage(T::RED . "Identifier " . $args[1] . " is missing.");
                                }
                                if ($entity instanceof TopNPC){
                                    $entity_data = NicholasLeaderboard::$top_leaderboard_entity->getAll();
                                    foreach ($entity_data as $id => $other_data){
                                        $world = $other_data["world"];
                                        $identifier = $other_data["identifier"];
                                        $type = $other_data["type"];
                                        $pos = new Position($other_data["position"]["x"], $other_data["position"]["y"], $other_data["position"]["z"], $this->plugin->getServer()->getWorldManager()->getWorldByName($world));
                                        if ($entity->getPosition() instanceof $pos){
                                            if (!isset($args[3])){
                                                $sender->sendMessage(T::RED . "<id> is required!");
                                                $sender->sendMessage(T::RED . "Usage: /nicholasleaderboard delete <identifier> <type> <id>");
                                            } else {
                                                $config = NicholasLeaderboard::$top_leaderboard_entity;
                                                if (!$config->exists($args[1])){
                                                    $sender->sendMessage(T::RED . "TopNPC with id " . $args[1] . "is missing.");
                                                }
                                                $entity->kill();
                                                $sender->sendMessage(T::GREEN . "TopNPC " . $args[1] . " removed.");
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    break;
                case "entities":
                case "topnpc":
                    if (!$sender->hasPermission("nicholasleaderboard.command.entities")) return;
                    foreach ($sender->getServer()->getWorldManager()->getWorlds() as $world){
                        foreach ($world->getEntities() as $entity){
                            if ($entity instanceof TopNPC){
                                $entity_data = NicholasLeaderboard::$top_leaderboard_entity->getAll();
                                foreach ($entity_data as $id => $other_data){
                                    $world = $other_data["world"];
                                    $identifier = $other_data["identifier"];
                                    $type = $other_data["type"];
                                    $pos = new Position($other_data["position"]["x"], $other_data["position"]["y"], $other_data["position"]["z"], $this->plugin->getServer()->getWorldManager()->getWorldByName($world));
                                    if ($entity->getPosition() instanceof $pos){
                                        $sender->sendMessage(T::GREEN . "TopNPC Custom id: " . $id . ", identifier: " . $identifier . ", type: " . $type . "\n");
                                    }
                                }
                            }
                        }
                    }
                    break;
                case "help":
                    $sender->sendMessage(NicholasLeaderboard::PREFIX);
                    $sender->sendMessage("§a/nicholasleaderboard create <name> <type>");
                    $sender->sendMessage("§a/nicholasleaderboard delete <name> <type>");
                    $sender->sendMessage("§a/nicholasleaderboard list");
                    $sender->sendMessage("§a/nicholasleaderboard top <name>");
                    break;
                case "list":
                    if (!$sender->hasPermission("nicholasleaderboard.command.list")) return;
                    $manager = $this->plugin->getPlayerDataManger();
                    $sender->sendMessage(NicholasLeaderboard::PREFIX);
                    $sender->sendMessage(T::GREEN . "- " . $manager::DATA_BREAKS);
                    $sender->sendMessage(T::GREEN . "- " . $manager::DATA_DEATHS);
                    $sender->sendMessage(T::GREEN . "- " . $manager::DATA_JUMPS);
                    $sender->sendMessage(T::GREEN . "- " . $manager::DATA_KILLS);
                    $sender->sendMessage(T::GREEN . "- " . $manager::DATA_PLACES);
                    $sender->sendMessage(T::GREEN . "- " . $manager::DATA_XP);
                    break;
                case "top":
                    if (!isset($args[1])){
                        $sender->sendMessage(T::RED . "Usage: /nicholasleaderboard top <identifier>");
                    } else {
                        $top_leaderboard = $this->plugin->getTopLeaderboard();
                        $top_message = $top_leaderboard->getTopLeaderboardData($args[1]);
                        $sender->sendMessage($top_message);
                    }
                    break;
            }
        }
    }

    public function getOwningPlugin() : Plugin
    {
        return $this->plugin;
    }
}