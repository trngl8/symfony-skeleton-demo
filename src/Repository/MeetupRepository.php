<?php

namespace App\Repository;

use App\Entity\Meetup;
use App\Entity\Subscribe;
use App\Model\MeetupRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MeetupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Meetup::class);
    }

    public function add(Meetup $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    public function update(Meetup $meetup, MeetupRequest $request): void
    {
        $meetup->setTitle($request->title);
        $meetup->setPlannedAt($request->plannedDayAt->add(
            new \DateInterval(sprintf('PT%dM', $request->duration))
        ));
    }

    public function save(): void
    {
        $this->getEntityManager()->flush();
    }

    public function get(int $id): ?Meetup
    {
        return $this->find($id);
    }

    public function remove($entity): void
    {
        $this->getEntityManager()->remove($entity);
    }

    public function findCurrent(): iterable
    {
        $meetups = $this->createQueryBuilder('m')
            ->orderBy('m.plannedAt', 'ASC')
            ->getQuery()
            ->getArrayResult();

        // TODO: use join
        foreach ($meetups as &$meetup) {
            $meetup['subscribers'] = $this->getEntityManager()->getRepository(Subscribe::class)->findBy([
                'type' => 'meetup',
                'target' => $meetup['id']
            ]);
        }

        return $meetups;
    }

}
