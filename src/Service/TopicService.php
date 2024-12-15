<?php

namespace App\Service;

use App\Entity\Topic;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class TopicService
{
    private string $baseDql = "SELECT t, p FROM App\Entity\Topic t LEFT JOIN t.profile p";

    private $doctrine;

    private $query;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->query = $this->doctrine->getManager()->createQuery($this->baseDql);
    }

    public function addCriteria($data) : self
    {
        if(is_array($data)) {
            foreach ($data as $key => $value) {
                if($key === 'type') {
                    $this->baseDql .= sprintf(" WHERE t.type='%s'", $value);
                }
            }
        }

        return $this;
    }

    public function addOrder($data) : self
    {
        if(is_array($data) && sizeof($data) > 0 && sizeof(array_keys($data)) > 0) {
            $this->baseDql .= ' ORDER BY ';

            foreach ($data as $key => $value) {
                $this->baseDql .= sprintf("t.%s %s, ", $key, $value);
            }

            $this->baseDql = substr($this->baseDql, 0, -2); //cuting space and , here
        }

        return $this;
    }


    public function getPaginator($page, $max) : Paginator
    {
        $this->query = $this->doctrine->getManager()->createQuery($this->baseDql)
            ->setFirstResult(($page-1) * $max)
            ->setMaxResults($max);

        return new Paginator($this->query, true);
    }

    public function close(Topic $topic) : bool
    {
        $topic->setClosedAt(new \DateTime());
        if(null === $topic->getStartedAt()) {
            $topic->setStartedAt($topic->getClosedAt());
        }
        $this->doctrine->getManager()->flush();

        return true;
    }

    public function run(Topic $topic) : bool
    {
        $topic->setStartedAt(new \DateTime());
        $this->doctrine->getManager()->flush();

        return true;
    }

    public function updateWeight(Topic $topic, int $weight) : self
    {
        $topic->setPriority($weight);
        $this->doctrine->getManager()->flush();

        return $this;
    }

    public function export() : array
    {
        return $this->query->getArrayResult();
    }
}
