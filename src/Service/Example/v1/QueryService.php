<?php

declare(strict_types=1);

namespace App\Service\Example\v1;

use App\Entity\Vehicle;
use Doctrine\ORM\EntityManagerInterface;
use Modix\Grpc\Service\Example\v1\Model;
use Modix\Grpc\Service\Example\v1\Model\StatusCode;
use Modix\Grpc\Service\Example\v1\Model\ToUpperArgs;
use Modix\Grpc\Service\Example\v1\Model\ToUpperResult;
use Modix\Grpc\Service\Example\v1\QueryInterface;
use Psr\Log\LoggerInterface;
use Spiral\RoadRunner\GRPC\ContextInterface;
use Spiral\RoadRunner\GRPC\Exception\ServiceException;
use Spiral\RoadRunner\GRPC\StatusCode as BaseStatusCode;

/**
 * Class ExampleService
 *
 * @package App\Service
 */
class QueryService implements QueryInterface
{
    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;

    public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    public function toUpper(ContextInterface $ctx, ToUpperArgs $in): ToUpperResult
    {
        $this->logger->info("to upper was called, yeyyy");

        $string = $in->getString();
        if (empty($string)) {
            throw new ServiceException("The given string must not be empty", StatusCode::EMPTY_STRING);
        }

        return (new ToUpperResult)->setString(strtoupper($string));
    }

    public function findVehicle(ContextInterface $ctx, Model\VehicleFilter $in): Model\Vehicle
    {
        $this->logger->debug(sprintf("findVehicle(%d)", $in->getId()));

        if (!$vehicle = $this->entityManager->getRepository(Vehicle::class)->findVehicle($in))
            throw new ServiceException("Vehicle Not Found", BaseStatusCode::NOT_FOUND);

        return $vehicle->toRpcModel();
    }
}
