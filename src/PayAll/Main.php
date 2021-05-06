<?php

namespace PayAll;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use onebone\economyapi\EconomyAPI;
use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\Player;

class Main extends PluginBase implements Listener{

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	
	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args):bool{
		switch($cmd->getName()){
			case "payall":
			if(!$sender instanceof Player){
				$sender->sendMessage("Use this command In-Game!");
				return true;
			}
			if(empty($args[0])){
				$sender->sendMessage("§cUsage: /payall <amount>");
				return true;
			}
			$money = EconomyAPI::getInstance()->myMoney($sender);
			$betrag = $args[0];
			$count = count($this->getServer()->getOnlinePlayers());
			$name = $sender->getName();
			if($money < $betrag * $count){
					$sender->sendMessage("§cYou don't have enough money to pay $betrag to all players!");
				return true;
			}
			foreach($this->getServer()->getOnlinePlayers() as $p){
					$p->sendMessage("§l§6$name payed §b$betrag §6to all online players!");
					
					$p->addTitle("§l§aPayAll §f: §b$betrag");
					EconomyAPI::getInstance()->addMoney($p, $betrag);
			}
			EconomyAPI::getInstance()->reduceMoney($sender, $betrag * $count);
			return true;
		    break;
		}
	}
}