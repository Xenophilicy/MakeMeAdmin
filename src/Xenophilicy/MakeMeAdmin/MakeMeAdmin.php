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

namespace Xenophilicy\MakeMeAdmin;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\{Command,CommandSender,ConsoleCommandSender};
use pocketmine\utils\config;
use pocketmine\scheduler\PluginTask;
use pocketmine\{Server,Player};

use Xenophilicy\MakeMeAdmin\libs\jojoe77777\FormAPI\SimpleForm;

class MakeMeAdmin extends PluginBase implements Listener{

    private $config;

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
        $this->config = new Config($this->getDataFolder()."config.yml", Config::YAML);
        $this->config->getAll();
        $this->ranks = $this->config->get("Ranks");
        $this->getLogger()->info("MakeMeAdmin has been enabled!");
        $pureinstalled = $this->getServer()->getPluginManager()->getPlugin("PurePerms");
        if($pureinstalled == null){
            $this->getLogger()->critical("Required dependancy 'PurePerms' not installed! Disabling plugin...");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
        foreach ($this->ranks as $rank) {
            $value = explode(":", $rank);
            if(isset($value[3])){
                switch($value[3]){
                    case'url':
                        break;
                    case'path':
                        break;
                    default:
                        $this->getLogger()->notice("Invalid image type! Rank: ".$value[0]."§r Image type: ".$value[3]." not supported. ");
                }
            }
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        $name = $command->getName();
        if($name == 'mma' || $name == 'switch' || $name == 'rankch'){
            if($sender->hasPermission("makemeadmin.use.ui")) {
                if ($sender instanceof Player){
                    $this->rankOptions($sender);
                }
                else {
                    $sender->sendMessage("§cThis is an in-game command only!");
                }
            }
            else {
                $sender->sendMessage("§cYou don't have permission to switch ranks!");
            }
        }
        return true;
    }

    public function rankOptions($player){
        $form = new SimpleForm(function (Player $player, $data){
            if ($data === null){
                return;
            }
            else{
                $prefix = $this->config->get("Group_Command");
                $name = $player->getName();
                $consolecmd = new ConsoleCommandSender();
                $value = explode(":", $this->ranks[$data]);
                $value = str_replace("&", "§", $value);
                if($player->hasPermission($value[2])){
                    $this->getServer()->getCommandMap()->dispatch($consolecmd, $prefix.' '.$name.' '.$value[1]);
                    $player->sendMessage("§aYou selected §e".$value[0]."§r§a as your rank!");
                    $this->getLogger()->notice("§e".$name." has changed their group to ".$value[0]);  
                }
                else{
                    $player->sendMessage("§cYou don't have permission to switch to this rank!");
                    return;
                }
            }
            return true;
        });
        $form->setTitle("§6Server Ranks");
        $form->setContent("§aPick the rank to switch to!");
        foreach ($this->ranks as $rank) {
            $value = explode(":", $rank);
            $value = str_replace("&", "§", $value);
            if($player->hasPermission($value[2])){
                if(isset($value[3])){
                    if($value[3] == "url"){
                        $form->addButton($value[0], 1, "https://".$value[4]);
                    }
                    if($value[3] == "path"){
                        $form->addButton($value[0], 0, $value[4]);
                    }
                }
                else{
                    $form->addButton($value[0]);
                }
            }
            else{
                $value = $this->removeColor($value);
                $form->addButton("§r§8".$value[0]." (Locked)");
            }
        }
        $form->sendToPlayer($player);
    }

    public function removeColor($string){
        foreach (['0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f','k','l','m','n','o','o'] as $target){
        $string = str_replace("§".$target, '', $string);
        }
        return $string;
    }
}
