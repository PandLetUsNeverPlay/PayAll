<?php

namespace PayAll;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use onebone\economyapi\EconomyAPI;
use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{

	public function onEnable(){
		$this->getConfig();
                @mkdir($this->getDataFolder());
                $this->saveResource("config.yml");
		$this->saveDefaultConfig();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info("§aPayAll by PandLetUsNeverPlay was loaded!");
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
					$p->sendMessage(str_replace(["{player}"], [$sender], $this->getConfig()->get("payall-message")));
					$p->addTitle(str_replace(["{count}"], [$betrag], $this->getConfig()->get("payall-tite")));
					EconomyAPI::getInstance()->addMoney($p, $betrag);
			}
			EconomyAPI::getInstance()->reduceMoney($sender, $betrag * $count);
			return true;
		    break;
		}
	}
}
