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

use nicholass003\nicholasleaderboard\NicholasLeaderboard;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat as T;

class NicholasLeaderboardCommand extends Command implements PluginOwned
{
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
            $sender->sendMessage($this->usageMessage());
        } else {
            switch (strtolower($args[0])){
                case "create":
                    if (!$sender->hasPermission("nicholasleaderboard.command.create")) return;
                    $sender->sendMessage();
                    break;
                case "delete":
                    if (!$sender->hasPermission("nicholasleaderboard.command.delete")) return;
                    $sender->sendMessage();
                    break;
                case "help":
                    $sender->sendMessage(NicholasLeaderboard::PREFIX);
                    $sender->sendMessage("§a/nicholasleaderboard create <name> <type>");
                    $sender->sendMessage("§a/nicholasleaderboard delete <name> <type>");
                    $sender->sendMessage("§a/nicholasleaderboard list");
                    $sender->sendMessage("§a/nicholasleaderboard top <type>");
                    break;
                case "list":
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
                    $data = NicholasLeaderboard::$data;
                    $manager = $this->plugin->getPlayerDataManger();
                    if (!isset($args[1])){
                        $sender->sendMessage(T::RED . "Usage: /nicholasleaderboard top <type>");
                    } else {
                        $result = "";
                        $all_data = $data->getAll();
                        
                        if (count($all_data) > 0){
                            $player_data = [];
                        
                            foreach ($all_data as $player_name => $data){
                                if (isset($data[$args[1]])){
                                    $player_data[$player_name] = $data[$args[1]];
                                }
                            }
                        
                            arsort($player_data);
                        
                            $num = 1;
                            foreach ($player_data as $player_name => $value){
                                $result .= $manager->getTopFormat($num, $player_name, $args[1], $value) . "\n";
                                if ($num >= 10){
                                    break;
                                }
                                ++$num;
                            }
                        } else {
                            $sender->sendMessage(T::RED . "Data doesn't exist.");
                        }
                        $sender->sendMessage($result);
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