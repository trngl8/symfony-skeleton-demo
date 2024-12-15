<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class CalendarOrder
{
    #[Assert\NotBlank]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Regex('/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/')]
    public string $phone;

    #[Assert\NotBlank]
    public string $date;
}
