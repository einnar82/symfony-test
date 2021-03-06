<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploaderService
{
    /**
     * @var string
     */
    private string $targetDirectory;

    /**
     * @param $targetDirectory
     */
    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function upload(UploadedFile $file): string
    {
        $filename = md5(uniqid()).'.'.$file->guessExtension();
        try {
            $file->move($this->getTargetDirectory(), $filename);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $filename;
    }

    /**
     * @return string
     */
    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}