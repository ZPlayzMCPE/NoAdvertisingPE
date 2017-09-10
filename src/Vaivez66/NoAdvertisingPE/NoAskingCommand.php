<?php

namespace Vaivez66\NoAdvertisingPE;

use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor;
use pocketmine\utils\TextFormat as TF;

class NoAskingCommand extends PluginBase implements CommandExecutor{

    public function __construct(NoAskingPE $plugin){
        $this->plugin = $plugin;
    }

    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
        switch(strtolower($cmd->getName())){
            case "nask":
                if($sender->hasPermission("no.asking.pe")) {
                    if (isset($args[0])) {
                        switch ($args[0]) {
                            case "add":
                                if(isset($args[1])){
                                    return $this->plugin->addQuestion($sender, $args[1]);
                                }
                                else{
                                    return false;
                                }
                                break;
                            case "remove":
                                if(isset($args[1])){
                                    return $this->plugin->removeQuestion($sender, $args[1]);
                                }
                                else{
                                    return false;
                                }
                                break;
                            case "list":
                                return $this->plugin->listQuestion($sender);
                                break;
                        }
                    }
                    else{
                        return false;
                    }
                }
                else{
                    $sender->sendMessage(TF::RED . "Not showing due to self-leak information.");
                    return true;
                }
                break;
        }
    }

}
