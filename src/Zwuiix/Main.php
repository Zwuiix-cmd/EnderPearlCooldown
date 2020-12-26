<?php

namespace Zwuiix;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\utils\TextFormat;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDamageByBlockEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\entity\Human;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\entity\{Effect, EffectInstance};
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\Config;
use pocketmine\Server;
use pocketmine\scheduler\Task;
use pocketmine\level\Position;
use pocketmine\level\sound\AnvilBreakSound;
use pocketmine\level\sound\PopSound;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\inventory\CraftingManager;
use pocketmine\inventory\ShapedRecipe;
use pocketmine\nbt\JsonNbtParser;
use pocketmine\timings\Timings;

class Main extends PluginBase implements Listener {

    CONST COOLDOWN = 10;

    /** @var Config $config */
    protected $config;

    public function onEnable() {

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, [
            "Message1" => "§4- §cVous devez attendre §c",
            "Message2" => "§cseconde(s)§c avant de lancer une perle §4-",
        ]);

    }

    public function onInteract(PlayerInteractEvent $event) {

        $config = new Config($this->getDataFolder() . "config.yml", Config::YAML);

        $player = $event->getPlayer();
        $item = $event->getItem();
        $name = $player->getName();

        if ($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_AIR) {

            if ($item->getId() === Item::ENDER_PEARL) {

                if (!isset($this->cooldown[$name])) $this->cooldown[$name] = time();

                if (time() < $this->cooldown[$name]) {
                    if ($event->isCancelled()) return;
                    $event->setCancelled();
                    $second = $this->cooldown[$name] - time();
                    $player->sendTip($config->get("Message1") . $second . $config->get("Message2"));

                }else {

                        $this->cooldown[$name] =  time() + self::COOLDOWN;
                }
            }
        }
    }

    public function convert(string $string, $player, $second): string
    {
        $string = str_replace("{seconde}", $second, $string);
        return $string;
    }
}