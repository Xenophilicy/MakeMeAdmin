<?php
/*
MADE BY:
 __    __                                          __        __  __  __                     
/  |  /  |                                        /  |      /  |/  |/  |                    
$$ |  $$ |  ______   _______    ______    ______  $$ |____  $$/ $$ |$$/   _______  __    __ 
$$  \/$$/  /      \ /       \  /      \  /      \ $$      \ /  |$$ |/  | /       |/  |  /  |
 $$  $$<  /$$$$$$  |$$$$$$$  |/$$$$$$  |/$$$$$$  |$$$$$$$  |$$ |$$ |$$ |/$$$$$$$/ $$ |  $$ |
  $$$$  \ $$    $$ |$$ |  $$ |$$ |  $$ |$$ |  $$ |$$ |  $$ |$$ |$$ |$$ |$$ |      $$ |  $$ |
 $$ /$$  |$$$$$$$$/ $$ |  $$ |$$ \__$$ |$$ |__$$ |$$ |  $$ |$$ |$$ |$$ |$$ \_____ $$ \__$$ |
$$ |  $$ |$$       |$$ |  $$ |$$    $$/ $$    $$/ $$ |  $$ |$$ |$$ |$$ |$$       |$$    $$ |
$$/   $$/  $$$$$$$/ $$/   $$/  $$$$$$/  $$$$$$$/  $$/   $$/ $$/ $$/ $$/  $$$$$$$/  $$$$$$$ |
                                        $$ |                                      /  \__$$ |
                                        $$ |                                      $$    $$/ 
                                        $$/                                        $$$$$$/  
Edited By:

 ▄██████▄     ▄████████  ▄███████▄  ████████▄      ▄█   ▄█▄ ███    █▄     ▄████████    ▄████████ 
███    ███   ███    ███ ██▀     ▄██ ███    ███    ███ ▄███▀ ███    ███   ███    ███   ███    ███ 
███    ███   ███    ███       ▄███▀ ███    ███    ███▐██▀   ███    ███   ███    ███   ███    ███ 
███    ███   ███    ███  ▀█▀▄███▀▄▄ ███    ███   ▄█████▀    ███    ███  ▄███▄▄▄▄██▀   ███    ███ 
███    ███ ▀███████████   ▄███▀   ▀ ███    ███  ▀▀█████▄    ███    ███ ▀▀███▀▀▀▀▀   ▀███████████ 
███    ███   ███    ███ ▄███▀       ███    ███    ███▐██▄   ███    ███ ▀███████████   ███    ███ 
███    ███   ███    ███ ███▄     ▄█ ███  ▀ ███    ███ ▀███▄ ███    ███   ███    ███   ███    ███ 
 ▀██████▀    ███    █▀   ▀████████▀  ▀██████▀▄█   ███   ▀█▀ ████████▀    ███    ███   ███    █▀  
                                                  ▀                      ███    ███              
*/

namespace MakeMeAdmin;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as Color;
use pocketmine\scheduler\PluginTask;
use pocketmine\command\CommandExecuter;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\Server;
use pocketmine\Player;

use jojoe77777\FormAPI;
use jojoe77777\FormAPI\SimpleForm;

class Main extends PluginBase implements Listener{
    //LOAD
    public function onLoad(){
        $this->getLogger()->info("§eMakeMeAdmin by §6Xenophilicy §eis loading...");
    }
    //ENABLE
    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        @mkdir($this->getDataFolder());
        $this->getLogger()->info($this->getConfig()->get("Enable_Message"));
    }
    //DISABLE
    public function onDisable(){
        $this->getLogger()->info($this->getConfig()->get("Disable_Message"));
    }
    //COMMAND-SENT
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        $player = $sender->getPlayer();
        switch($command->getName()){
            case'mma':
                $this->rankOptions($sender);
                break;
            case'mmadmin':
                $this->rankOptions($sender);
                break;
            case'makemeadmin':
                $sender->sendMessage("§7-=== §6MakeMeAdmin §7===-");
                $sender->sendMessage("§eAuthor: §aXenophillicy");
                $sender->sendMessage("§eDescription: §aEasily change ranks with a command!");
                $sender->sendMessage("§7-====================-");
                break;
    }
    return true;
}
    //SERVER-LIST-FORM
    public function rankOptions($player){
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = new SimpleForm(function (Player $player, $data){
            if($data === null){
                return;
            }
            $command_prefix = $this->getConfig()->get("group_command");
            $sender = $player->getName();
			$rank1_label = $this->getConfig()->get("rank1_label");
			$rank2_label = $this->getConfig()->get("rank2_label");
			$rank3_label = $this->getConfig()->get("rank3_label");
			$rank4_label = $this->getConfig()->get("rank4_label");
			$rank5_label = $this->getConfig()->get("rank5_label");
            $consolecmd = new ConsoleCommandSender();
            switch($data){
                case 0:
                    $this->getServer()->getCommandMap()->dispatch($consolecmd, $command_prefix.' '.$sender.' '.$this->getConfig()->get("rank1_alias"));
					$player->sendMessage("§aYou selected §e".$rank1_label."§a as your rank!");
                    break;
                case 1:
                    $this->getServer()->getCommandMap()->dispatch($consolecmd, $command_prefix.' '.$sender.' '.$this->getConfig()->get("rank2_alias"));
					$player->sendMessage("§aYou selected §e".$rank2_label."§a as your rank!");
                    break;
                case 2:
                    $this->getServer()->getCommandMap()->dispatch($consolecmd, $command_prefix.' '.$sender.' '.$this->getConfig()->get("rank3_alias"));
					$player->sendMessage("§aYou selected §e".$rank3_label."§a as your rank!");
                    break;
                case 3:
                    $this->getServer()->getCommandMap()->dispatch($consolecmd, $command_prefix.' '.$sender.' '.$this->getConfig()->get("rank4_alias"));
					$player->sendMessage("§aYou selected §e".$rank4_label."§a as your rank!");
                    break;
                case 4:
                    $this->getServer()->getCommandMap()->dispatch($consolecmd, $command_prefix.' '.$sender.' '.$this->getConfig()->get("rank5_alias"));
					$player->sendMessage("§aYou selected §e".$rank5_label."§a as your rank!");
                    break;
            }
            return true;
        });
        //MAKE-FORM
        $form->setTitle("§6Server Ranks");
        $form->setContent("§aPick the rank to switch to!");
        $form->addButton($this->getConfig()->get("rank1_label"));
        $form->addButton($this->getConfig()->get("rank2_label"));
        $form->addButton($this->getConfig()->get("rank3_label"));
        $form->addButton($this->getConfig()->get("rank4_label"));
        $form->addButton($this->getConfig()->get("rank5_label"));
        $form->sendToPlayer($player);
    }
}        
