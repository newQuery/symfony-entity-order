<?php

namespace newQuery\Helper;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Class ClassReflectionHelper
 * @package newQuery\Helper
 * @author newQuery
 */
class ClassReflectionHelper
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getEntities()
    {
        $meta = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $entities = [];
        foreach ($meta as $m) {
            $entities[] = $m->getName();
        }

        return $entities;
    }

    public function getNamespaceForEntity(string $className)
    {
        $meta = $this->entityManager->getMetadataFactory()->getAllMetadata();

        foreach ($meta as $m) {
            if(strpos($m->getName(), 'App\Entity') !== false && strpos($m->getName(), $className) !== false)
            {
                return $m->getName();
            }
        }

        return false;
    }

    public function getRepositoryForEntity(string $className)
    {
        $meta = $this->entityManager->getMetadataFactory()->getAllMetadata();

        foreach ($meta as $m) {
            if(strpos($m->getName(), 'App\Entity') !== false && strpos($m->getName(), $className) !== false)
            {
                return $this->entityManager->getRepository($m->getName());
            }
        }

        return false;
    }

    public function getEntitiesUsingTrait($trait)
    {
        $meta = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $entities = [];
        foreach ($meta as $m) {
            if(in_array(
                $trait,
                class_uses($m->getName()))
            ) {
                $entities[] = $m->getName();
            }
        }

        return $entities;
    }
}