<?php

declare(strict_types=1);

namespace App\Service\Example\v1;

use App\Entity\Dealer;
use App\Entity\Vehicle;
use Doctrine\ORM\EntityManagerInterface;
use Modix\Grpc\Service\Example\v1\CommandInterface;
use Modix\Grpc\Service\Example\v1\Model;
use Psr\Log\LoggerInterface;
use Spiral\GRPC;
use Spiral\RoadRunner\GRPC\ContextInterface;

/**
 * Class ExampleService
 *
 * @package App\Service
 */
class CommandService implements CommandInterface
{
    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;

    public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    public function persistVehicle(ContextInterface $ctx, Model\Vehicle $in): Model\Vehicle
    {
        $vehicle = new Vehicle();
        $vehicle->setMake($in->getMake());
        $vehicle->setModel($in->getModel());
        $vehicle->setType($in->getType());

        if ($dealer = $this->entityManager->find(Dealer::class, $in->getDealer()))
            $vehicle->setDealer($dealer);

        $this->entityManager->persist($vehicle);
        $this->entityManager->flush();

        return $vehicle->toRpcModel();
    }
}
