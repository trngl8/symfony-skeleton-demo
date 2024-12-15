<?php

namespace App\Entity;

use App\Repository\MeetupRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MeetupRepository::class)]
#[ORM\Table(name: 'app_meetups')]
class Meetup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private $id;

    #[ORM\Column(length: 255)]
    private string $title;
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $plannedAt;

    #[ORM\Column(length: 64)]
    private string $timezone;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    private array $subscribers;

    public function __construct(string $title, \DateTimeInterface $plannedAt)
    {
        $this->title = $title;
        $this->plannedAt = $plannedAt;
        $this->timezone = date_default_timezone_get();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function setPlannedAt(\DateTimeInterface $plannedAt): self
    {
        $this->plannedAt = $plannedAt;
        return $this;
    }

    public function getPlannedAt(): \DateTimeInterface
    {
        return $this->plannedAt;
    }

    public function getDuration(): int
    {
        return 60;
    }
}
