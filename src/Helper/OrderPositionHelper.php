<?php

namespace newQuery\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use function Symfony\Component\String\u;

/**
 * Class OrderPositionHelper
 * @package newQuery\Helper
 * @author newQuery
 */
class OrderPositionHelper
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $managerRegistry;

    public function __construct(EntityManagerInterface $entityManager, ManagerRegistry $managerRegistry)
    {
        $this->entityManager = $entityManager;
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @param ObjectRepository $repository
     * @param int $id
     * @return bool
     */
    public function increase(ObjectRepository $repository, int $id): bool
    {
        $entity = $repository->find($id);
        $greaterEntity = $repository->findOneBy(['positionOrder' => ($entity->getPositionOrder() + 1)]);

        if(null !== $greaterEntity) {
            $entity->setPositionOrder($entity->getPositionOrder() + 1);
            $greaterEntity->setPositionOrder($greaterEntity->getPositionOrder() - 1);

            return true;
        }

        return false;
    }

    /**
     * Decrease OrderPosition of given entity
     * @param ObjectRepository $repository
     * @param int $id
     * @return bool
     */
    public function decrease(ObjectRepository $repository, int $id): bool
    {
        $entity = $repository->find($id);
        $lowerEntity = $repository->findOneBy(['positionOrder' => ($entity->getPositionOrder() - 1)]);

        if(null !== $lowerEntity) {
            $entity->setPositionOrder($entity->getPositionOrder() - 1);
            $lowerEntity->setPositionOrder($lowerEntity->getPositionOrder() + 1);

            return true;
        }

        return false;
    }


    /**
     * Set the position to the MAX() + 1
     * @param object $entity
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function setDefaultPosition(object $entity): void
    {
        $table_name = u(substr(strrchr(get_class($entity), "\\"), 1))->snake();

        $sql = sprintf("select max(position_order) from %s", $table_name);
        $conn = $this->entityManager->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery();

        $entity->setPositionOrder($stmt->fetchOne() + 1);
    }

    /**
     * Reset the OrderPosition of all elements from the given entity
     * @param ObjectRepository $repository
     */
    public function fixOrder(ObjectRepository $repository): void
    {
        $elements = $repository->findAll();

        $position = 0;
        foreach ($elements as $element) {
            $element->setPositionOrder($position);
            $position++;
        }

        $this->entityManager->flush();
    }
}