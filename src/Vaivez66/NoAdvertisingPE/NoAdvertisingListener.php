<?php

namespace Vaivez66\NoAdvertisingPE;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\utils\TextFormat;

class NoAdvertisingListener implements Listener{

    public function __construct(NoAdvertising $plugin){
        $this->plugin = $plugin;
    }

    public function onChat(PlayerChatEvent $event){
        $p = $event->getPlayer();
        $msg = $event->getMessage();
        $domain = $this->plugin->getDomain();
        $allowed = $this->plugin->getAllowedDomain();
        $type = $this->plugin->getType();
        $m = $this->plugin->getMsg();
        $m = str_replace("{player}", $p->getName(), $m);
        $m = $this->plugin->getFormat()->translate($m);
        if($p->hasPermission('no.advertising.pe.bypass')){
            return true;
        }
        foreach($allowed as $a){
            if(stripos($msg, $a) !== false){
                return true;
            }
        }
        foreach($domain as $d){
            if((stripos($msg, $d) !== false) || (preg_match("/[0-9]+\.[0-9]+/i", $msg))){
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
            if($p->hasPermission('no.advertising.pe.bypass')){
                return true;
            }
            foreach($lines as $line){
                foreach($this->plugin->getAllowedDomain() as $a){
                    if(stripos($line, $a) !== false){
                        return true;
                    }
                }
                foreach($this->plugin->getDomain() as $d){
                    if(stripos($line, $d) !== false) {
                        for ($i = 0; $i <= 3; $i++) {
                            $event->setLine($i, $sign[$i]);
                        }
                        $p->sendMessage(TF::RED . 'Do not try to advertising, ' . $p->getName());
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
        if ($p->hasPermission('no.advertising.pe.bypass')) {
            return true;
        }
        foreach ($this->plugin->getAllowedDomain() as $a) {
            if (stripos($m, $a) !== false) {
                return true;
            }
        }
        if(in_array($cmd, $this->plugin->getBlockedCmd())) {
            foreach ($this->plugin->getDomain() as $d) {
                if (stripos($m, $d) !== false) {
                    $event->setCancelled(true);
                    $p->sendMessage(TF::RED . 'Do not try to advertising with ' . $cmd . ', ' . $p->getName());
                }
            }
        }
    }

}
