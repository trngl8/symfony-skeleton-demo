<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'app_orders')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid')]
    private $uuid;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?Offer $offer = null;

    #[ORM\Column(length: 32)]
    private ?string $action = null;

    #[ORM\Column]
    private ?int $amount = null;

    #[ORM\Column(length: 32)]
    private ?string $currency = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 32)]
    private ?string $status = 'new';

    #[ORM\Column(length: 255)]
    private string $deliveryEmail;

    #[ORM\Column(length: 255)]
    private ?string $deliveryPhone;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $deliveryName = null;

    public function __construct(?Uuid $uuid = null)
    {
        $this->uuid = $uuid ?? Uuid::v4();
        $this->action = 'pay';
    }


    public function __toString(): string
    {
       return sprintf("%d. %s [%s]", $this->id, $this->description, $this->status);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOffer(): ?Offer
    {
        return $this->offer;
    }

    public function setOffer(?Offer $offer): self
    {
        $this->offer = $offer;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function setDeliveryEmail(string $deliveryEmail): self
    {
        $this->deliveryEmail = $deliveryEmail;

        return $this;
    }

    public function setDeliveryPhone(string $deliveryPhone): self
    {
        $this->deliveryPhone = $deliveryPhone;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getDeliveryEmail(): string
    {
        return $this->deliveryEmail;
    }

    public function getDeliveryPhone(): ?string
    {
        return $this->deliveryPhone;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getDeliveryName(): ?string
    {
        return $this->deliveryName;
    }

    public function setDeliveryName(?string $deliveryName): static
    {
        $this->deliveryName = $deliveryName;

        return $this;
    }
}
