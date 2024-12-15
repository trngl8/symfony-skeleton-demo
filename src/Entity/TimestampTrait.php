<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

trait TimestampTrait
{
    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $startedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $closedAt;

    public function setCreatedAt(\DateTimeInterface $date): self
    {
        if($this->createdAt && $this->createdAt < $date) {
            //throw new \RuntimeException("Impossible to set created date in the future");
        }

        $this->createdAt = $date;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getStartedAt(): ?\DateTime
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTime $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getClosedAt(): ?\DateTime
    {
        return $this->closedAt;
    }

    public function setClosedAt(?\DateTime $closedAt): self
    {
        $this->closedAt = $closedAt;

        return $this;
    }
}
