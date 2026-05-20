<?php
namespace Inter\InterFunItems\Items;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\BlazeShootSound;

class dezor implements Listener
{
    private array $cooldown = [];
    private int $cooldowntimer = 30;
    public function PlayerItemUseDezor(PlayerItemUseEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $playername = $player->getName();

        if ($item->getNamedTag()->getString("dezor", "") === "true") {
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
                        $slowness = new EffectInstance(VanillaEffects::SLOWNESS(), 8 * 20, 4, true);
                        $slepota = new EffectInstance(VanillaEffects::DARKNESS(), 6 * 20, 0, true);
                        $nausea = new EffectInstance(VanillaEffects::NAUSEA(), 6 * 20, 0, true);
                        $issusenie = new EffectInstance(VanillaEffects::WITHER(), 13 * 20, 2, true);
                        $utomlenie = new EffectInstance(VanillaEffects::MINING_FATIGUE(), 8 * 20, 4, true);
                    } elseif ($radius <= 3) {
                        $slowness = new EffectInstance(VanillaEffects::SLOWNESS(), 7 * 20, 4, true);
                        $slepota = new EffectInstance(VanillaEffects::DARKNESS(), 5 * 20, 0, true);
                        $nausea = new EffectInstance(VanillaEffects::NAUSEA(), 5 * 20, 0, true);
                        $issusenie = new EffectInstance(VanillaEffects::WITHER(), 12 * 20, 2, true);
                        $utomlenie = new EffectInstance(VanillaEffects::MINING_FATIGUE(), 7 * 20, 4, true);
                    } elseif ($radius <= 5) {
                        $slowness = new EffectInstance(VanillaEffects::SLOWNESS(), 6 * 20, 4, true);
                        $slepota = new EffectInstance(VanillaEffects::DARKNESS(), 4 * 20, 0, true);
                        $nausea = new EffectInstance(VanillaEffects::NAUSEA(), 4 * 20, 0, true);
                        $issusenie = new EffectInstance(VanillaEffects::WITHER(), 11 * 20, 2, true);
                        $utomlenie = new EffectInstance(VanillaEffects::MINING_FATIGUE(), 6 * 20, 4, true);
                    } else {
                        $slowness = new EffectInstance(VanillaEffects::SLOWNESS(), 5 * 20, 4, true);
                        $slepota = new EffectInstance(VanillaEffects::DARKNESS(), 3 * 20, 0, true);
                        $nausea = new EffectInstance(VanillaEffects::NAUSEA(), 3 * 20, 0, true);
                        $issusenie = new EffectInstance(VanillaEffects::WITHER(), 10 * 20, 2, true);
                        $utomlenie = new EffectInstance(VanillaEffects::MINING_FATIGUE(), 5 * 20, 4, true);
                    }

                    $entity->getEffects()->add($slowness);
                    $entity->getEffects()->add($nausea);
                    $entity->getEffects()->add($slepota);
                    $entity->getEffects()->add($issusenie);
                    $entity->getEffects()->add($utomlenie);
                    $entity->sendMessage(TextFormat::RED . "Вас дезориентировал " . $playername);
                }
            }

            $this->cooldown[$playername] = time() + $this->cooldowntimer;

            $item->setCount($item->getCount() - 1);
            $player->getInventory()->setItemInHand($item);
            $player->sendMessage(TextFormat::GREEN . "Ты дезориентировал всех вокруг!");
            $player->broadcastSound(new BlazeShootSound());
        }
    }
}