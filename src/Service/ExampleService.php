<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Dealer;
use App\Entity\Vehicle;
use Doctrine\ORM\EntityManagerInterface;
use Modix\Grpc\Example\ExampleInterface;
use Modix\Grpc\Example\ExampleStatus;
use Modix\Grpc\Example\ToUpperArgs;
use Modix\Grpc\Example\ToUpperResult;
use Modix\Grpc\Example\VehicleMessage;
use Psr\Log\LoggerInterface;
use Spiral\GRPC;

/**
 * Class ExampleService
 *
 * @package App\Service
 */
class ExampleService implements ExampleInterface
{
    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;

    public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    public function toUpper(GRPC\ContextInterface $ctx, ToUpperArgs $in): ToUpperResult
    {
        $this->logger->info("to upper was called, yeyyy");

        $string = $in->getString();
        if (empty($string)) {
            throw new GRPC\Exception\ServiceException("The given string must not be empty", ExampleStatus::EMPTY_STRING);
        }

        return (new ToUpperResult)->setString(strtoupper($string));
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
