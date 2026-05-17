<?php

namespace Inter\InterFunItems;

use Inter\InterFunItems\Forms\FormGive;
use Inter\InterFunItems\Items\dezor;
use Inter\InterFunItems\Items\fire;
use Inter\InterFunItems\Items\zamorozka;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener
{
    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents(new dezor(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new fire(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new zamorozka(), $this);
        $this->getLogger()->info(TextFormat::GREEN . "InterFunItems включен!\nПлагин разработан игроком Inter4ik1337\nhttps://github.com/Inter4ik1337");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if ($command->getName() == "funitems") {
            if (!isset($args[0])) {
                if (!$sender instanceof Player) {
                    $sender->sendMessage(TextFormat::RED . "/funitems предмет [ник]\n" . TextFormat::GRAY . "Все предметы: \ndezor,\nfire,\nzamorozka");
                    return true;
                }
                $sender->sendForm(new FormGive());
                return true;
            }
            $count = 1; // по дефолту
            if (isset($args[2]) && is_numeric($args[2])) {
                $count = $args[2];
                if ($count < 1) {
                    $count = 1;
                }
                if ($count > 64) {
                    $count = 64;
                }
            }

            if (isset($args[1])) {
                $target = $sender->getServer()->getPlayerExact($args[1]);
                if ($target === null) {
                    $sender->sendMessage(TextFormat::RED . "Игрок не найден!");
                    return true;
                }
            } else {
                if (!$sender instanceof Player) {
                    $sender->sendMessage(TextFormat::RED . "Консоль должна указать ник!");
                    return true;
                }
                $target = $sender;
            }
            if ($args[0] !== "dezor" && $args[0] !== "zamorozka" && $args[0] !== "fire") {
                $sender->sendMessage(TextFormat::RED . "/funitems предмет [ник]\n" . TextFormat::GRAY . "Все предметы: \ndezor,\nfire,\nzamorozka");
                return true;
            }

            if ($args[0] === "dezor") {
                $dezor = VanillaItems::ENDER_PEARL();
                $dezor->getNamedTag()->setString("dezor", "true");
                $dezor->setCustomName(TextFormat::RESET . TextFormat::GREEN . "[*] Дезориентация");
                $dezor->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 1));
                $dezor->setLore([
                    " ",
                    TextFormat::RESET . TextFormat::DARK_GRAY . "* " .TextFormat::GRAY . "Эффекты для противников: ",
                    TextFormat::RESET . TextFormat::DARK_GRAY . "- " . TextFormat::RED . "Иссушение III (00:10)",
                    TextFormat::RESET . TextFormat::DARK_GRAY . "- " . TextFormat::RED . "Замедление V (00:05)",
                    TextFormat::RESET . TextFormat::DARK_GRAY . "- " . TextFormat::RED . "Утомление V (00:05)",
                    TextFormat::RESET . TextFormat::DARK_GRAY . "- " . TextFormat::RED . "Слепота (00:03)",
                    TextFormat::RESET . TextFormat::DARK_GRAY . "- " . TextFormat::RED . "Тошнота (00:03)"
                ]);
                if ($count > 16) {
                    $count = 16;
                }
                $dezor->setCount($count);
                $target->getInventory()->addItem($dezor);
            }
            if ($args[0] === "zamorozka") {
                $zamorozka = VanillaItems::SNOWBALL();
                $zamorozka->getNamedTag()->setString("zamorozka", "true");
                $zamorozka->setLore([
                    " ",
                    TextFormat::RESET . TextFormat::DARK_GRAY . "* " .TextFormat::GRAY . "Эффекты для противников: ",
                    TextFormat::RESET . TextFormat::DARK_GRAY . "- " . TextFormat::RED . "Заморозка (00:01)",
                    TextFormat::RESET . TextFormat::DARK_GRAY . "- " . TextFormat::RED . "Слабость (00:05)"
                ]);
                $zamorozka->setCustomName(TextFormat::RESET . TextFormat::DARK_AQUA . "[*] Снежок заморозка");
                $zamorozka->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 1));
                $zamorozka->setCount($count);
                $target->getInventory()->addItem($zamorozka);
            }
            if ($args[0] === "fire") {
                $fire = VanillaItems::FIRE_CHARGE();
                $fire->getNamedTag()->setString("fire", "true");
                $fire->setLore([
                    " ",
                    TextFormat::RESET . TextFormat::DARK_GRAY . "* " .TextFormat::GRAY . "Эффекты для противников: ",
                    TextFormat::RESET . TextFormat::DARK_GRAY . "- " . TextFormat::RED . "Поджог (00:03)"
                ]);
                $fire->setCustomName(TextFormat::RESET . TextFormat::DARK_RED . "[*] Огненный смерч");
                $fire->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 1));
                $fire->setCount($count);
                $target->getInventory()->addItem($fire);
            }
        }
        return true;
    }
}
