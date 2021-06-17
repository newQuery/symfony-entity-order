<?php

namespace newQuery\Command;

use newQuery\Lib\OrderEntity;
use newQuery\Helper\ClassReflectionHelper;
use newQuery\Helper\OrderPositionHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FixOrderPosition
 * @package newQuery\Command
 * @author newQuery
 */
class FixOrderPosition extends Command
{
    protected static $defaultName = 'nq:position-order:fix';

    /**
     * @var ClassReflectionHelper
     */

    private ClassReflectionHelper $classReflectionHelper;

    /**
     * @var OrderPositionHelper
     */
    private OrderPositionHelper $orderPositionHelper;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    public function __construct(ClassReflectionHelper $classReflectionHelper, OrderPositionHelper $orderPositionHelper, EntityManagerInterface $entityManager)
    {
        parent::__construct(self::$defaultName);
        $this->classReflectionHelper = $classReflectionHelper;
        $this->orderPositionHelper = $orderPositionHelper;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Reset position order of all elements from given entity')
            ->addArgument('entity', InputArgument::OPTIONAL, 'Entity name. If no given, will reset every entity using the OrderEntity trait')
            ->setHelp('If you do not provide any Entities as arguments, it will reset the OrderPosition for ALL entities using the OrderEntity trait')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entityName = $input->getArgument('entity');

        if(null !== $entityName) {
            $output->writeln(sprintf('Fixing order for entity: %s', $entityName));
            $this->orderPositionHelper->fixOrder($this->classReflectionHelper->getRepositoryForEntity($entityName));

            $output->writeln('Success');
        } else {
            $output->writeln('Fixing order for all entity using OrderEntity trait');
            $classes = $this->classReflectionHelper->getEntitiesUsingTrait(OrderEntity::class);

            foreach ($classes as $class) {
                $this->orderPositionHelper->fixOrder($this->entityManager->getRepository($class));
                $output->writeln(sprintf('Success fixing order for entity: %s', $class));
            }

            $output->writeln('Success');
        }

        return Command::SUCCESS;
    }
}