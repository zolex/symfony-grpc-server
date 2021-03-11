<?php

declare(strict_types=1);

namespace App\Service;

use Modix\Grpc\Example\ExampleInterface;
use Modix\Grpc\Example\ExampleStatus;
use Modix\Grpc\Example\ToUpperArgs;
use Modix\Grpc\Example\ToUpperResult;
use Psr\Log\LoggerInterface;
use Spiral\GRPC;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class ExampleService
 *
 * @package App\Service
 */
class ExampleService implements ExampleInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
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
}
