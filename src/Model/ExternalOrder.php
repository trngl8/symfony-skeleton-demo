<?php

namespace App\Model;
use Symfony\Component\Validator\Constraints as Assert;

class ExternalOrder
{
    #[Assert\NotBlank]
    public string $firstname;

    #[Assert\NotBlank]
    public string $lastname;

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    public string $phone;

    #[Assert\NotBlank]
    public string $address;

    #[Assert\NotBlank]
    public int $period;

    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
}
