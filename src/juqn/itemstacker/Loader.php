<?php

declare(strict_types=1);

namespace juqn\itemstacker;

use pocketmine\entity\object\ItemEntity;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\EventPriority;
use pocketmine\plugin\PluginBase;

final class Loader extends PluginBase {

    protected function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvent(EntitySpawnEvent::class, function (EntitySpawnEvent $event): void {
            $entity = $event->getEntity();

            if (!$entity instanceof ItemEntity) {
                return;
            }
            $item = $entity->getItem();
            $entities = $entity->getWorld()->getNearbyEntities($entity->getBoundingBox()->expand(5, 5, 5), $entity);

            foreach ($entities as $en) {
                if (!$en instanceof ItemEntity || $en->isFlaggedForDespawn() || $en->isClosed()) {
                    continue;
                }
                $it = $en->getItem();

                if ($it->getMaxStackSize() > 1 && $it->canStackWith($item)) {
                    $en->flagForDespawn();
                    $item->setCount($it->getCount() + $item->getCount());
                }
            }
        }, EventPriority::NORMAL, $this);
    }
}
