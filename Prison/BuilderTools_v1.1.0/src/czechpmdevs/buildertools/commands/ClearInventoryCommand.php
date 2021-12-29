<?php

/**
 * Copyright 2018 CzechPMDevs
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace czechpmdevs\buildertools\commands;

use czechpmdevs\buildertools\BuilderTools;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

/**
 * Class ClearInventoryCommand
 * @package buildertools\commands
 */
class ClearInventoryCommand extends Command implements PluginIdentifiableCommand {

    /**
     * ClearInventoryCommand constructor.
     */
    public function __construct() {
        parent::__construct("/clearinventory", "Clear inventory", null, ["/ci"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can be used only in-game!");
            return;
        }
        if(!$sender->hasPermission("bt.cmd.clearinventory")) {
            $sender->sendMessage("§cYou do have not permissions to use this command!");
            return;
        }

        $removed = 0;
        foreach ($sender->getInventory()->getContents() as $index => $item) {
            $sender->getInventory()->setItem($index, Item::get(Item::AIR));
            $removed++;
        }

        $sender->sendMessage(BuilderTools::getPrefix()."§aInventory cleared, $removed items removed.");
    }

    /**
     * @return Plugin|BuilderTools $plugin
     */
    public function getPlugin(): Plugin {
        return BuilderTools::getInstance();
    }
}