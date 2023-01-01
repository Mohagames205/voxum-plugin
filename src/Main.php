<?php

declare(strict_types=1);

namespace Mohamed205\voxum;

use Mohamed205\voxum\lib\predis\Client;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Internet;

class Main extends PluginBase{

    private Client $client;

    public function onEnable(): void
    {
        $this->client = new Client([
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
        ]);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        switch ($command->getName()) {

            case "trigger":

                for ($x = 0; $x < 1000; $x++)
                    $this->client->publish("coordinate-channel", str_repeat(implode(" ", $args), $x) .  $x);

                $this->client->publish("coordinate-channel", "done");
                return true;

            case "verify":
                $code = $args[0];
                if(!is_numeric($code))
                {
                    $sender->sendMessage("This is not a number!");
                    return true;
                }


                $response = Internet::postURL("http://127.0.0.1:8000/api/verifycode", ["code" => $code, "username" => $sender->getName()]);

                $sender->sendMessage(json_decode($response->getBody(), true)["is_correct"] ? "§aYou have been authenticated successfully!" : "§cThe authentication has failed.");



                return true;

            default:
                return false;


        }
    }


}
