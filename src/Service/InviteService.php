<?php

namespace App\Service;

use App\Entity\Invite;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class InviteService
{
    private string $baseDql = "SELECT i FROM App\Entity\Invite i";

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
                    $this->baseDql .= sprintf(" WHERE i.type='%s'", $value);
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

    public function close(Invite $invite) : bool
    {
        $invite->setClosedAt(new \DateTime());
        $this->doctrine->getManager()->flush();

        return true;
    }

    public function export() : array
    {
        return $this->query->getArrayResult();
    }
}
