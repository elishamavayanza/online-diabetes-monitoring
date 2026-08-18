<?php

namespace App\Service\File;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploaderService
{
    private string $targetDirectory;

    public function __construct(
        private readonly SluggerInterface $slugger,
        string $projectDir,
        string $targetDirectory = 'uploads/files'
    ) {
        $this->targetDirectory = $projectDir . '/public/' . $targetDirectory;
    }

    public function upload(UploadedFile $file, ?string $subFolder = null): string
    {
        // Vérification de la validité du fichier uploadé pour éviter les erreurs de "stat failed"
        if (!$file->isValid()) {
            throw new \RuntimeException("Fichier invalide ou corrompu (Code d'erreur PHP : " . $file->getError() . "). Vérifiez la taille maximale autorisée.");
        }

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        $targetDir = $this->targetDirectory;
        if ($subFolder) {
            $targetDir .= '/' . trim($subFolder, '/');
        }

        // S'assurer que le dossier cible existe
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        try {
            $file->move($targetDir, $fileName);
        } catch (FileException $e) {
            throw new \RuntimeException("Erreur lors de l'upload du fichier : " . $e->getMessage());
        }

        return $fileName;
    }

    public function remove(string $fileName, ?string $subFolder = null): bool
    {
        $targetDir = $this->targetDirectory;
        if ($subFolder) {
            $targetDir .= '/' . trim($subFolder, '/');
        }

        $filePath = $targetDir . '/' . $fileName;

        if (file_exists($filePath)) {
            return unlink($filePath);
        }

        return false;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
