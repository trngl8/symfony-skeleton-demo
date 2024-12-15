<?php

namespace App\Repository;

interface RepositoryInterface
{
    public function add(object $entity): void;

    public function save(): void;
}
