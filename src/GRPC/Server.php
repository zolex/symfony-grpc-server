<?php

declare(strict_types=1);

namespace App\GRPC;

use Spiral\GRPC\Server as BaseServer;
use Spiral\GRPC\ServiceInterface;
use Spiral\RoadRunner\Worker;

/**
 * Class Server
 *
 * @package App\GRPC
 */
class Server
{
    private BaseServer $server;
    private Worker $worker;

    /**
     * @var ServiceInterface[]
     */
    private iterable $services;

    /**
     * Server constructor.
     *
     * @param BaseServer $server
     * @param Worker $worker
     * @param ServiceInterface[] $services
     */
    public function __construct(BaseServer $server, Worker $worker, iterable $services)
    {
        $this->server = $server;
        $this->worker = $worker;
        $this->services = $services;
    }

    /**
     * @throws \ReflectionException
     */
    private function registerServices()
    {
        foreach ($this->services as $service) {
            $reflection = new \ReflectionClass($service);
            $interfaces = $reflection->getInterfaceNames();
            foreach ($interfaces as $interface) {
                if (str_contains($interface, 'Modix\Grpc\Service')) {
                    $this->server->registerService($interface, $service);
                    break;
                }
            }
        }
    }

    /**
     * @throws \ReflectionException
     */
    public function serve()
    {
        $this->registerServices();
        $this->server->serve($this->worker);
    }
}
