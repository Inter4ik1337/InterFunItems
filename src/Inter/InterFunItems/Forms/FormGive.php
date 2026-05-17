<?php
namespace Inter\InterFunItems\Forms;
use pocketmine\form\Form;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class FormGive implements Form {
    private array $players = [];

    public function jsonSerialize(): mixed
    {
        $this->players = [];
        foreach (Server::getInstance()->getOnlinePlayers() as $p) {
            $this->players[] = $p->getName();
        }

        $playerList = $this->players;
        if (empty($playerList)) {
            $playerList = ["ошибка"];
        }
        return [
            "type" => "custom_form",
            "title" => "Выбрать предмет из фантайма",
            "content" => [
                [
                    "type" => "dropdown",
                    "text" => "Выбор предмета",
                    "options" => [
                        "Дезориентация",
                        "Огненный смерч",
                        "Снежок заморозка"
                    ],
                    "default" => 0,
                ],
                [
                    "type" => "dropdown",
                    "text" => "Выберите игрока",
                    "options" => $playerList,
                    "default" => 0,
                ],
                [
                    "type" => "input",
                    "text" => "Количество",
                    "placeholder" => "Например 1",
                    "default" => "1",
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if ($data === null) return;
        $predmet = (int)$data[0];
        $players = (int)$data[1];
        $count = (int)$data[2];
        if ($count < 1) {
            $count = 1;
        }
        if ($count > 64) {
            $count = 64;
        }

        if (empty($this->players)) {
            $player->sendMessage("ошибка");
            return;
        }
        $targetName = $this->players[$players];
        $target = Server::getInstance()->getPlayerExact($targetName);
        if ($target === null) {
            $player->sendMessage("ошибка");
            return;
        }
        switch ($predmet) {
            case 0:
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
                break;
                case 1:
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
                    break;
                    case 2:
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
    }
}
