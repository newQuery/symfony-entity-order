<?php


namespace newQuery\Bundle\EntityOrder\Lib;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait OrderEntity
 * On create make sure to call $orderPositionHelper->setDefaultPosition($entity);
 * Otherwise the default position will always be 0 and will cause conflicts
 * @package newQuery\Lib
 * @author newQuery
 */
trait OrderEntity
{
    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $positionOrder;

    /**
     * @return mixed
     */
    public function getPositionOrder()
    {
        return $this->positionOrder;
    }

    /**
     * @param mixed $positionOrder
     * @return OrderEntity
     */
    public function setPositionOrder($positionOrder)
    {
        $this->positionOrder = $positionOrder;
        return $this;
    }
}