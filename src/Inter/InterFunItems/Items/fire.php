<?php
namespace Inter\InterFunItems\Items;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\BlazeShootSound;

class fire implements Listener
{
    private array $cooldown = [];
    private int $cooldowntimer = 30;
    public function PlayerItemUseFire(PlayerItemUseEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $playername = $player->getName();

        if ($item->getNamedTag()->getString("fire", "") === "true") {
            $event->cancel();

            if (isset($this->cooldown[$playername]) && time() < $this->cooldown[$playername]) {
                $remaining = $this->cooldown[$playername] - time();
                $player->sendMessage(TextFormat::RED . "Подождите ещё " . $remaining . " секунд");
                return;
            }

            $world = $player->getWorld();

            foreach ($world->getNearbyEntities($player->getBoundingBox()->expandedCopy(10, 10, 10)) as $entity) {
                if ($entity instanceof Player && $entity->getId() !== $player->getId()) {
                    $radius = $player->getPosition()->distance($entity->getPosition());
                    if ($radius <= 2) {
                        $entity->setOnFire(6);
                    }
                    elseif ($radius <= 3) {
                        $entity->setOnFire(5);
                    }
                    elseif ($radius <= 5) {
                        $entity->setOnFire(4);
                    }
                    else {
                        $entity->setOnFire(3);
                    }
                    $entity->sendMessage(TextFormat::RED . "Вас поджог " . $playername);
                }
            }

            $this->cooldown[$playername] = time() + $this->cooldowntimer;

            $item->setCount($item->getCount() - 1);
            $player->getInventory()->setItemInHand($item);
            $player->sendMessage(TextFormat::GREEN . "Ты поджог всех вокруг!");
            $player->broadcastSound(new BlazeShootSound());
        }
    }

    public function FireUseOnBlock(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $playername = $player->getName();

        if ($item->getNamedTag()->getString("fire", "") === "true") {
            $event->cancel();

            if (isset($this->cooldown[$playername]) && time() < $this->cooldown[$playername]) {
                $remaining = $this->cooldown[$playername] - time();
                $player->sendMessage(TextFormat::RED . "Подождите ещё " . $remaining . " секунд");
                return;
            }

            $world = $player->getWorld();

            foreach ($world->getNearbyEntities($player->getBoundingBox()->expandedCopy(10, 10, 10)) as $entity) {
                if ($entity instanceof Player && $entity->getId() !== $player->getId()) {
                    $entity->setOnFire(3);
                    $entity->sendMessage(TextFormat::RED . "Вас поджог " . $playername);
                }
            }

            $this->cooldown[$playername] = time() + $this->cooldowntimer;

            $item->setCount($item->getCount() - 1);
            $player->getInventory()->setItemInHand($item);
            $player->sendMessage(TextFormat::GREEN . "Ты поджог всех вокруг!");
            $player->broadcastSound(new BlazeShootSound());
        }
    }
}