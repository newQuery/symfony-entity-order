<?php

namespace newQuery\Bundle\EntityOrder\EventListener;

use newQuery\Bundle\EntityOrder\Lib\OrderEntity;
use newQuery\Bundle\EntityOrder\Helper\OrderPositionHelper;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Class OrderPositionListener
 * @author newQuery
 * @package newQuery\EventListener
 */
class OrderPositionListener
{
    /**
     * @var OrderPositionHelper
     */
    private OrderPositionHelper $orderPositionHelper;

    public function __construct(OrderPositionHelper $orderPositionHelper)
    {
        $this->orderPositionHelper = $orderPositionHelper;
    }

    /**
     * Set position to new item.
     * @param LifecycleEventArgs $args
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if(in_array(OrderEntity::class, class_uses(get_class($entity)))) {
            $this->orderPositionHelper->setDefaultPosition($entity);
        }
    }

    /**
     * Set correctly the position
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if(in_array(OrderEntity::class, class_uses(get_class($entity)))) {
            $repository = $args->getEntityManager()->getRepository(get_class($entity));
            foreach ($repository->findAll() as $item) {
                if($item->getPositionOrder() > $entity->getPositionOrder()) {
                    $item->setPositionOrder($item->getPositionOrder() - 1);
                }
            }
        }
    }
}