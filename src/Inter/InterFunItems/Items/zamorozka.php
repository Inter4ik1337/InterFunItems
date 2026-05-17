<?php
namespace Inter\InterFunItems\Items;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\BlazeShootSound;

class zamorozka implements Listener
{
    private array $cooldown = [];
    private int $cooldowntimer = 30;
    public function PlayerItemUseZamorozka(PlayerItemUseEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $playername = $player->getName();

        if ($item->getNamedTag()->getString("zamorozka", "") === "true") {
            $event->cancel();

            if (isset($this->cooldown[$playername]) && time() < $this->cooldown[$playername]) {
                $remaining = $this->cooldown[$playername] - time();
                $player->sendMessage(TextFormat::RED . "Подождите ещё " . $remaining . " секунд");
                return;
            }

            $world = $player->getWorld();

            foreach ($world->getNearbyEntities($player->getBoundingBox()->expandedCopy(7, 7, 7)) as $entity) {
                if ($entity instanceof Player && $entity->getId() !== $player->getId()) {
                    $slowness = new EffectInstance(VanillaEffects::SLOWNESS(), 1 * 20, 4, true);
                    $slabost = new EffectInstance(VanillaEffects::WEAKNESS(), 5 * 20, 0, true);
                    $entity->getEffects()->add($slowness);
                    $entity->getEffects()->add($slabost);
                    $entity->sendMessage(TextFormat::RED . "Вас заморозил " . $playername);
                }
            }

            $this->cooldown[$playername] = time() + $this->cooldowntimer;

            $item->setCount($item->getCount() - 1);
            $player->getInventory()->setItemInHand($item);
            $player->sendMessage(TextFormat::GREEN . "Ты заморозил всех вокруг!");
            $player->broadcastSound(new BlazeShootSound());
        }
    }
}