<?php

declare(strict_types=1);

namespace App\Service\Example\v1;

use Doctrine\ORM\EntityManagerInterface;
use Modix\Grpc\Service\Example\v1\Model\StatusCode;
use Modix\Grpc\Service\Example\v1\Model\ToUpperArgs;
use Modix\Grpc\Service\Example\v1\Model\ToUpperResult;
use Modix\Grpc\Service\Example\v1\QueryInterface;
use Psr\Log\LoggerInterface;
use Spiral\GRPC;

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

    public function toUpper(GRPC\ContextInterface $ctx, ToUpperArgs $in): ToUpperResult
    {
        $this->logger->info("to upper was called, yeyyy");

        $string = $in->getString();
        if (empty($string)) {
            throw new GRPC\Exception\ServiceException("The given string must not be empty", StatusCode::EMPTY_STRING);
        }

        return (new ToUpperResult)->setString(strtoupper($string));
    }
}
