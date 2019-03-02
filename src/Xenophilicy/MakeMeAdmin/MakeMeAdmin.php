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
use pocketmine\utils\{config,TextFormat as TF};
use pocketmine\Player;

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
        if($this->getServer()->getPluginManager()->getPlugin("PurePerms") == null){
            $this->getLogger()->critical("Required dependancy 'PurePerms' not installed! Disabling plugin...");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
        foreach ($this->ranks as $rank) {
            $value = explode(":", $rank);
            if(isset($value[3])){
                switch($value[3]){
                    case 'url':
                        break;
                    case 'path':
                        break;
                    default:
                        $value = $this->removeColor(str_replace("&", "§", $value));
                        $this->getLogger()->notice("Invalid image type! Rank: ".TF::YELLOW.$value[0].TF::AQUA." Image type: ".TF::RED.$value[3].TF::AQUA." not supported.");
                        break;
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
                    $sender->sendMessage(TF::RED."This is an in-game command only!");
                }
            }
            else {
                $sender->sendMessage(TF::RED."You don't have permission to switch ranks!");
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
                    $player->sendMessage(TF::GREEN."You selected ".TF::YELLOW.$value[0].TF::RESET.TF::GREEN." as your rank!");
                    $this->getLogger()->info(TF::YELLOW.$name." has changed their group to ".$value[0]);  
                }
                else{
                    $player->sendMessage(TF::RED."You don't have permission to switch to this rank!");
                    return;
                }
            }
            return true;
        });
        $form->setTitle(TF::GOLD."Server Ranks");
        $form->setContent(TF::GREEN."Pick the rank to switch to!");
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
                if($value[3] == "url"){
                    $form->addButton(TF::GRAY.$value[0]." (Locked)", 1, "https://".$value[4]);
                }
                if($value[3] == "path"){
                    $form->addButton(TF::GRAY.$value[0]." (Locked)", 0, $value[4]);
                }
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
