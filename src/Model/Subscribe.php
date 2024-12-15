<?php

namespace App\Model;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A registration DTO
 */
class Subscribe
{
    CONST DEFAULT_TYPE = 'email'; //direct

    #[Assert\NotBlank]
    private string $type = self::DEFAULT_TYPE;

    #[Assert\NotBlank]
    public string $name;

    #[Assert\Email]
    #[Assert\NotBlank]
    public string $email;

    #[Assert\NotBlank]
    public string $locale;

    #[Assert\NotBlank]
    public $agree;

    public function getType() : string
    {
        return $this->type;
    }
}
