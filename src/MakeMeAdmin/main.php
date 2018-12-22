<?php
# MADE BY:
#  __    __                                          __        __  __  __                     
# /  |  /  |                                        /  |      /  |/  |/  |                    
# $$ |  $$ |  ______   _______    ______    ______  $$ |____  $$/ $$ |$$/   _______  __    __ 
# $$  \/$$/  /      \ /       \  /      \  /      \ $$      \ /  |$$ |/  | /       |/  |  /  |
#  $$  $$<  /$$$$$$  |$$$$$$$  |/$$$$$$  |/$$$$$$  |$$$$$$$  |$$ |$$ |$$ |/$$$$$$$/ $$ |  $$ |
#   $$$$  \ $$    $$ |$$ |  $$ |$$ |  $$ |$$ |  $$ |$$ |  $$ |$$ |$$ |$$ |$$ |      $$ |  $$ |
#  $$ /$$  |$$$$$$$$/ $$ |  $$ |$$ \__$$ |$$ |__$$ |$$ |  $$ |$$ |$$ |$$ |$$ \_____ $$ \__$$ |
# $$ |  $$ |$$       |$$ |  $$ |$$    $$/ $$    $$/ $$ |  $$ |$$ |$$ |$$ |$$       |$$    $$ |
# $$/   $$/  $$$$$$$/ $$/   $$/  $$$$$$/  $$$$$$$/  $$/   $$/ $$/ $$/ $$/  $$$$$$$/  $$$$$$$ |
#                                         $$ |                                      /  \__$$ |
#                                         $$ |                                      $$    $$/ 
#                                         $$/                                        $$$$$$/           
# Editied By:
#
#            /$$   /$$                                        
#           | $$  /$$/                                        
#   /$$$$$$ | $$ /$$/   /$$$$$$  /$$$$$$$$  /$$$$$$  /$$$$$$$ 
#  /$$__  $$| $$$$$/   /$$__  $$|____ /$$/ |____  $$| $$__  $$
# | $$  \ $$| $$  $$  | $$  \ $$   /$$$$/   /$$$$$$$| $$  \ $$
# | $$  | $$| $$\  $$ | $$  | $$  /$$__/   /$$__  $$| $$  | $$
# |  $$$$$$/| $$ \  $$|  $$$$$$$ /$$$$$$$$|  $$$$$$$| $$  | $$
#  \______/ |__/  \__/ \____  $$|________/ \_______/|__/  |__/
#                           | $$                              
#                           | $$                              
#                           |__/                              

namespace MakeMeAdmin;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\config;
use pocketmine\scheduler\PluginTask;
use pocketmine\command\CommandExecuter;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

use jojoe77777\FormAPI;
use jojoe77777\FormAPI\SimpleForm;

class Main extends PluginBase implements Listener{

    private $config;

