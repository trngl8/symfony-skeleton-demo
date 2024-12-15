<?php


namespace App\Model;

class Import
{
    private $filename;

    public function getFile()
    {
        return $this->filename;
    }

    public function setFilename(string $filename) : self
    {
        $this->filename = $filename;

        return $this;
    }
}
