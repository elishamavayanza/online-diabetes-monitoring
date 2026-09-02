<?php

namespace App\Service\Communication;

use App\DTO\Feedback;
use App\Mapper\Communication\MessageAttachmentMapper;
use App\Repository\Communication\MessageAttachmentRepository;
use App\Repository\Communication\MessageRepository;
use App\Entity\Identity\Patient;
use App\Service\File\FileUploaderService;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MessageAttachmentService
{
    public function __construct(
        private readonly MessageAttachmentRepository $repository,
        private readonly MessageRepository $messageRepository,
        private readonly MessageAttachmentMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService,
        private readonly FileUploaderService $fileUploaderService
    ) {}

    public function upload(string $messageId, UploadedFile $file): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::SEND_MESSAGE->value);

            $message = $this->messageRepository->find($messageId);
            if (!$message) {
                return $feedback->setErrorFlushDescription("Message introuvable.")->autoInitFlush();
            }
            $patient = $message->getConversation()?->getPatient();
            if (!$patient instanceof Patient) {
                return $feedback->setErrorFlushDescription("Patient de la conversation introuvable.")->autoInitFlush();
            }
            $this->securityService->checkPatientAccess($patient, SecurityAction::SEND_MESSAGE);

            // Gestion intelligente du dossier : s'il s'agit d'un audio/vocal, on peut le stocker dans 'voices', sinon 'attachments'
            $mimeType = $file->getMimeType() ?? 'application/octet-stream';
            $sizeBytes = $file->getSize();
            $subFolder = str_starts_with($mimeType, 'audio/') ? 'voices' : 'attachments';

            // Utilisation centralisée de FileUploaderService
            $newFilename = $this->fileUploaderService->upload($file, $subFolder);

            // Création de l'entité via le mapper
            $attachment = $this->mapper->mapUploadToEntity(
                $message,
                pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                $newFilename,
                $mimeType,
                $sizeBytes ?? 0
            );

            $this->entityManager->persist($attachment);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($attachment))
                ->setFlushDescription("Pièce jointe ajoutée avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function download(string $id): BinaryFileResponse|Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkPermission(SecurityAction::READ_MESSAGE->value);

            $attachment = $this->repository->find($id);
            if (!$attachment) {
                return $feedback->setErrorFlushDescription("Pièce jointe introuvable.")->autoInitFlush();
            }
            $patient = $attachment->getMessage()?->getConversation()?->getPatient();
            if (!$patient instanceof Patient) {
                return $feedback->setErrorFlushDescription("Patient de la conversation introuvable.")->autoInitFlush();
            }
            $this->securityService->checkPatientAccess($patient, SecurityAction::DOWNLOAD_ATTACHMENT);

            // Détermination dynamique du dossier en fonction du type MIME enregistré en base
            $subFolder = str_starts_with((string)$attachment->getMimeType(), 'audio/') ? 'voices' : 'attachments';

            $filePath = $this->fileUploaderService->getTargetDirectory() . '/' . $subFolder . '/' . $attachment->getFileUrl();

            if (!file_exists($filePath)) {
                return $feedback->setErrorFlushDescription("Fichier physique introuvable sur le serveur.")->autoInitFlush();
            }

            return new BinaryFileResponse($filePath);

        } catch (\Exception $e) {
            return $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }
    }
}
