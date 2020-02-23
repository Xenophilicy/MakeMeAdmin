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

use pocketmine\command\{PluginCommand,Command,CommandSender,ConsoleCommandSender};
use pocketmine\utils\{TextFormat as TF, config};
use pocketmine\plugin\{Plugin,PluginBase};
use pocketmine\event\Listener;
use pocketmine\Player;

use Xenophilicy\MakeMeAdmin\libs\jojoe77777\FormAPI\SimpleForm;

class MakeMeAdmin extends PluginBase implements Listener{

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $configPath = $this->getDataFolder()."config.yml";
        if(!file_exists($configPath)){
            $this->getLogger()->critical("It appears that this is the first time you are using MakeMeAdmin! This plugin does not function with the default config.yml, so please edit it to your preferred settings before attempting to use it.");
            $this->saveDefaultConfig();
            $config = new Config($configPath, Config::YAML);
            $config->getAll();
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
        $this->saveDefaultConfig();
        $this->config = new Config($configPath, Config::YAML);
        $this->config->getAll();
        $version = $this->config->get("VERSION");
        $this->pluginVersion = $this->getDescription()->getVersion();
        if($version < "1.6.0"){
            $this->getLogger()->warning("You have updated MakeMeAdmin to v".$this->pluginVersion." but have a config from v$version! Please delete your old config for new features to be enabled and to prevent unwanted errors! Plugin will remain disabled...");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
        foreach($this->ranks = $this->config->get("Ranks") as $rank){
            $values = explode(":", $rank);
            if(isset($values[3])){
                switch($values[3]){
                    case 'url' || 'path':
                        break;
                    default:
                        $this->getLogger()->warning("One of the ranks you have added has an invalid image type. Rank: ".$values[0].TF::RESET." Image type: ".$values[3]." not supported.");
                }
            }
        }
        $this->cmdName = str_replace("/","",$this->config->getNested("Command.Prefix"));
        if($this->cmdName == null || $this->cmdName == ""){
            $this->getLogger()->critical("Invalid command prefix found, disabling plugin...");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        } else{
            $cmd = new PluginCommand($this->cmdName , $this);
            $cmd->setDescription($this->config->getNested("Command.Description"));
            if($this->config->getNested("Command.Permission.Require")){
                $cmd->setPermission($this->config->getNested("Command.Permission.Node"));
            }
            $this->getServer()->getCommandMap()->register("makemeadmin", $cmd, $this->cmdName);
        }
        if($manager = $this->config->getNested("Group.Manager") == "PurePerms"){
            if($this->loadPurePerms()){
                $this->ppEnabled = true;
                $this->purePerms = $this->getServer()->getPluginManager()->getPlugin("PurePerms");
                if(($result = $this->verifyGroups($this->ranks)) === true){
                    $this->getLogger()->info("Successfully verified ranks and PurePerms installation.");
                } else{
                    $this->getLogger()->warning("One of the ranks is not valid. Invalid rank: ".$result);
                    $this->getServer()->getPluginManager()->disablePlugin($this);
                    return;
                }
            } else{
                $this->getLogger()->warning("You have selected PurePerms as your group manager but the plugin did not load correctly. Please verify PurePerms is installed and reload the server. Plugin will remain disabled...");
                $this->getServer()->getPluginManager()->disablePlugin($this);
                return;
            }
        } else{
            $this->ppEnabled = false;
            $this->getLogger()->notice("Your group manager $manager, is not fully supported. For best compatibility, please use PurePerms instead.");
        }
        $this->lang = $this->config->get("Messages");
    }

    private function verifyGroups(array $ranks){
        foreach($ranks as $rank){
            $values = explode(":", $rank);
            $group = $this->purePerms->getGroup($values[1]);
            if($group === null || $group === ""){
                return $values[1];
            }
        }
        return true;
    }

    private function getData(){
        return $this->purePerms->getUserDataMgr();
    }

    private function loadPurePerms() : bool{
        if($plugin = $this->getServer()->getPluginManager()->getPlugin("PurePerms") instanceof Plugin){
            $this->purePerms = $plugin;
            return true;
        }
        return false;
    }

    private function hasPurePermission(Player $player, $perm) : bool{
        $group = $this->getData()->getGroup($player);
        $perms = array_merge($this->getData()->getUserPermissions($player),$group->getGroupPermissions());
        foreach($perms as $purePerm){
            if($perm == $purePerm){
                return true;
            }
        }
        return false;
    }

    private function hasPerms($player, $perm) : bool{
        if($player->isOp()){
            if($this->config->get("OP-Bypass")){
                return true;
            } elseif($this->hasPurePermission($player, $perm)){
                return true;
            } else{
                return false;
            }
        } elseif($this->hasPurePermission($player, $perm)){
            return true;
        } else{
            return false;
        }
    }

    private function replacePlaceholders(string $source, string $group, string $name = ""){
        $source = str_replace("&", "§", $source);
        $source = str_replace("{player}", $name, $source);
        $source = str_replace("{group}", $group, $source);
        return $source;
    }

    private function setPlayerGroup(Player $player, array $values){
        $values = str_replace("&", "§", $values);
        $name = $player->getName();
        if($this->hasPerms($player, $values[2])){
            if($this->ppEnabled){
                $group = $this->purePerms->getGroup($values[1]);
                if($this->getData()->getGroup($player) === $group){
                    $player->sendMessage($this->replacePlaceholders($this->lang["Current-Rank"], $values[0]));
                    return;
                }
                $this->purePerms->setGroup($player, $group);
            } else{
                $cmdString = $this->replacePlaceholders($this->config->getNested("Group.Command-String"), $values[0], $name);
                $consolecmd = new ConsoleCommandSender();
                $this->getServer()->getCommandMap()->dispatch($consolecmd, $cmdString);
            }
            $playerString = $this->replacePlaceholders($this->lang["Player-Report"], $values[0]);
            $player->sendMessage($playerString);
            if($this->config->get("Report-Rank-Change")){
                $consoleString = $this->replacePlaceholders($this->lang["Console-Report"], $values[0], $name);
                $this->getLogger()->info($consoleString);
            }
        } else{
            $player->sendMessage($this->replacePlaceholders($this->lang["No-Rank"], $values[0]));
        }
        return;
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if ($command->getName() == "makemeadmin"){
            $sender->sendMessage(TF::GRAY."---".TF::GOLD." MakeMeAdmin ".TF::GRAY."---");
            $sender->sendMessage(TF::YELLOW."Version: ".TF::AQUA.$this->pluginVersion);
            $sender->sendMessage(TF::YELLOW."Description: ".TF::AQUA."Quickly switch your PurePerms group with a UI");
            $sender->sendMessage(TF::YELLOW."Command: ".TF::BLUE."/".$this->cmdName);
            $sender->sendMessage(TF::YELLOW."Ranks: ");
            foreach($this->ranks as $rank){
                $values = explode(":", $rank);
                $values = str_replace("&", "§", $values);
                $sender->sendMessage(TF::LIGHT_PURPLE ." - ".TF::GREEN.$values[0].TF::RESET.TF::GRAY." (Alias: ".$values[1].")");
            }
            $sender->sendMessage(TF::GRAY."-------------------");
        } elseif($command->getName() == $this->cmdName){
            if($sender instanceof Player){
                $canSwitch = false;
                foreach($this->ranks as $rank){
                    $values = explode(":", $rank);
                    if($this->hasPerms($sender, $values[2])){
                        $canSwitch = true;
                    }
                }
                if($canSwitch == false){
                    $sender->sendMessage($this->lang["No-Switch"]);
                } else{
                    if($args == null){
                        $this->rankOptions($sender);
                    } else{
                        foreach($this->ranks as $rank){
                            $values = explode(":", $rank);
                            if($this->purePerms->getGroup($values[1])->getAlias() == $args[0]){
                                $this->setPlayerGroup($sender, $values);
                                return true;
                            }
                        }
                        $sender->sendMessage($this->replacePlaceholders($this->lang["No-Exists"], $args[0]));
                    }
                }
            } else{
                $sender->sendMessage(TF::RED."This is an in-game command only!");
            }
        }
        return true;
    }

    public function rankOptions($player){
        $form = new SimpleForm(function (Player $player, $data){
            if ($data === null){
                return;
            } else{
                $values = explode(":", $this->ranks[$data]);
                $this->setPlayerGroup($player, $values);
            }
            return;
        });
        $form->setTitle($this->config->getNested("UI.Title"));
        $form->setContent($this->config->getNested("UI.Message"));
        foreach ($this->ranks as $rank) {
            $values = explode(":", $rank);
            $values = str_replace("&", "§", $values);
            if($this->hasPerms($player, $values[2])){
                if(isset($values[3])){
                    if($values[3] == "url"){
                        $form->addButton($values[0], 1, "https://".$values[4]);
                    }
                    if($values[3] == "path"){
                        $form->addButton($values[0], 0, $values[4]);
                    }
                } else{
                    $form->addButton($values[0]);
                }
            } else{
                $values[0] = TF::clean($values[0]);
                $form->addButton(TF::RESET.TF::DARK_GRAY.$values[0]." (Locked)");
            }
        }
        $form->sendToPlayer($player);
    }
}
