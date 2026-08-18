<?php

namespace App\Service\Common;

use App\DTO\Feedback;
use App\Mapper\Common\FileAttachmentMapper;
use App\Repository\Common\FileAttachmentRepository;
use App\Service\File\FileUploaderService; // <-- Votre service d'upload unique
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Bundle\SecurityBundle\Security; // Pour récupérer l'utilisateur connecté

class FileAttachmentService
{
    public function __construct(
        private readonly FileAttachmentRepository $fileAttachmentRepository,
        private readonly FileAttachmentMapper $fileAttachmentMapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService,
        private readonly FileUploaderService $fileUploaderService,
        private readonly Security $security // Pour récupérer l'ID de l'utilisateur connecté
    ) {}

    public function uploadAndCreate(UploadedFile $file, string $entityType, string $entityId): Feedback
    {
        $feedback = new Feedback();

        try {
            // 1. Vérification des droits de sécurité
            $this->securityService->checkPermission(SecurityAction::UPLOAD_LABORATORY_RESULT->value);

            // 2. Gestion intelligente du sous-dossier (ex: 'voices' pour audio/vocal, 'attachments' pour le reste)
            $mimeType = $file->getMimeType();
            $subFolder = str_starts_with($mimeType, 'audio/') ? 'voices' : 'attachments';

            // 3. Upload physique du fichier via votre service centralisé
            $uniqueFilename = $this->fileUploaderService->upload($file, $subFolder);

            // 4. Récupération de l'utilisateur connecté (si disponible)
            $currentUser = $this->security->getUser();
            $currentUserId = $currentUser ? $currentUser->getId() : null;

            // 5. Mapping et persistance en base de données
            $fileAttachment = $this->fileAttachmentMapper->mapUploadToEntity(
                $file,
                $uniqueFilename,
                $entityType,
                $entityId,
                $currentUserId
            );

            $this->entityManager->persist($fileAttachment);
            $this->entityManager->flush();

            $responseDTO = $this->fileAttachmentMapper->mapEntityToResponse($fileAttachment);

            $feedback->setData($responseDTO)
                ->setFlushDescription("Fichier attaché et enregistré avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())
                ->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur lors de l'enregistrement du fichier : " . $e->getMessage())
                ->autoInitFlush();
        }

        return $feedback;
    }

    public function getById(string $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::DOWNLOAD_ATTACHMENT->value);

            $fileAttachment = $this->fileAttachmentRepository->find($id);

            if (!$fileAttachment) {
                return $feedback->setErrorFlushDescription("Fichier introuvable.")->autoInitFlush();
            }

            $responseDTO = $this->fileAttachmentMapper->mapEntityToResponse($fileAttachment);

            $feedback->setData($responseDTO)
                ->setFlushDescription("Fichier récupéré avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())
                ->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())
                ->autoInitFlush();
        }

        return $feedback;
    }
}
