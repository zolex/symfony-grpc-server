<?php

declare(strict_types=1);

namespace App\GRPC;

use Spiral\GRPC\Server as BaseServer;
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
    private iterable $services;

    /**
     * Server constructor.
     *
     * @param BaseServer $server
     * @param Worker $worker
     * @param iterable $services
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
            $this->server->registerService($interfaces[0], $service);
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
