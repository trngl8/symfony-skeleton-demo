<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ChatMessage
{
    #[Assert\NotBlank]
    public int $chatId;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public string $text = '';

    public function __construct(string $chatId, $text = '')
    {
        $this->chatId = $chatId;
        $this->text = $text;
    }
}
