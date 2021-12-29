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

namespace czechpmdevs\buildertools\commands;

use czechpmdevs\buildertools\BuilderTools;
use czechpmdevs\buildertools\editors\Editor;
use czechpmdevs\buildertools\editors\Filler;
use czechpmdevs\buildertools\editors\Replacement;
use czechpmdevs\buildertools\Selectors;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

/**
 * Class ReplaceCommand
 * @package buildertools\commands
 */
class ReplaceCommand extends Command implements PluginIdentifiableCommand {

    /**
     * ReplaceCommand constructor.
     */
    public function __construct() {
        parent::__construct("/replace", "Replace selected blocks", null, []);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can be used only in-game!");
            return;
        }
        if(!$sender->hasPermission("bt.cmd.replace")) {
            $sender->sendMessage("§cYou do have not permissions to use this command!");
            return;
        }
        if(!isset($args[0]) || !isset($args[1])) {
            $sender->sendMessage("§cUsage: §7//replace <BlocksToReplace - id1:meta1,id2:meta2,...> <Blocks - id1:meta1,id2:meta2,...>");
            return;
        }
        if(!Selectors::isSelected(1, $sender)) {
            $sender->sendMessage(BuilderTools::getPrefix()."§cFirst you need to select the first position.");
            return;
        }
        if(!Selectors::isSelected(2, $sender)) {
            $sender->sendMessage(BuilderTools::getPrefix()."§cFirst you need to select the second position.");
            return;
        }
        $firstPos = Selectors::getPosition($sender, 1);
        $secondPos = Selectors::getPosition($sender, 2);
        if($firstPos->getLevel()->getName() != $secondPos->getLevel()->getName()) {
            $sender->sendMessage(BuilderTools::getPrefix()."§cPositions must be in same level");
            return;
        }

        $startTime = microtime(true);

        /** @var Replacement $replacement */
        $replacement = BuilderTools::getEditor(Editor::REPLACEMENT);
        $list = $replacement->prepareReplace($firstPos, $secondPos, $firstPos->getLevel(), $args[0], $args[1]);

        /** @var Filler $filler */
        $filler = BuilderTools::getEditor(Editor::FILLER);
        $result = $filler->fill($sender, $list);

        $count = $result->countBlocks;
        $sender->sendMessage(BuilderTools::getPrefix()."§aSelected area filled in ".round(microtime(true)-$startTime, 2)." ({$count} blocks changed)!");
    }

    /**
     * @return Plugin|BuilderTools $builderTools
     */
    public function getPlugin(): Plugin {
        return BuilderTools::getInstance();
    }
}