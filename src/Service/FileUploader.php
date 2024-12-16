<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    public function __construct(
        private readonly string $uploadDirectory,
        private readonly SluggerInterface $slugger,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = sprintf("%s-%s.%s", $safeFilename, uniqid(), $file->guessExtension());

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            $this->logger->error($e->getMessage());
            throw $e;
        }

        return $fileName;
    }

    public function getTargetDirectory(): string
    {
        return $this->uploadDirectory;
    }
}
