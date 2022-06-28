<?php

namespace App\Repository;

use App\Entity\Vehicle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Modix\Grpc\Service\Example\v1\Model\VehicleFilter;

/**
 * @method Vehicle|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vehicle|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vehicle[]    findAll()
 * @method Vehicle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VehicleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicle::class);
    }

    /**
     * @param VehicleFilter $filter
     *
     * @return Vehicle|null
     * @throws NonUniqueResultException
     */
    public function findVehicle(VehicleFilter $filter): ?Vehicle
    {
        return $this->createQueryBuilder('vehicle')
            ->leftJoin('vehicle.dealer', 'dealer')
            ->where('vehicle.id = :id')
            ->setParameter('id', $filter->getId())
            ->getQuery()
            ->disableResultCache()
            ->getOneOrNullResult();
    }
}
