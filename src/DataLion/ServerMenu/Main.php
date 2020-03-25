<?php

declare(strict_types=1);

namespace DataLion\ServerMenu;






use jojoe77777\FormAPI\SimpleForm;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\Config;

class Main extends PluginBase{

    	private $config;
	public static $instance;
	
	public static function getInstance(): Main
	{
		return self::$instance;
	}
	
	

	public function onEnable() : void{
		
	
		
		$this->getLogger()->info("Enabled");


		$standard_vals = [
		    "servers" => [
		        [
		            "name" => "LocalHost",
                    "ip" => "127.0.0.1",
                    "port" => "19132"
                ],
                [
                    "name" => "Example Server",
                    "ip" => "play.example.com",
                    "port" => "19132"
                ],
            ],
        ];



		$this->config = new Config($this->getDataFolder()."config.yml", Config::YAML, $standard_vals);
		self::$instance = $this;


	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		switch($command->getName()){
			case "servermenu":
			    if($sender instanceof Player){
                    $this->serverMenu($sender);
                }


				return true;
			default:
				return false;
		}
	}






	public function onDisable() : void{
		$this->getLogger()->info("Disabled");
	}






	public static function serverMenu(Player $player): void{
        $options = array();
        foreach(self::getInstance()->config->get("servers") as $server){
            $options[] = $server["name"];
        }

        if(sizeof($options) == 0){
            $text = "No servers found";
        }else{
            $text = "Select Server";
        }
        $form = new SimpleForm(function(Player $player, $data){
            if($data !== null){
                if(isset($data)){
                    if (is_int($data)) {


                        $server = self::getInstance()->config->get("servers")[$data];
                        $player->transfer($server["ip"], intval($server["port"]), $server["name"]);
                        return;


                    }
                }
            }
            return;
        });
        $form->setTitle("Server Selection");
        $form->setContent($text);
        foreach(self::getInstance()->config->get("servers") as $server){
            $form->addButton($server["name"]);
        }
        $player->sendForm($form);
        return;
    }
}
