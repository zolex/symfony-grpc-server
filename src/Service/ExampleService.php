<?php

declare(strict_types=1);

namespace App\Service;

use Modix\Grpc\Example\ExampleInterface;
use Modix\Grpc\Example\ExampleStatus;
use Modix\Grpc\Example\ToUpperArgs;
use Modix\Grpc\Example\ToUpperResult;
use Spiral\GRPC;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class ExampleService
 *
 * @package App\Service
 */
class ExampleService implements ExampleInterface
{
    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function toUpper(GRPC\ContextInterface $ctx, ToUpperArgs $in): ToUpperResult
    {
        $string = $in->getString();
        if (empty($string)) {
            throw new GRPC\Exception\ServiceException("The given string must not be empty", ExampleStatus::EMPTY_STRING);
        }

        return (new ToUpperResult)->setString(strtoupper($string));
    }
}
