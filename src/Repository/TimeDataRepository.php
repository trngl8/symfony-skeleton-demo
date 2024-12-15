<?php

namespace App\Repository;

use App\Entity\TimeData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<TimeData>
 *
 * @method TimeData|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimeData|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimeData[]    findAll()
 * @method TimeData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimeDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeData::class);
    }

    public function save(TimeData $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TimeData $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
