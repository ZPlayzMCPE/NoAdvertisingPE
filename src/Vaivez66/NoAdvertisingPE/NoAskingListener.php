<?php

namespace Vaivez66\NoAdvertisingPE;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\utils\TextFormat as TF;

class NoAskingListener implements Listener{

    public function __construct(NoAskingPE $plugin){
        $this->plugin = $plugin;
    }

    public function onChat(PlayerChatEvent $event){
        $p = $event->getPlayer();
        $msg = $event->getMessage();
        $question = $this->plugin->getQuestion();
        $allowed = $this->plugin->getAllowedQuestion();
        $type = $this->plugin->getType();
        $m = $this->plugin->getMsg();
        $m = str_replace("{player}", $p->getName(), $m);
        $m = $this->plugin->getFormat()->translate($m);
        if($p->hasPermission('no.asking.pe.bypass')){
            return;
        }
        foreach($allowed as $a){
            if(stripos($msg, $a) !== false){
                return;
            }
        }
        foreach($question as $q){
            if((stripos($msg, $q) !== false) || (preg_match("/[0-9]+\.[0-9]+/i", $msg))){
                switch($type){
                    case "broadcast":
                        $event->setCancelled(true);
                        $this->plugin->broadcastMsg($m);
                        break;
                    case "block":
                        $event->setCancelled(true);
                        $p->sendMessage($m);
                        break;
                    case "kick":
                        $event->setCancelled(true);
                        $p->kick($m, true);
                }
            }
        }
    }

    public function onSign(SignChangeEvent $event){
        if($this->plugin->detectSign()){
            $lines = $event->getLines();
            $p = $event->getPlayer();
            $sign = $this->plugin->getSignLines();
            if($p->hasPermission('no.asking.pe.bypass')){
                return;
            }
            foreach($lines as $line){
                foreach($this->plugin->getAllowedQuestion() as $a){
                    if(stripos($line, $a) !== false){
                        return;
                    }
                }
                foreach($this->plugin->getQuestion() as $q){
                    if(stripos($line, $q) !== false) {
                        for ($i = 0; $i <= 3; $i++) {
                            $event->setLine($i, $sign[$i]);
                        }
                        $p->sendMessage(TF::RED . 'Please do not ask this question, ' . $p->getName());
                    }
                }
            }
        }
    }

    public function onCmd(PlayerCommandPreprocessEvent $event){
        $msg = explode(' ', $event->getMessage());
        $cmd = array_shift($msg);
        $p = $event->getPlayer();
        $m = implode(' ', $msg);
        if ($p->hasPermission('no.asking.pe.bypass')) {
            return;
        }
        foreach ($this->plugin->getAllowedQuestion() as $a) {
            if (stripos($m, $a) !== false) {
                return;
            }
        }
        if(in_array($cmd, $this->plugin->getBlockedCmd())) {
            foreach ($this->plugin->getQuestion() as $q) {
                if (stripos($m, $q) !== false) {
                    $event->setCancelled(true);
                    $p->sendMessage(TF::RED . 'Please do not ask this question with ' . $cmd . ', ' . $p->getName());
                }
            }
        }
    }

}