    public function onLoad(){
        $this->saveDefaultConfig();
        $this->config = new Config($this->getDataFolder()."config.yml", Config::YAML);
        $this->config->getAll();
        $this->getLogger()->info("§eMakeMeAdmin by §6Xenophilicy §eis loading...");
    }

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info("§6MakeMeAdmin§a has been enabled!");
        $pureinstalled = $this->getServer()->getPluginManager()->getPlugin("PurePerms");
        $forminstalled = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        if($forminstalled == null){
            $this->getLogger()->critical("Required dependancy 'FormAPI' not installed! Disabling plugin...");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
        if($pureinstalled == null){
            $this->getLogger()->critical("Required dependancy 'PurePerms' not installed! Disabling plugin...");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
    }

    public function onDisable(){
        $this->getLogger()->info("§6MakeMeAdmin§c has been disabled!");   
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if($sender->hasPermission("makemeadmin.use.ui")) {
            if ($sender instanceof Player){
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
            else {
                $sender->sendMessage("§cThis is an in-game command only!");
                return true;
            }
        }
        else {
            $sender->sendMessage("§cYou don't have permission to switch ranks!");
        }
        return true;
    }

    public function removeColor($string){
        $string = str_replace('§0', '', $string);
        $string = str_replace('§1', '', $string);
        $string = str_replace('§2', '', $string);
        $string = str_replace('§3', '', $string);
        $string = str_replace('§4', '', $string);
        $string = str_replace('§5', '', $string);
        $string = str_replace('§6', '', $string);
        $string = str_replace('§7', '', $string);
        $string = str_replace('§8', '', $string);
        $string = str_replace('§9', '', $string);
        $string = str_replace('§a', '', $string);
        $string = str_replace('§b', '', $string);
        $string = str_replace('§c', '', $string);
        $string = str_replace('§d', '', $string);
        $string = str_replace('§e', '', $string);
        $string = str_replace('§f', '', $string);
        $string = str_replace('§k', '', $string);
        $string = str_replace('§l', '', $string);
        $string = str_replace('§m', '', $string);
        $string = str_replace('§n', '', $string);
        $string = str_replace('§o', '', $string);
        $string = str_replace('§r', '', $string);
        return $string;
    }

    public function rankOptions($player){
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = new SimpleForm(function (Player $player, $data){
            if($data === null){
                return;
            }
            $prefix = $this->config->get("group_command");
            $sender = $player->getName();
            $consolecmd = new ConsoleCommandSender();
            switch($data){
                case 0:
                    if($player->hasPermission("makemeadmin.use.1")){
                        $this->getServer()->getCommandMap()->dispatch($consolecmd, $prefix.' '.$sender.' '.$this->config->getNested("rank1.alias"));
                        $player->sendMessage("§aYou selected §e".$this->config->getNested("rank1.label")."§r§a as your rank!");
                        $this->getLogger()->notice("§e".$sender." has changed their group to ".$this->config->getNested("rank1.label"));  
                    }
                    else{
                        $player->sendMessage("§cYou don't have permission to switch to this rank!");
                    }
                    break;
                case 1:
                    if($player->hasPermission("makemeadmin.use.2")){
                        $this->getServer()->getCommandMap()->dispatch($consolecmd, $prefix.' '.$sender.' '.$this->config->getNested("rank2.alias"));
                        $player->sendMessage("§aYou selected §e".$this->config->getNested("rank2.label")."§r§a as your rank!");
                        $this->getLogger()->notice("§e".$sender." has changed their group to ".$this->config->getNested("rank2.label"));  
                    }
                    else{
                        $player->sendMessage("§cYou don't have permission to switch to this rank!");
                    }
                    break;
                case 2:
                    if($player->hasPermission("makemeadmin.use.3")){
                        $this->getServer()->getCommandMap()->dispatch($consolecmd, $prefix.' '.$sender.' '.$this->config->getNested("rank3.alias"));
                        $player->sendMessage("§aYou selected §e".$this->config->getNested("rank3.label")."§r§a as your rank!");
                        $this->getLogger()->notice("§e".$sender." has changed their group to ".$this->config->getNested("rank3.label"));  
                    }
                    else{
                        $player->sendMessage("§cYou don't have permission to switch to this rank!");
                    }
                    break;
                case 3:
                    if($player->hasPermission("makemeadmin.use.4")){
                        $this->getServer()->getCommandMap()->dispatch($consolecmd, $prefix.' '.$sender.' '.$this->config->getNested("rank4.alias"));
                        $player->sendMessage("§aYou selected §e".$this->config->getNested("rank4.label")."§r§a as your rank!");
                        $this->getLogger()->notice("§e".$sender." has changed their group to ".$this->config->getNested("rank4.label"));  
                    }
                    else{
                        $player->sendMessage("§cYou don't have permission to switch to this rank!");
                    }
                    break;
                case 4:
                    if($player->hasPermission("makemeadmin.use.5")){
                        $this->getServer()->getCommandMap()->dispatch($consolecmd, $prefix.' '.$sender.' '.$this->config->getNested("rank5.alias"));
                        $player->sendMessage("§aYou selected §e".$this->config->getNested("rank5.label")."§r§a as your rank!");
                        $this->getLogger()->notice("§e".$sender." has changed their group to ".$this->config->getNested("rank5.label"));  
                    }
                    else{
                        $player->sendMessage("§cYou don't have permission to switch to this rank!");
                    }
                    break;
            }
            return true;
        });
        $form->setTitle("§6Server Ranks");
        $form->setContent("§aPick the rank to switch to!");
        $label1 = $this->config->getNested("rank1.label");
        $label2 = $this->config->getNested("rank2.label");
        $label3 = $this->config->getNested("rank3.label");
        $label4 = $this->config->getNested("rank4.label");
        $label5 = $this->config->getNested("rank5.label");
        if($player->hasPermission("makemeadmin.use.1")){
            $form->addButton($label1);
        }
        else{
            $label1 = $this->removeColor($label1);
            $form->addButton("§r§8".$label1." (Locked)");
        }
        if($player->hasPermission("makemeadmin.use.2")){
            $form->addButton($label2);
        }
        else{
            $label2 = $this->removeColor($label2);
            $form->addButton("§r§8".$label2." (Locked)");
        }
        if($player->hasPermission("makemeadmin.use.3")){
            $form->addButton($label3);
        }
        else{
            $label3 = $this->removeColor($label3);
            $form->addButton("§r§8".$label3." (Locked)");
        }
        if($player->hasPermission("makemeadmin.use.4")){
            $form->addButton($label4);
        }
        else{
            $label4 = $this->removeColor($label4);
            $form->addButton("§r§8".$label4." (Locked)");
        }
        if($player->hasPermission("makemeadmin.use.5")){
            $form->addButton($label5);
        }
        else{
            $label5 = $this->removeColor($label5);
            $form->addButton("§r§8".$label5." (Locked)");
        }
        $form->sendToPlayer($player);
    }
}
