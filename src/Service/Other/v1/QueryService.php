<?php

declare(strict_types=1);

namespace App\Service\Other\v1;

use Modix\Grpc\Service\Other\v1\Model\MultiplyArgs;
use Modix\Grpc\Service\Other\v1\Model\MultiplyResult;
use Modix\Grpc\Service\Other\v1\QueryInterface;
use Spiral\GRPC;

/**
 * Class OtherService
 *
 * @package App\Service
 */
class QueryService implements QueryInterface
{
    public function multiply(GRPC\ContextInterface $ctx, MultiplyArgs $in): MultiplyResult
    {
        return (new MultiplyResult)->setResult($in->getA() * $in->getB());
    }
}