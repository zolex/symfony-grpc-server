<?php

declare(strict_types=1);

namespace App\Service;

use Modix\Grpc\Other\MultiplyArgs;
use Modix\Grpc\Other\MultiplyResult;
use Modix\Grpc\Other\OtherInterface;
use Spiral\GRPC;

/**
 * Class OtherService
 *
 * @package App\Service
 */
class OtherService implements OtherInterface
{
    public function multiply(GRPC\ContextInterface $ctx, MultiplyArgs $in): MultiplyResult
    {
        return (new MultiplyResult)->setResult($in->getA() * $in->getB());
    }
}