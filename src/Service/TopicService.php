<?php

namespace App\Service;

use App\Entity\Topic;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class TopicService
{
    private string $baseDql = "SELECT t FROM App\Entity\Topic t";

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
            //TODO: add other custom filters
            foreach ($data as $key => $value) {
                if($key === 'type') {
                    $this->baseDql .= sprintf(" WHERE t.type='%s'", $value);
                }
            }
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
        //TODO: check ability or permissions
        $topic->setClosedAt(new \DateTime());
        $this->doctrine->getManager()->flush();

        return true;
    }
}
