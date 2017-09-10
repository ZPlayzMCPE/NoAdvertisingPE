<?php

namespace Vaivez66\NoAdvertisingPE;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

class NoAskingPE extends PluginBase{

    public $cfg;
    private $format;

    public function onEnable(){
	$this->saveDefaultConfig();
	$this->format = new NoAskingFormat($this);
	$this->cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML, array());
	$this->getServer()->getLogger()->info(TF::GREEN . "NoAskingPE is ready!");
	$this->getServer()->getPluginManager()->registerEvents(new NoAskingListener($this), $this);
	$this->getCommand("nask")->setExecutor(new NoAskingCommand($this));
    }

    /**
     * @return array
     */

    public function getQuestion(){
	$question = (array) $this->cfg->get("question");
	return $question;
    }

    /**
     * @return array
     */

    public function getAllowedQuestion(){
	$allowed = (array) $this->cfg->get("allowed.question");
	return $allowed;
    }

    /**
     * @return mixed
     */

    public function getType(){
	return $this->cfg->get("type");
    }

    /**
     * @return mixed
     */

    public function getMsg(){
	return $this->cfg->get("message");
    }

    /**
     * @return bool
     */

    public function detectSign(){
	return $this->cfg->get('detect.sign') === true;
    }

    /**
     * @return array
     */

    public function getSignLines(){
	return (array) $this->cfg->get('lines');
    }

    /**
     * @return array
     */

    public function getBlockedCmd(){
	return (array) $this->cfg->get('blocked.cmd');
    }

    /**
     * @param $p
     * @param $name
     * @return bool
     */

    public function addQuestion($p, $name){
	$question = $this->getQuestion();
	if(in_array($name, $question)){
	    $p->sendMessage(TF::RED . "That question already exists!");
	    return false;
	}
	$question[] = $name;
	$this->cfg->set("question", $question);
	$this->cfg->save();
	$p->sendMessage(TF::GREEN . "Successfully added " . $name . " into config");
	return true;
    }
    
    /**
     * @param $p
     * @param $name
     * @return bool
     */

    public function removeQuestion($p, $name){
    	$question = $this->getQuestion();
    	$key = array_search($name, $question);
    	if($key === false){
    	    $p->sendMessage(TF::RED . "That question does not exist!");
    	    return false;
    	}
    	unset($question[$key]);
    	$this->cfg->set("question", array_values($question));
    	$this->cfg->save();
    	$p->sendMessage(TF::GREEN . "Successfully removed " . $name . " from config");
    	return true;
    }

    /**
     * @param $p
     * @return bool
     */

    public function listQuestion($p){
	$question = implode("§a\n" . TF::YELLOW . "§2- ", $this->getQuestion());
	$p->sendMessage(TF::YELLOW . "§7Available §eQuestions:");
	$p->sendMessage(TF::YELLOW . "- " . $question);
	return true;
    }

    /**
     * @param $m
     */

    public function broadcastMsg($m){
	foreach($this->getServer()->getOnlinePlayers() as $p){
	    $p->sendMessage($m);
	}
    }

    /**
     * @return mixed
     */

    public function getFormat(){
	return $this->format;
    }
	
    public function onDisable(){
	$this->getServer()->getLogger()->info(TF::RED . "NoAskingPE was disabled!");
    }

}
