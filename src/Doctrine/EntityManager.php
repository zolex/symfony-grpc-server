<?php

declare(strict_types=1);

namespace App\Doctrine;

use Doctrine\ORM\Decorator\EntityManagerDecorator;

/**
 * Class EntityManager decorates Doctrine's default EntityManager.
 *
 * @package App\Doctrine
 */
class EntityManager extends EntityManagerDecorator
{
    /**
     * Should be called from every gRPC method that tries to access
     * the database as the connection may be lost because we are in
     * a long running worker.
     */
    public function ensureConnection()
    {
        $connection = $this->getConnection();
        try {
            if (!$this->isOpen())
                throw new \Exception;
            $connection->executeQuery($connection->getDatabasePlatform()->getDummySelectSQL());
        } catch (\Exception $e) {
            $connection->close();
            $connection->connect();
        }
    }
}
