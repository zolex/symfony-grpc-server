<?php

declare(strict_types=1);

namespace App\Service\Example\v1;

use App\Entity\Dealer;
use App\Entity\Vehicle;
use Doctrine\ORM\EntityManagerInterface;
use Modix\Grpc\Service\Example\v1\CommandInterface;
use Modix\Grpc\Service\Example\v1\Model\Vehicle as VehicleMessage;
use Psr\Log\LoggerInterface;
use Spiral\GRPC;

/**
 * Class ExampleService
 *
 * @package App\Service
 */
class ComandService implements CommandInterface
{
    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;

    public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    public function persistVehicle(GRPC\ContextInterface $ctx, VehicleMessage $in): VehicleMessage
    {
        $this->entityManager->ensureConnection();

        $vehicle = new Vehicle();
        $vehicle->setMake($in->getMake());
        $vehicle->setModel($in->getModel());
        $vehicle->setType($in->getType());

        if ($dealer = $this->entityManager->find(Dealer::class, $in->getDealer()))
            $vehicle->setDealer($dealer);

        $this->entityManager->persist($vehicle);
        $this->entityManager->flush();

        $out = new VehicleMessage();
        $out->setId($vehicle->getId());
        $out->setMake($vehicle->getMake());
        $out->setModel($vehicle->getModel());
        $out->setType($vehicle->getType());
        if ($dealer = $vehicle->getDealer())
            $out->setDealer($dealer->getId());

        return $out;
    }
}
